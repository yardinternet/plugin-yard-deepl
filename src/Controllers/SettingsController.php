<?php

namespace YDPL\Controllers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

		return array_map( $sanitize_recursive, $settings );
	}
}
