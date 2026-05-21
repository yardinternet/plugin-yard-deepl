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
		$text        = $request->get_param( 'text' );
		$target_lang = $request->get_param( 'target_lang' );
		$object_id   = $request->get_param( 'object_id' );
		$origin      = $request->get_header( 'origin' );

		if ( is_null( $origin ) || home_url() !== $origin ) {
			return $this->set_failure_response( 403, 'Invalid origin. Origin does not match the site URL.' );
		}

		// Are required by Deepl.
		if ( empty( $text ) || empty( $target_lang ) ) {
			return $this->set_failure_response( 400, 'Invalid input parameters.' );
		}

		// Is required when configured as such in the plugin settings.
		if ( $this->options->rest_api_param_object_id_is_mandatory() && empty( $object_id ) ) {
			return $this->set_failure_response( 400, 'Invalid input parameters.' );
		}

		// Apply rate limit check if object ID is absent or translation is not cached when an object ID is present.
		if ( empty( $object_id ) || ! $this->service->object_has_cached_translation( (int) $object_id, $target_lang ) ) {
			if ( $this->is_rate_limit_exceeded() ) {
				return $this->set_failure_response( 429, 'Rate limit exceeded.' );
			}
		}

		$cache = current_user_can( apply_filters( 'yard::deepl/cache_capability', 'edit_posts' ) );

		try {
			$translation = $this->service->handle_translation( (int) $object_id, $text, $target_lang, $cache );

			if ( empty( $translation ) ) {
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
	 * @since 1.1.1
	 */
	protected function is_rate_limit_exceeded(): bool
	{
		$client_ip = $this->get_client_ip();

		if ( empty( $client_ip ) ) {
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
	 * @since 1.1.1
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
