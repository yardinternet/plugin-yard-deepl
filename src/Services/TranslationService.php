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
	public function handle_translation( int $object_id, array $text, string $target_lang, bool $cache = false, ?array $cached_translation = null ): array
	{
		if ( 0 < $object_id ) {
			return $this->handle_translation_with_object_id( $object_id, $text, $target_lang, $cache, $cached_translation );
		}

		return $this->handle_translation_without_object_id( $text, $target_lang );
	}

	/**
	 * @since 0.0.1
	 */
	public function handle_translation_with_object_id( int $object_id, array $text, string $target_lang, bool $cache = false, ?array $cached_translation = null ): array
	{
		if ( null === $cached_translation ) {
			$cached_translation = $this->get_cached_translation( $object_id, $target_lang ) ?? array();
		}

		$missing_texts = $this->missing_texts( $text, $cached_translation );

		if ( array() === $missing_texts ) {
			return $this->translations_for( $text, $cached_translation );
		}

		$translations = $this->translations_by_text( $this->handle_translation_without_object_id( $missing_texts, $target_lang ) )
			+ $this->translations_by_text( $cached_translation );
		$merged       = array_map(
			fn ( $source, $translation ) => array(
				'text'        => (string) $source,
				'translation' => $translation,
			),
			array_keys( $translations ),
			$translations
		);

		if ( $cache ) {
			$this->repository->store_translation( $object_id, $target_lang, $merged );
		} else {
			$this->repository->increment_uncached_request_count( $object_id, $target_lang );
		}

		return $this->translations_for( $text, $merged );
	}

	/**
	 * @since 2.2.0
	 */
	public function missing_texts( array $text, array $cached_translation ): array
	{
		return array_values( array_unique( array_diff( $text, array_keys( $this->translations_by_text( $cached_translation ) ) ) ) );
	}

	/**
	 * @since 2.2.0
	 */
	public function translations_for( array $text, array $translation ): array
	{
		$translations = $this->translations_by_text( $translation );
		$known_texts  = array_filter( array_values( $text ), fn ( $source ) => isset( $translations[ $source ] ) );

		return array_values(
			array_map(
				fn ( $source ) => array(
					'text'        => $source,
					'translation' => $translations[ $source ],
				),
				$known_texts
			)
		);
	}

	protected function translations_by_text( array $translation ): array
	{
		return array_column( $translation, 'translation', 'text' );
	}

	/**
	 * @since 0.0.1
	 */
	public function handle_translation_without_object_id( array $text, string $target_lang ): array
	{
		$translation = DeeplService::get_instance()->translate( $text, $target_lang );

		return $translation;
	}

	/**
	 * @since 2.0.0
	 *
	 * @throws ObjectNotFoundException
	 */
	public function get_cached_translation( int $object_id, string $target_lang ): ?array
	{
		return $this->repository->get_cached_translation( $object_id, $target_lang );
	}
}
