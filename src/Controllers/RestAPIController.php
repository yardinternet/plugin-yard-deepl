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
use YardDeepl\Services\DeeplService;
use YardDeepl\Traits\ErrorLog;

/**
 * @since 0.0.1
 */
class RestAPIController
{
	use ErrorLog;

	/**
	 * @since 0.0.1
	 */
	public function handle_translate_request(WP_REST_Request $request ): WP_REST_Response
	{
		$text        = array_map( 'sanitize_text_field', $request->get_param( 'text' ) );
		$target_lang = sanitize_text_field( $request->get_param( 'target_lang' ) );

		if (empty( $text ) || empty( $target_lang )) {
			return $this->set_failure_response( 400, 'Invalid input parameters.' );
		}

		try {
			$translation = DeeplService::getInstance()->translate( $text, $target_lang );

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
