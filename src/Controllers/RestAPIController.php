<?php

namespace YardDeepl\Controllers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;
use WP_REST_Request;
use WP_REST_Response;
use YardDeepl\Services\TranslationService;
use YardDeepl\Singletons\SiteOptionsSingleton;
use YardDeepl\Traits\ErrorLog;

/**
 * @since 0.0.1
 */
class RestAPIController
{
	use ErrorLog;

	protected TranslationService $service;
	protected SiteOptionsSingleton $options;

	public function __construct()
	{
		$this->service = new TranslationService();
		$this->options = yard_deepl_resolve_from_container( 'ydpl.site_options' );
	}

	/**
	 * @since 0.0.1
	 */
	public function handle_translate_request( WP_REST_Request $request ): WP_REST_Response
	{
		$text        = $request->get_param( 'text' );
		$target_lang = $request->get_param( 'target_lang' );
		$object_id   = $request->get_param( 'object_id' );

		// Are required by Deepl.
		if ( empty( $text ) || empty( $target_lang ) ) {
			return $this->set_failure_response( 400, 'Invalid input parameters.' );
		}

		// Is required when configured as such in the plugin settings.
		if ( $this->options->rest_api_param_object_id_is_mandatory() && empty( $object_id ) ) {
			return $this->set_failure_response( 400, 'Invalid input parameters.' );
		}

		try {
			$translation = $this->service->handle_translation( (int) $object_id, $text, $target_lang );

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
