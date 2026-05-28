<?php

namespace YDPL\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YDPL\Contracts\ServiceProviderInterface;
use YDPL\Repositories\TranslationRepository;
use YDPL\Traits\CachePostTypesTrait;

/**
 * Registers a "Translation cache" column on the list screen of every post type
 * that has DeepL cache support enabled. Each row shows which of the configured
 * target languages already have a valid, non-stale cached translation so editors
 * can spot at a glance which posts will trigger a live DeepL API call.
 *
 * @since NEXT
 */
class AdminColumnsServiceProvider implements ServiceProviderInterface
{
	use CachePostTypesTrait;

	protected TranslationRepository $repository;

	public function __construct()
	{
		$this->repository = new TranslationRepository();
	}

	/**
	 * @since NEXT
	 */
	public function register(): void
	{
		foreach ( $this->get_cache_metabox_post_types() as $post_type ) {
			add_filter( "manage_{$post_type}_posts_columns", array( $this, 'add_column' ) );
			add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'render_column' ), 10, 2 );
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * @since NEXT
	 */
	public function add_column( array $columns ): array
	{
		$columns['ydpl_translation_cache'] = __( 'Translation cache', 'yard-deepl' );

		return $columns;
	}

	/**
	 * @since NEXT
	 */
	public function render_column( string $column, int $post_id ): void
	{
		if ( 'ydpl_translation_cache' !== $column ) {
			return;
		}

		$configured_languages = ydpl_resolve_from_container( 'ydpl.site_options' )->configured_supported_languages();

		if ( array() === $configured_languages ) {
			echo '<span class="ydpl-cache-status--none" aria-label="' . esc_attr__( 'No languages configured', 'yard-deepl' ) . '">—</span>';

			return;
		}

		$column_data     = $this->repository->get_column_data( $post_id, $configured_languages );
		$cached_langs    = $column_data['cached_languages'];
		$uncached_counts = $column_data['uncached_request_counts'];

		$badges = '';

		foreach ( $configured_languages as $lang ) {
			if ( in_array( $lang, $cached_langs, true ) ) {
				$badges .= sprintf(
					'<span class="ydpl-cache-badge ydpl-cache-badge--cached">%s</span>',
					esc_html( $lang )
				);
			} elseif ( isset( $uncached_counts[ $lang ] ) ) {
				$count   = $uncached_counts[ $lang ];
				$badges .= sprintf(
					'<span class="ydpl-cache-badge ydpl-cache-badge--uncached" title="%s">%s <span class="ydpl-badge-count">%s</span></span>',
					esc_attr(
						sprintf(
							/* translators: %d: number of uncached DeepL API calls made for this post and language */
							_n( '%d uncached call', '%d uncached calls', $count, 'yard-deepl' ),
							$count
						)
					),
					esc_html( $lang ),
					esc_html( number_format_i18n( $count ) )
				);
			}
		}

		if ( '' === $badges ) {
			echo '<span class="ydpl-cache-status--none" aria-label="' . esc_attr__( 'No cached translations', 'yard-deepl' ) . '">—</span>';

			return;
		}

		echo '<div class="ydpl-cache-badges">' . $badges . '</div>';
	}

	/**
	 * @since NEXT
	 */
	public function enqueue_styles( string $hook_suffix ): void
	{
		if ( 'edit.php' !== $hook_suffix ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->post_type, $this->get_cache_metabox_post_types(), true ) ) {
			return;
		}

		$path         = ydpl_asset_path( 'admin-columns.asset.php' );
		$script_asset = file_exists( $path ) ? require $path : array(
			'dependencies' => array(),
			'version'      => round( microtime( true ) ),
		);

		wp_enqueue_style( 'ydpl-admin-columns', ydpl_asset_url( 'admin-columns.css' ), $script_asset['dependencies'] ?? array(), $script_asset['version'] );
	}
}
