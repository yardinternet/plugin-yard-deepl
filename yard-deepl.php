<?php
/**
 * @package Yard_Deepl
 *
 * @author  Yard | Digital Agency
 *
 * Plugin Name: Yard Deepl
 * Description: This plugin registers secure API endpoints that allows you to request translations directly from DeepL
 * Version: 0.0.2
 * Author: Yard | Digital
 * Author URI: https://www.yard.nl
 * License: GPLv2 or later
 * Text Domain: yard-deepl
 * Domain Path: /languages
 * Requires at least: 6.0
 */

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'YDPL_VERSION', '0.0.2' );
define( 'YDPL_REQUIRED_WP_VERSION', '6.0' );
define( 'YDPL_PLUGIN_NAME', basename( __DIR__ ) );
define( 'YDPL_PLUGIN_FILE', __FILE__ );
define( 'YDPL_PLUGIN_URL', plugins_url( '/', YDPL_PLUGIN_FILE ) );
define( 'YDPL_PLUGIN_DIR_PATH', plugin_dir_path( YDPL_PLUGIN_FILE ) );
define( 'YDPL_API_NAMESPACE', 'yard/deepl/v1' );
define( 'YDPL_SITE_OPTION_NAME', 'ydpl_options' );
define( 'YDPL_NONCE_REST_NAME', 'ydpl_rest' );

/**
 * Require autoloader.
 */
if ( file_exists( __DIR__ . '/vendor-prefixed/autoload.php' ) ) {
	require_once __DIR__ . '/vendor-prefixed/autoload.php';
}
require_once __DIR__ . '/src/autoloader.php';
require_once __DIR__ . '/src/Bootstrap.php';

add_action(
	'plugins_loaded',
	function () {
		$init = new YardDeepl\Bootstrap();
	}
);
