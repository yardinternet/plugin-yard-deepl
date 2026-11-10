<?php

namespace YDPL\Repositories;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_Post;
use YDPL\Exceptions\ObjectNotFoundException;

/**
 * @since 0.0.1
 */
class TranslationRepository
{
	/**
	 * @since 0.0.1
	 *
	 * @throws ObjectNotFoundException
	 */
	public function get_cached_translation( int $object_id, string $target_lang ): ?array
	{
		if ( ! $this->translated_object_exists( $object_id ) ) {
			throw new ObjectNotFoundException( 'Translated object not found.', 404 );
		}

		if ( $this->is_cache_disabled( $object_id ) ) {
			return null;
		}

		$post_modified        = get_post_field( 'post_modified', $object_id );
		$cached_translation   = get_post_meta( $object_id, "_translation_$target_lang", true );
		$translation_modified = get_post_meta( $object_id, "_translation_modified_$target_lang", true );

		if ( ! $cached_translation || strtotime( $translation_modified ) < strtotime( $post_modified ) ) {
			return null;
		}

		return $cached_translation;
	}

	/**
	 * @since 0.0.1
	 */
	protected function translated_object_exists( int $object_id ): bool
	{
		$object = get_post( $object_id );

		return $object instanceof WP_Post;
	}

	/**
	 * @since 2.0.0
	 */
	public function delete_cached_translations( int $object_id ): void
	{
		if ( ! $this->translated_object_exists( $object_id ) ) {
			return;
		}

		$meta_keys = array_keys( get_post_meta( $object_id ) );

		foreach ( $meta_keys as $key ) {
			if ( 0 === strpos( $key, '_translation_' ) ) {
				delete_post_meta( $object_id, $key );
			}
		}
	}

	/**
	 * @since 0.0.1
	 *
	 * @throws ObjectNotFoundException
	 */
	public function store_translation( int $object_id, string $target_lang, array $translation ): void
	{
		if ( ! $this->translated_object_exists( $object_id ) ) {
			throw new ObjectNotFoundException( 'Translated object not found.', 404 );
		}

		if ( $this->is_cache_disabled( $object_id ) ) {
			return;
		}

		update_post_meta( $object_id, "_translation_$target_lang", $translation );
		update_post_meta( $object_id, "_translation_modified_$target_lang", current_time( 'mysql' ) );
	}

	protected function is_cache_disabled( int $object_id ): bool
	{
		$is_disabled = get_post_meta( $object_id, 'ydpl_disable_deepl_translation_cache', true );

		return is_string( $is_disabled ) && 0 < strlen( $is_disabled ) && '1' === $is_disabled;
	}
}
