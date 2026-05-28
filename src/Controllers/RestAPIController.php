<?php

namespace YDPL\Controllers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;
use WP_REST_Request;
use WP_REST_Response;
use YDPL\Exceptions\ObjectNotFoundException;
use YDPL\Services\TranslationService;
use YDPL\Singletons\SiteOptionsSingleton;
use YDPL\Traits\ErrorLog;

/**
 * @since 0.0.1
 */
class RestAPIController
{
	protected const RATE_LIMIT                        = 3;
	protected const RATE_LIMIT_TIME_WINDOW_IN_SECONDS = 60;

	use ErrorLog;

	protected TranslationService $service;
	protected SiteOptionsSingleton $options;

	public function __construct()
	{
		$this->service = new TranslationService();
		$this->options = ydpl_resolve_from_container( 'ydpl.site_options' );
	}

	/**
	 * @since 0.0.1
	 */
	public function handle_translate_request( WP_REST_Request $request ): WP_REST_Response
	{
		$text        = (array) ( $request->get_param( 'text' ) ?? array() );
		$target_lang = (string) ( $request->get_param( 'target_lang' ) ?? '' );
		$object_id   = (int) ( $request->get_param( 'object_id' ) ?? 0 );
		$origin      = (string) ( $request->get_header( 'origin' ) ?? '' );

		if ( 0 < strlen( $origin ) && ! $this->is_same_origin( $origin ) ) {
			return $this->set_failure_response( 403, 'Invalid origin. Origin does not match the site URL.' );
		}

		$user_has_cache_capability = current_user_can( apply_filters( 'yard::deepl/cache_capability', 'edit_posts' ) );

		if ( 0 < $object_id ) {
			try {
				$cached_translation = $this->service->get_cached_translation( $object_id, $target_lang ) ?? array();
			} catch ( ObjectNotFoundException $e ) {
				return $this->set_failure_response( 404, 'Object not found.' );
			}
		} else {
			$cached_translation = null;
		}

		// Apply rate limit check if object ID is absent or translation is not cached when an object ID is present.
		if ( ! $cached_translation ) {
			if ( $this->is_rate_limit_exceeded() && ! $user_has_cache_capability ) {
				return $this->set_failure_response( 429, 'Rate limit exceeded.' );
			}
		}

		try {
			$translation = $this->service->handle_translation( $object_id, $text, $target_lang, $user_has_cache_capability, $cached_translation );

			if ( array() === $translation ) {
				throw new Exception( 'Failed to translate text.', 500 );
			}
		} catch ( Exception $e ) {
			$this->logError( $e->getMessage() );

			return $this->set_failure_response( $e->getCode() ?: 500, 'An error occurred while processing the translation.' );
		}

		return new WP_REST_Response(
			$translation
		);
	}

	/**
	 * @since NEXT
	 */
	protected function is_rate_limit_exceeded(): bool
	{
		$client_ip = $this->get_client_ip();

		if ( '' === $client_ip ) {
			return true;
		}

		$transient_key = 'ydpl_rate_limit_' . hash_hmac( 'sha256', $client_ip, SECURE_AUTH_KEY );
		$request_count = (int) ( get_transient( $transient_key ) ?: 0 );

		if ( self::RATE_LIMIT <= $request_count ) {
			return true;
		}

		set_transient( $transient_key, $request_count + 1, self::RATE_LIMIT_TIME_WINDOW_IN_SECONDS );

		return false;
	}

	/**
	 * @since NEXT
	 */
	protected function get_client_ip(): string
	{
		$remote_address = $_SERVER['REMOTE_ADDR'] ?? '';

		if ( filter_var( $remote_address, FILTER_VALIDATE_IP ) === false ) {
			return '';
		}

		return $remote_address;
	}

	/**
	 * @since NEXT
	 */
	protected function is_same_origin( string $origin ): bool
	{
		$home   = wp_parse_url( home_url() );
		$parsed = wp_parse_url( $origin );

		if ( ! $home || ! $parsed ) {
			return false;
		}

		$home_scheme   = $home['scheme'] ?? '';
		$parsed_scheme = $parsed['scheme'] ?? '';

		return $home_scheme === $parsed_scheme
			&& ( $home['host'] ?? '' ) === ( $parsed['host'] ?? '' )
			&& $this->normalize_port( $home['port'] ?? null, $home_scheme ) === $this->normalize_port( $parsed['port'] ?? null, $parsed_scheme );
	}

	/**
	 * @since NEXT
	 */
	protected function normalize_port( ?int $port, string $scheme ): int
	{
		if ( null !== $port ) {
			return $port;
		}

		return 'https' === $scheme ? 443 : 80;
	}

	/**
	 * @since 0.0.1
	 */
	protected function set_failure_response( int $status, string $message ): WP_REST_Response
	{
		return new WP_REST_Response(
			array(
				'status'  => $status,
				'message' => $message,
			),
			$status
		);
	}
}
