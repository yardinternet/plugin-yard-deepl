<?php

namespace YardDeepl\Controllers;

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
		yard_deepl_render_view( 'admin.settings-page' );
	}

	/**
	 * @since 0.0.1
	 */
	public function section_description(): void
	{
		yard_deepl_render_view( 'admin.partials.settings.settings-description' );
	}

	/**
	 * @since 0.0.1
	 */
	public function section_fields_render( array $args ): void
	{
		yard_deepl_render_view(
			'admin.partials.settings.settings-fields',
			array(
				'api_key'                        => yard_deepl_resolve_from_container( 'ydpl.site_options' )->api_key(),
				'settings_field_id'              => $args['settings_field_id'] ?? '',
				'supported_languages'            => yard_deepl_resolve_from_container( 'ydpl.supported_target.languages' ),
				'configured_supported_languages' => yard_deepl_resolve_from_container( 'ydpl.site_options' )->configured_supported_languages(),
			)
		);
	}
}
