<?php

namespace YardDeepl\Repositories;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WP_Post;
use YardDeepl\Exceptions\ObjectNotFoundException;

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
	public function get_cached_translation(int $object_id, string $target_lang ): ?array
	{
		if ( ! $this->translated_object_exists( $object_id )) {
			throw new ObjectNotFoundException( 'Translated object not found.', 404 );
		}

		$post_modified        = get_post_field( 'post_modified', $object_id );
		$cached_translation   = get_post_meta( $object_id, "_translation_$target_lang", true );
		$translation_modified = get_post_meta( $object_id, "_translation_modified_$target_lang", true );

		if ( ! $cached_translation || strtotime( $translation_modified ) < strtotime( $post_modified )) {
			return null;
		}

		return $cached_translation;
	}

	/**
	 * @since 0.0.1
	 */
	protected function translated_object_exists(string $object_id ): bool
	{
		$object = get_post( $object_id );

		return $object instanceof WP_Post;
	}

	/**
	 * @since 0.0.1
	 *
	 * @throws ObjectNotFoundException
	 */
	public function store_translation(int $object_id, string $target_lang, array $translation ): void
	{
		if ( ! $this->translated_object_exists( $object_id )) {
			throw new ObjectNotFoundException( 'Translated object not found.', 404 );
		}

		update_post_meta( $object_id, "_translation_$target_lang", $translation );
		update_post_meta( $object_id, "_translation_modified_$target_lang", current_time( 'mysql' ) );
	}
}
