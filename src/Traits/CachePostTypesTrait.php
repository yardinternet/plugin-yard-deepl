<?php

namespace YDPL\Traits;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides the list of post types that have DeepL cache support enabled.
 *
 * Shared between MetaBoxServiceProvider and AdminColumnsServiceProvider so
 * both always operate on the same set of post types.
 *
 * @since 2.0.0
 */
trait CachePostTypesTrait
{
	/**
	 * @since 2.0.0
	 */
	protected function get_cache_metabox_post_types(): array
	{
		$post_types = apply_filters_deprecated(
			'yard::deepl/disable_cache_metabox_post_types',
			array( array( 'page' ) ),
			'2.0.0',
			'yard::deepl/cache_metabox_post_types'
		);

		return apply_filters( 'yard::deepl/cache_metabox_post_types', $post_types );
	}
}
