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
use YDPL\Support\LanguageCode;
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

		/**
		 * Request value first, then the site locale, then DeepL's own
		 * auto-detect, which an empty string stands for here.
		 *
		 * The request value has already been normalised by the REST sanitize
		 * callback, but it is normalised and checked again: the server is the
		 * authority on the source language and must not assume the callback
		 * ran. A step is dropped unless DeepL really supports the code, so a
		 * well formed but unsupported tag such as 'fy' — or a three-letter
		 * locale such as 'nds_NL', which normalises to nothing — falls through
		 * instead of earning an HTTP 400 or being mistranslated as Dutch.
		 *
		 * Dutch sites are unaffected: 'nl-NL' from the document and 'nl_NL'
		 * from get_locale() both resolve to the supported code 'NL'.
		 *
		 * @since 2.2.0
		 */
		$source_lang = LanguageCode::to_source_language( (string) ( $request->get_param( 'source_lang' ) ?? '' ) );

		if ( '' === $source_lang ) {
			$source_lang = LanguageCode::to_source_language( get_locale() );
		}

		if ( 0 < strlen( $origin ) && ! $this->is_same_origin( $origin ) ) {
			return $this->set_failure_response( 403, 'Invalid origin. Origin does not match the site URL.' );
		}

		$user_has_cache_capability = current_user_can( apply_filters( 'yard::deepl/cache_capability', 'edit_posts' ) );

		/**
		 * Whether this request will actually reach DeepL, which is what the rate
		 * limiter is there to meter.
		 *
		 * A cache entry merely existing no longer answers that question. A partial
		 * entry now sends the strings it is missing to DeepL and merges the result
		 * back, so gating the limiter on `! $cached_translation` let anyone pair a
		 * warm object ID with text of their own choosing: every string missed,
		 * every string went to the API, and the limiter was never consulted.
		 *
		 * The gate is therefore the very set of strings the service will send,
		 * decided by the service's own helper on the same cache entry the service
		 * is handed below, so the two cannot drift apart. Without an object ID
		 * there is no entry to consult and the whole request goes to the API.
		 *
		 * @since 2.2.0
		 */
		if ( 0 < $object_id ) {
			try {
				$cached_translation = $this->service->get_cached_translation( $object_id, $target_lang, $source_lang ) ?? array();
			} catch ( ObjectNotFoundException $e ) {
				return $this->set_failure_response( 404, 'Object not found.' );
			}

			$request_reaches_api = array() !== TranslationService::untranslated_text( $cached_translation, $text );
		} else {
			$cached_translation  = null;
			$request_reaches_api = true;
		}

		// Apply the rate limit to every request that costs an API call, and only to those. A full cache hit is free and consumes nobody's quota.
		if ( $request_reaches_api ) {
			if ( $this->is_rate_limit_exceeded() && ! $user_has_cache_capability ) {
				return $this->set_failure_response( 429, 'Rate limit exceeded.' );
			}
		}

		try {
			$translation = $this->service->handle_translation( $object_id, $text, $target_lang, $user_has_cache_capability, $cached_translation, $source_lang );

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
	 * @since 2.0.0
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
	 * @since 2.0.0
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
	 * @since 2.0.0
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
	 * @since 2.0.0
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
