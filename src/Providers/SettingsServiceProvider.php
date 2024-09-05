<?php

namespace YardDeepl\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YardDeepl\Contracts\ServiceProviderInterface;
use YardDeepl\Controllers\SettingsController;

/**
 * @since 0.0.1
 */
class SettingsServiceProvider implements ServiceProviderInterface
{
	private SettingsController $controller;

	public function __construct()
	{
		$this->controller = new SettingsController();
	}

	/**
	 * @since 0.0.1
	 */
	public function register(): void
	{
		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings_options' ) );
	}

	/**
	 * Add a settings page to the wp-admin.
	 *
	 * @since 0.0.1
	 */
	public function register_settings_page(): void
	{
		add_options_page(
			__( 'Yard Deepl', 'yard-deepl' ),
			__( 'Yard Deepl', 'yard-deepl' ),
			'manage_options',
			'yard-deepl',
			array( $this->controller, 'render_page' )
		);
	}

	/**
	 * Initialize the options for the settings page.
	 *
	 * @since 0.0.1
	 */
	public function register_settings_options(): void
	{
		register_setting(
			'ydpl_options_group',
			YDPL_SITE_OPTION_NAME
		);

		add_settings_section(
			'ydpl_section_id',
			__( 'Settings', 'yard-deepl' ),
			array( $this->controller, 'section_description' ),
			'yard-deepl'
		);

		add_settings_field(
			'ydpl_api_key',
			__( 'Deepl API key', 'yard-deepl' ),
			array( $this->controller, 'section_fields_render' ),
			'yard-deepl',
			'ydpl_section_id',
			array( 'settings_field_id' => 'ydpl_api_key' )
		);

		add_settings_field(
			'ydpl_supported_target_languages',
			__( 'Deepl supported languages', 'yard-deepl' ),
			array( $this->controller, 'section_fields_render' ),
			'yard-deepl',
			'ydpl_section_id',
			array( 'settings_field_id' => 'ydpl_supported_target_languages' )
		);
	}
}
