<?php

/**
 * Plugin helpers.
 *
 * @package Yard_Deepl
 *
 * @author  Yard | Digital Agency
 *
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
 *
 * @author  Yard | Digital Agency
 *
 * @since   0.0.1
 */
if ( ! function_exists( 'yard_deepl_prefix' ) ) {
	function yard_deepl_prefix( $name ): string
	{
		return 'yard-deepl-' . $name;
	}
}

/**
 * Add prefix for the given string.
 *
 * @package Yard_Deepl
 *
 * @author  Yard | Digital Agency
 *
 * @since   0.0.1
 */
if ( ! function_exists( 'yard_deepl_url' ) ) {
	function yard_deepl_url( string $path ): string
	{
		return YDPL_PLUGIN_URL . $path;
	}
}

/**
 * Add prefix for the given string.
 *
 * @package Yard_Deepl
 *
 * @author  Yard | Digital Agency
 *
 * @since   0.0.1
 */
if ( ! function_exists( 'yard_deepl_asset_url' ) ) {
	function yard_deepl_asset_url( string $path ): string
	{
		return yard_deepl_url( 'dist/' . $path );
	}
}

/**
 * Render a view file.
 *
 * @package Yard_Deepl
 *
 * @author  Yard | Digital Agency
 *
 * @since   0.0.1
 */
if ( ! function_exists( 'yard_deepl_render_view' ) ) {
	function yard_deepl_render_view( string $file_path, $data = array() ): void
	{
		// Escape the data array
		$escaped_data = array_map( 'yard_deepl_escape_data', $data );

		// Render the view with escaped data
		echo yard_deepl_resolve_from_container( 'ydpl.blade_compiler' )->render( $file_path, $escaped_data );
	}
}

/**
 * Finds an entry of the container by its identifier and returns it.
 *
 * @package Yard_Deepl
 *
 * @author  Yard | Digital Agency
 *
 * @since   0.0.1
 */
if ( ! function_exists( 'yard_deepl_resolve_from_container' ) ) {
	function yard_deepl_resolve_from_container( string $container )
	{
		return YardDeepl\Bootstrap::get_container()->get( $container );
	}
}

/**
 * Escapes an array of data for safe output.
 *
 * @param mixed $data The data to escape.
 *
 * @return mixed The escaped data.
 */
if ( ! function_exists( 'yard_deepl_escape_data' ) ) {
	function yard_deepl_escape_data( $data )
	{
		if ( is_array( $data ) ) {
			// Recursively escape arrays
			return array_map( 'yard_deepl_escape_data', $data );
		}

		if ( is_string( $data ) ) {
			return esc_html( $data );
		}

		// Return other data types as is (e.g., numbers, objects)
		return $data;
	}
}
