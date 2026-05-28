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
 * @since NEXT
 */
trait CachePostTypesTrait
{
	/**
	 * @since NEXT
	 */
	protected function get_cache_metabox_post_types(): array
	{
		$post_types = apply_filters_deprecated(
			'yard::deepl/disable_cache_metabox_post_types',
			array( array( 'page' ) ),
			'NEXT',
			'yard::deepl/cache_metabox_post_types'
		);

		return apply_filters( 'yard::deepl/cache_metabox_post_types', $post_types );
	}
}
