<?php

namespace YDPL\Controllers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YDPL\Clients\DeeplClient;

/**
 * @since 0.0.1
 */
class SettingsController
{
	/**
	 * @since 0.0.1
	 */
	public function render_page(): void
	{
		ydpl_render_view( 'admin/settings-page' );
	}

	/**
	 * @since 0.0.1
	 */
	public function section_description_general(): void
	{
		ydpl_render_view( 'admin/partials/settings/settings-description-general' );
	}

	/**
	 * @since 0.0.1
	 */
	public function section_description_rest_api(): void
	{
		ydpl_render_view( 'admin/partials/settings/settings-description-rest-api' );
	}

	/**
	 * @since 0.0.1
	 */
	public function section_fields_render( array $args ): void
	{
		ydpl_render_view(
			'admin/partials/settings/settings-fields',
			array(
				'api_key'                               => ydpl_resolve_from_container( 'ydpl.site_options' )->api_key(),
				'base_url'                              => ydpl_resolve_from_container( 'ydpl.site_options' )->raw_base_url(),
				'default_base_url'                      => DeeplClient::DEFAULT_BASE_URL,
				'settings_field_id'                     => $args['settings_field_id'] ?? '',
				'supported_languages'                   => ydpl_resolve_from_container( 'ydpl.supported_target.languages' ),
				'configured_supported_languages'        => ydpl_resolve_from_container( 'ydpl.site_options' )->configured_supported_languages(),
				'rest_api_param_object_id_is_mandatory' => ydpl_resolve_from_container( 'ydpl.site_options' )->rest_api_param_object_id_is_mandatory(),
			)
		);
	}

	/**
	 * @since 0.0.1
	 */
	public function sanitize_plugin_options_settings( $settings ): array
	{
		if ( ! is_array( $settings ) ) {
			return array();
		}

		$sanitize_recursive = function ( $value ) use ( &$sanitize_recursive ) {
			if ( is_array( $value ) ) {
				return array_map( $sanitize_recursive, $value );
			}

			if ( is_string( $value ) ) {
				return sanitize_text_field( $value );
			}

			return $value;
		};

		$settings = array_map( $sanitize_recursive, $settings );
		$settings = $this->sanitize_base_url_setting( $settings );

		return $settings;
	}

	/**
	 * @since NEXT
	 */
	private function sanitize_base_url_setting( array $settings ): array
	{
		if ( ! is_string( $settings['ydpl_base_url'] ?? null ) ) {
			return $settings;
		}

		$base_url = trim( $settings['ydpl_base_url'] );

		if ( '' === $base_url ) {
			$settings['ydpl_base_url'] = '';

			return $settings;
		}

		$scheme        = strtolower( (string) wp_parse_url( $base_url, PHP_URL_SCHEME ) );
		$host          = strtolower( (string) wp_parse_url( $base_url, PHP_URL_HOST ) );
		$path          = rtrim( (string) wp_parse_url( $base_url, PHP_URL_PATH ), '/' );
		$is_deepl_host = str_ends_with( $host, '.deepl.com' );

		if ( ! wp_http_validate_url( $base_url ) || 'https' !== $scheme || ! $is_deepl_host || '/v2/translate' !== $path ) {
			add_settings_error(
				'ydpl_options_group',
				'ydpl_base_url_invalid',
				__( 'The DeepL API base URL must be a valid https://*.deepl.com/v2/translate URL. The previous value has been kept.', 'yard-deepl' )
			);

			$previous_options          = get_option( YDPL_SITE_OPTION_NAME, array() );
			$settings['ydpl_base_url'] = $previous_options['ydpl_base_url'] ?? '';

			return $settings;
		}

		$settings['ydpl_base_url'] = rtrim( $base_url, '/' );

		return $settings;
	}
}
