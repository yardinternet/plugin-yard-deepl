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
		add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ), 999 );
		add_action( 'save_post', array( $this, 'save_metabox_values' ), 999 );
	}

	/**
	 * @since 1.1.0
	 */
	public function register_meta_box(): void
	{
		add_meta_box(
			'yard-deepl',
			__( 'Yard Deepl', 'yard-deepl' ),
			array( $this, 'render_meta_box' ),
			apply_filters( 'yard::deepl/disable_cache_metabox_post_types', array( 'page' ) ),
			'side',
			'high'
		);
	}

	/**
	 * @since 1.1.0
	 */
	public function render_meta_box( WP_Post $post ): void
	{
		$this->security_nonce_field();

		$html = sprintf(
			'<p>%s</p>',
			esc_html__( 'Disable translation cache when this object contains dynamic content.', 'yard-deepl' )
		);

		$html = $this->disable_translation_cache_metabox( $html, $post );

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
	private function disable_translation_cache_metabox( string $html, WP_Post $post ): string
	{
		$is_disabled = get_post_meta( $post->ID, 'ydpl_disable_deepl_translation_cache', true );

		$html .= '<div class="ydpl-metabox-wrapper"><label for="ydpl_disable_deepl_translation_cache">';
		$html .= sprintf( '<input type="checkbox" name="ydpl_disable_deepl_translation_cache" id="ydpl_disable_deepl_translation_cache" value="1"%s />', esc_attr( checked( $is_disabled, 1, false ) ) );
		$html .= sprintf( ' %s</label></div>', esc_html__( 'Disable translation cache?', 'yard-deepl' ) );

		return $html;
	}

	/**
	 * @since 1.1.0
	 */
	public function save_metabox_values( int $post_id ): void
	{
		if ( ! $this->should_save_metabox_values( $post_id ) ) {
			return;
		}

		$is_disabled = ( isset( $_POST['ydpl_disable_deepl_translation_cache'] ) ? '1' : '0' );
		update_post_meta( $post_id, 'ydpl_disable_deepl_translation_cache', $is_disabled );
	}

	/**
	 * @since 1.1.0
	 */
	private function should_save_metabox_values( int $post_id ): bool
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
