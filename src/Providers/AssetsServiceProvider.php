<?php

namespace YardDeepl\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YardDeepl\Contracts\ServiceProviderInterface;

/**
 * @since 0.0.1
 */
class AssetsServiceProvider implements ServiceProviderInterface
{
	/**
	 * @since 0.0.1
	 */
	public function register(): void
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * @since 0.0.1
	 */
	public function enqueue_assets(): void
	{
		$path         = yard_deepl_asset_url( 'main.asset.php' );
		$script_asset = file_exists( $path ) ? require $path : array(
			'dependencies' => array(),
			'version'      => round( microtime( true ) ),
		);

		wp_enqueue_script( 'ydpl-main', yard_deepl_asset_url( 'main.js' ), array( 'jquery' ), $script_asset['version'], true );
		wp_localize_script(
			'ydpl-main',
			'ydpl',
			array(
				'ydpl_translate_post_id'   => get_the_ID() ?: null,
				'ydpl_rest_translate_url'  => esc_url_raw( rest_url( YDPL_API_NAMESPACE . '/translate' ) ),
				'ydpl_supported_languages' => $this->format_selected_supported_languages(),
				'ydpl_api_request_nonce'   => wp_create_nonce( YDPL_NONCE_REST_NAME ),
			)
		);
	}

	private function format_selected_supported_languages(): array
	{
		$supported_languages            = yard_deepl_resolve_from_container( 'ydpl.supported_target.languages' );
		$configured_supported_languages = yard_deepl_resolve_from_container( 'ydpl.site_options' )->configured_supported_languages();

		$filtered = array_filter(
			$supported_languages,
			function ($supported_language ) use ($configured_supported_languages ) {
				return in_array( $supported_language['iso_alpha2'], $configured_supported_languages );
			}
		);

		return array_values( $filtered );
	}
}
