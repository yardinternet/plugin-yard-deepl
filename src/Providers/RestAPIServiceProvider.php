<?php

namespace YDPL\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YDPL\Contracts\ServiceProviderInterface;
use YDPL\Controllers\RestAPIController;
use YDPL\Singletons\SiteOptionsSingleton;

/**
 * @since 0.0.1
 */
class RestAPIServiceProvider implements ServiceProviderInterface
{
	private RestAPIController $controller;
	protected SiteOptionsSingleton $options;

	public function __construct()
	{
		$this->controller = new RestAPIController();
		$this->options    = ydpl_resolve_from_container( 'ydpl.site_options' );
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
						'description'       => 'An array of texts to translate.',
						'type'              => 'array',
						'default'           => array(),
						'required'          => true,
						'sanitize_callback' => function ( $value, $request, $param ) {
							return array_map( 'sanitize_text_field', $value );
						},
					),
					'target_lang' => array(
						'description'       => 'The target language in which the provided text should be translated to.',
						'type'              => 'string',
						'default'           => 'NL',
						'required'          => true,
						'sanitize_callback' => function ( $value, $request, $param ) {
							return sanitize_text_field( $value );
						},
					),
					'object_id'   => array(
						'description'       => 'The ID of the object to translate.',
						'required'          => $this->options->rest_api_param_object_id_is_mandatory(),
						'validate_callback' => function ( $value, $request, $param ) {
							return is_numeric( $value );
						},
						'sanitize_callback' => function ( $value, $request, $param ) {
							return intval( $value );
						},
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
		return wp_verify_nonce( sanitize_text_field( wp_unslash( $_SERVER['HTTP_NONCE'] ?? '' ) ), YDPL_NONCE_REST_NAME ) || is_user_logged_in();
	}
}
