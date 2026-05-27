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

		$is_disabled = get_post_meta( $object_id, 'ydpl_disable_deepl_translation_cache', true );

		if ( ! is_string( $is_disabled ) || ( 0 < strlen( $is_disabled ) && '1' === $is_disabled ) ) {
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
	protected function translated_object_exists( string $object_id ): bool
	{
		$object = get_post( $object_id );

		return $object instanceof WP_Post;
	}

	/**
	 * Deletes all cached translations and their modified timestamps for every language.
	 *
	 * Removes both the cached translation data (_translation_<lang>) and the
	 * staleness timestamp (_translation_modified_<lang>) written by store_translation().
	 * Returns silently when the object does not exist.
	 *
	 * @since NEXT
	 */
	public function delete_cached_translations( int $object_id ): void
	{
		if ( ! $this->translated_object_exists( $object_id ) ) {
			return;
		}

		$meta_keys = array_keys( get_post_meta( $object_id ) );

		foreach ( $meta_keys as $key ) {
			if ( 0 === strpos( $key, '_translation_' ) || 0 === strpos( $key, '_translation_modified_' ) ) {
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

		update_post_meta( $object_id, "_translation_$target_lang", $translation );
		update_post_meta( $object_id, "_translation_modified_$target_lang", current_time( 'mysql' ) );
	}
}
