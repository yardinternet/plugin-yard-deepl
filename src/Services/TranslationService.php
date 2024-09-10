<?php

namespace YardDeepl\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YardDeepl\Repositories\TranslationRepository;

/**
 * @since 0.0.1
 */
class TranslationService
{
	protected $repository;

	public function __construct()
	{
		$this->repository = new TranslationRepository();
	}

	/**
	 * @since 0.0.1
	 */
	public function handle_translation(int $object_id, array $text, string $target_lang )
	{
		$cached_translation = $this->repository->get_cached_translation( $object_id, $target_lang );

		if ($cached_translation) {
			return $cached_translation;
		}

		$translation = DeeplService::get_instance()->translate( $text, $target_lang );

		$this->repository->store_translation( $object_id, $target_lang, $translation );

		return $translation;
	}
}
