<?php

/**
 * Plugin helpers.
 *
 * @package Yard_Deepl
 * @author  Yard | Digital Agency
 * @since   0.0.1
 */

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add prefix for the given string.
 *
 * @package Yard_Deepl
 * @author  Yard | Digital Agency
 * @since   0.0.1
 */
function ydpl_prefix( $name ): string
{
	return 'yard-deepl-' . $name;
}

/**
 * Generates a full plugin URL by appending the given path to the base plugin URL.
 *
 * @package Yard_Deepl
 * @author  Yard | Digital Agency
 * @since   0.0.1
 */
function ydpl_url( string $path ): string
{
	return YDPL_PLUGIN_URL . $path;
}

/**
 * Generates a full plugin path by appending the given path to the base plugin URL.
 *
 * @package Yard_Deepl
 * @author  Yard | Digital Agency
 * @since   0.0.1
 */
function ydpl_path( string $path ): string
{
	return YDPL_PLUGIN_DIR_PATH . $path;
}

/**
 * Generates a full asset URL by appending the given path to the plugin's asset directory.
 *
 * @package Yard_Deepl
 * @author  Yard | Digital Agency
 * @since   0.0.1
 */
function ydpl_asset_url( string $path ): string
{
	return ydpl_url( 'dist/' . $path );
}

/**
 * Generates a full asset path by appending the given path to the plugin's asset directory.
 *
 * @package Yard_Deepl
 * @author  Yard | Digital Agency
 * @since   1.1.1
 */
function ydpl_asset_path( string $path ): string
{
	return ydpl_path( 'dist/' . $path );
}

/**
 * Render a view file.
 *
 * @package Yard_Deepl
 * @author  Yard | Digital Agency
 * @since   0.0.1
 */
function ydpl_render_view( string $file_path, $data = array() )
{
	$full_path = YDPL_PLUGIN_DIR_PATH . 'src/Views/' . $file_path . '.php';

	if ( ! file_exists( $full_path ) ) {
		return '';
	}
	extract( $data, EXTR_SKIP );

	return require $full_path;
}

/**
 * Finds an entry of the container by its identifier and returns it.
 *
 * @package Yard_Deepl
 * @author  Yard | Digital Agency
 * @since   0.0.1
 */
function ydpl_resolve_from_container( string $container )
{
	return YDPL\Bootstrap::get_container()->get( $container );
}

/**
 * Escapes an array of data for safe output.
 *
 * @param mixed $data The data to escape.
 *
 * @return mixed The escaped data.
 */
function ydpl_escape_data( $data )
{
	if ( is_array( $data ) ) {
		// Recursively escape arrays
		return array_map( 'ydpl_escape_data', $data );
	}

	if ( is_string( $data ) ) {
		return esc_html( $data );
	}

	// Return other data types as is (e.g., numbers, objects)
	return $data;
}
