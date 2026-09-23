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

		if ( array() === $untranslated_text ) {
			return $cached_translation;
		}

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
	 * @since NEXT
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
