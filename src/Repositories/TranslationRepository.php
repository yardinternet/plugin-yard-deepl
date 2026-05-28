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
	 * Returns data needed to render the admin list-table column in a single meta fetch.
	 *
	 * 'cached_languages'        — ISO codes from $language_codes whose cache is valid and non-stale.
	 * 'uncached_request_counts' — map of ISO code => cumulative uncached visitor API calls, only for
	 *                             languages that have at least one recorded call.
	 *
	 * @since NEXT
	 *
	 * @return array{ cached_languages: string[], uncached_request_counts: array<string, int> }
	 */
	public function get_column_data( int $object_id, array $language_codes ): array
	{
		$empty = array(
			'cached_languages'        => array(),
			'uncached_request_counts' => array(),
		);

		if ( ! $this->translated_object_exists( $object_id ) || $this->is_cache_disabled( $object_id ) ) {
			return $empty;
		}

		$post_modified           = get_post_field( 'post_modified', $object_id );
		$post_modified_timestamp = strtotime( $post_modified );
		$all_meta                = get_post_meta( $object_id );
		$cached                  = array();
		$counts                  = array();

		foreach ( $language_codes as $lang ) {
			$translation_value    = $all_meta[ "_translation_$lang" ][0] ?? null;
			$translation_modified = $all_meta[ "_translation_modified_$lang" ][0] ?? null;

			if ( $translation_value && $translation_modified && strtotime( $translation_modified ) >= $post_modified_timestamp ) {
				$cached[] = $lang;
			}

			$count = (int) ( $all_meta[ "_ydpl_uncached_request_count_$lang" ][0] ?? 0 );

			if ( 0 < $count ) {
				$counts[ $lang ] = $count;
			}
		}

		return array(
			'cached_languages'        => $cached,
			'uncached_request_counts' => $counts,
		);
	}

	/**
	 * Increments the per-language count of uncached visitor API calls for this post.
	 *
	 * Only called for requests from visitors who lack cache-write capability, so the
	 * count reflects API spend that caching would have prevented. Returns silently
	 * when the object does not exist.
	 *
	 * @since NEXT
	 */
	public function increment_uncached_request_count( int $object_id, string $target_lang ): void
	{
		if ( ! $this->translated_object_exists( $object_id ) || $this->is_cache_disabled( $object_id ) ) {
			return;
		}

		$key     = "_ydpl_uncached_request_count_$target_lang";
		$current = (int) get_post_meta( $object_id, $key, true );

		update_post_meta( $object_id, $key, $current + 1 );
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

		if ( $this->is_cache_disabled( $object_id ) ) {
			return;
		}

		update_post_meta( $object_id, "_translation_$target_lang", $translation );
		update_post_meta( $object_id, "_translation_modified_$target_lang", current_time( 'mysql' ) );
		delete_post_meta( $object_id, "_ydpl_uncached_request_count_$target_lang" );
	}

	protected function is_cache_disabled( int $object_id ): bool
	{
		$is_disabled = get_post_meta( $object_id, 'ydpl_disable_deepl_translation_cache', true );

		return is_string( $is_disabled ) && 0 < strlen( $is_disabled ) && '1' === $is_disabled;
	}
}
