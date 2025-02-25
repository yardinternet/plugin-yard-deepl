<?php

namespace YDPL\Providers;

use WP_Post;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YDPL\Contracts\ServiceProviderInterface;

/**
 * @since 1.1.0
 */
class MetaBoxServiceProvider implements ServiceProviderInterface
{
	public function register(): void
	{
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ), 999 );
		add_action( 'save_post', array( $this, 'handle_saved_metabox_values' ), 999 );
	}

	/**
	 * @since 1.1.0
	 */
	public function register_meta_boxes(): void
	{
		add_meta_box(
			'yard-deepl',
			__( 'Yard Deepl', 'yard-deepl' ),
			array( $this, 'render_meta_boxes' ),
			apply_filters( 'yard::deepl/cache_metabox_post_types', array( 'page' ) ),
			'side',
			'high'
		);
	}

	/**
	 * @since 1.1.0
	 */
	public function render_meta_boxes( WP_Post $post ): void
	{
		$this->security_nonce_field();

		$html = $this->translation_cache_metaboxes( $post );

		echo $html;
	}

	/**
	 * @since 1.1.0
	 */
	private function security_nonce_field(): void
	{
		wp_nonce_field( 'ydpl_metabox_nonce_action', 'ydpl_metabox_nonce' );
	}

	/**
	 * @since 1.1.0
	 */
	private function translation_cache_metaboxes( WP_Post $post ): string
	{
		$cache_is_disabled       = get_post_meta( $post->ID, 'ydpl_disable_deepl_translation_cache', true );
		$cache_clear_is_disabled = get_post_meta( $post->ID, 'ydpl_clear_deepl_translation_cache', true );

		$html  = '<div class="ydpl-metabox-wrapper">';
		$html .= '<div class="ydpl-metabox-row">';
		$html .= sprintf( '<p>%s</p>', esc_html__( 'Disable translation cache when this object contains dynamic content.', 'yard-deepl' ) );
		$html .= '<label for="ydpl_disable_deepl_translation_cache">';
		$html .= sprintf( '<input type="checkbox" name="ydpl_disable_deepl_translation_cache" id="ydpl_disable_deepl_translation_cache" value="1"%s />', esc_attr( checked( $cache_is_disabled, 1, false ) ) );
		$html .= sprintf( ' %s</label></div>', esc_html__( 'Disable translation cache?', 'yard-deepl' ) );

		$html .= '<div class="ydpl-metabox-row">';
		$html .= sprintf( '<p>%s</p>', esc_html__( 'Clear cached translations on save.', 'yard-deepl' ) );
		$html .= '<label for="ydpl_clear_deepl_translation_cache">';
		$html .= sprintf( '<input type="checkbox" name="ydpl_clear_deepl_translation_cache" id="ydpl_clear_deepl_translation_cache" value="1"%s />', esc_attr( checked( $cache_clear_is_disabled, 1, false ) ) );
		$html .= sprintf( ' %s</label></div>', esc_html__( 'Clear translation cache?', 'yard-deepl' ) );
		$html .= '</div>';

		return $html;
	}

	/**
	 * @since 1.1.0
	 */
	public function handle_saved_metabox_values( int $post_id ): void
	{
		if ( ! $this->should_handle_saved_metabox_values( $post_id ) ) {
			return;
		}

		$this->set_disable_cache_metabox_value( $post_id );
		$this->clear_translation_cache( $post_id );
	}

	/**
	 * @since 1.1.1
	 */
	private function set_disable_cache_metabox_value( int $post_id ): void
	{
		$cache_is_disabled = ( isset( $_POST['ydpl_disable_deepl_translation_cache'] ) ? '1' : '0' );
		update_post_meta( $post_id, 'ydpl_disable_deepl_translation_cache', $cache_is_disabled );
	}

	/**
	 * @since 1.1.1
	 */
	private function clear_translation_cache( int $post_id ): void
	{
		$cache_clear_is_enabled = ( isset( $_POST['ydpl_clear_deepl_translation_cache'] ) ? '1' : '0' );

		if ( '1' !== $cache_clear_is_enabled ) {
			return;
		}

		$meta_keys = get_post_meta( $post_id );

		foreach ( $meta_keys as $key => $value ) {
			if ( strpos( $key, '_translation_' ) === 0 ) {
				delete_post_meta( $post_id, $key );
			}
		}
	}

	/**
	 * @since 1.1.0
	 */
	private function should_handle_saved_metabox_values( int $post_id ): bool
	{
		// Verify nonce.
		if ( ! isset( $_POST['ydpl_metabox_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['ydpl_metabox_nonce'] ), 'ydpl_metabox_nonce_action' ) ) {
			return false;
		}

		// Skip autosave to avoid overwriting values during revisions or autosaves.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		// Only allow updates for supported post types.
		$post_types = apply_filters( 'yard::deepl/disable_cache_metabox_post_types', array( 'page' ) );
		if ( ! in_array( get_post_type( $post_id ), $post_types, true ) ) {
			return false;
		}

		return true;
	}
}
