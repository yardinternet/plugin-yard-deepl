<?php

namespace YardDeepl\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YardDeepl\Contracts\ServiceProviderInterface;
use YardDeepl\Controllers\RestAPIController;

/**
 * @since 0.0.1
 */
class RestAPIServiceProvider implements ServiceProviderInterface
{
	private RestAPIController $controller;

	public function __construct()
	{
		$this->controller = new RestAPIController();
	}

	/**
	 * @since 0.0.1
	 */
	public function register(): void
	{
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * @since 0.0.1
	 */
	public function register_routes()
	{
		register_rest_route(
			YDPL_API_NAMESPACE,
			'/translate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this->controller, 'handle_translate_request' ),
				'permission_callback' => array( $this, 'verify_nonce' ),
				'args'                => array(
					'text'        => array(
						'description' => 'An array of texts to translate.',
						'type'        => 'array',
						'default'     => array(),
					),
					'target_lang' => array(
						'description' => 'The target language in which the provided text should be translated to.',
						'type'        => 'string',
						'default'     => 'NL',
						'required'    => true,
					),
				),
			)
		);
	}

	/**
	 * @since 0.0.1
	 */
	public function verify_nonce(): bool
	{
		return wp_verify_nonce( sanitize_text_field( wp_unslash( $_SERVER['HTTP_NONCE'] ?? '' ) ), YDPL_NONCE_REST_NAME );
	}
}
