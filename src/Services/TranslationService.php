<?php

namespace YDPL\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YDPL\Exceptions\ObjectNotFoundException;
use YDPL\Repositories\TranslationRepository;

/**
 * @since 0.0.1
 */
class TranslationService
{
	protected TranslationRepository $repository;

	public function __construct()
	{
		$this->repository = new TranslationRepository();
	}

	/**
	 * @since 0.0.1
	 */
	public function handle_translation( int $object_id, array $text, string $target_lang, bool $cache = false, ?array $cached_translation = null, string $source_lang = '' ): array
	{
		if ( 0 < $object_id ) {
			return $this->handle_translation_with_object_id( $object_id, $text, $target_lang, $cache, $cached_translation, $source_lang );
		}

		return $this->handle_translation_without_object_id( $text, $target_lang, $source_lang );
	}

	/**
	 * @since 0.0.1
	 */
	public function handle_translation_with_object_id( int $object_id, array $text, string $target_lang, bool $cache = false, ?array $cached_translation = null, string $source_lang = '' ): array
	{
		if ( null === $cached_translation ) {
			$cached_translation = $this->get_cached_translation( $object_id, $target_lang, $source_lang );
		}

		$cached_translation = is_array( $cached_translation ) ? $cached_translation : array();
		$untranslated_text  = self::untranslated_text( $cached_translation, $text );

		/**
		 * A full hit still short-circuits without touching the API.
		 *
		 * @since 2.2.0
		 */
		if ( array() === $untranslated_text ) {
			return $cached_translation;
		}

		/**
		 * Only the strings the entry does not already hold go to DeepL, and the
		 * result is merged back onto the entry.
		 *
		 * An entry warmed by an older toolbar can be missing strings the current
		 * one asks for — both resolve to source NL, so they share a cache key —
		 * and returning it wholesale left those strings untranslated until
		 * post_modified moved. Re-translating only the gap keeps the repair
		 * cheap: an editor's request re-stores the complete set once, and a
		 * visitor's request, which may not write the cache, pays for the few
		 * missing strings rather than for the whole page.
		 *
		 * The cache is deliberately not written on behalf of a requester without
		 * the capability: that would let any visitor overwrite an editor's entry
		 * with translations of text of their own choosing.
		 *
		 * @since 2.2.0
		 */
		$translation = array_merge( $cached_translation, $this->handle_translation_without_object_id( $untranslated_text, $target_lang, $source_lang ) );

		if ( ! $cache ) {
			$this->repository->increment_uncached_request_count( $object_id, $target_lang );
		}

		if ( $cache ) {
			$this->repository->store_translation( $object_id, $target_lang, $translation, $source_lang );
		}

		return $translation;
	}

	/**
	 * @since 0.0.1
	 */
	public function handle_translation_without_object_id( array $text, string $target_lang, string $source_lang = '' ): array
	{
		$translation = DeeplService::get_instance()->translate( $text, $target_lang, $source_lang );

		return $translation;
	}

	/**
	 * Returns the requested strings a cached entry does not already cover.
	 *
	 * A cached entry is a list of array( 'text' => original, 'translation' =>
	 * translated ), so membership is decided on the stored originals. Duplicates
	 * in the request collapse, because paying twice for the same string helps
	 * nobody.
	 *
	 * An empty return is exactly the condition under which this request costs
	 * nothing, so RestAPIController calls this to decide whether the rate limiter
	 * applies. It is public and static for that reason: both sides must answer
	 * the question the same way, and a pure function of the same cache entry and
	 * the same requested strings cannot answer it differently.
	 *
	 * @since 2.2.0
	 */
	public static function untranslated_text( array $cached_translation, array $text ): array
	{
		$cached_text = array_column( $cached_translation, 'text' );

		return array_values(
			array_filter(
				array_unique( $text ),
				function ( $value ) use ( $cached_text ) {
					return ! in_array( $value, $cached_text, true );
				}
			)
		);
	}

	/**
	 * @since 2.0.0
	 *
	 * @throws ObjectNotFoundException
	 */
	public function get_cached_translation( int $object_id, string $target_lang, string $source_lang = '' ): ?array
	{
		return $this->repository->get_cached_translation( $object_id, $target_lang, $source_lang );
	}
}
