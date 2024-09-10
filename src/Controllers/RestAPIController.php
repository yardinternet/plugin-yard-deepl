<?php

namespace YardDeepl\Controllers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use Exception;
use WP_REST_Request;
use WP_REST_Response;
use YardDeepl\Services\TranslationService;
use YardDeepl\Traits\ErrorLog;

/**
 * @since 0.0.1
 */
class RestAPIController
{
	use ErrorLog;

	protected TranslationService $service;

	public function __construct()
	{
		$this->service = new TranslationService();
	}

	/**
	 * @since 0.0.1
	 */
	public function handle_translate_request(WP_REST_Request $request ): WP_REST_Response
	{
		$text        = $request->get_param( 'text' );
		$target_lang = $request->get_param( 'target_lang' );
		$object_id   = $request->get_param( 'object_id' );

		if (empty( $text ) || empty( $target_lang ) || empty( $object_id )) {
			return $this->set_failure_response( 400, 'Invalid input parameters.' );
		}

		try {
			$translation = $this->service->handle_translation( $object_id, $text, $target_lang );

			if (empty( $translation )) {
				throw new Exception( 'Failed to translate text.', 500 );
			}
		} catch (Exception $e) {
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
	protected function set_failure_response(int $status, string $message ): WP_REST_Response
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
