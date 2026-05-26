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
		$cached_translation = $cached_translation ?? $this->get_cached_translation( $object_id, $target_lang );

		if ( $cached_translation ) {
			return $cached_translation;
		}

		$translation = $this->handle_translation_without_object_id( $text, $target_lang );

		if ( $cache ) {
			$this->repository->store_translation( $object_id, $target_lang, $translation );
		}

		return $translation;
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
	 * @since NEXT
	 */
	public function get_cached_translation( int $object_id, string $target_lang ): ?array
	{
		try {
			return $this->repository->get_cached_translation( $object_id, $target_lang );
		} catch ( ObjectNotFoundException $e ) {
			return null;
		}
	}
}
