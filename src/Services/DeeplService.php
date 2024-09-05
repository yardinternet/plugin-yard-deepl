<?php

namespace YardDeepl\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use Exception;
use YardDeepl\Singletons\SiteOptionsSingleton;
use YardDeepl\Vendor_Prefixed\DeepL\Translator;

class DeeplService
{
	private static $instance = null;
	protected SiteOptionsSingleton $options;
	protected Translator $translator;

	private function __construct()
	{
		$this->options = yard_deepl_resolve_from_container( 'ydpl.site_options' );
		$this->startService();
	}

	private function __clone() {}
	private function __wakeup() {}

	/**
	 * @since 0.0.1
	 */
	public static function getInstance(): self
	{
		if (self::$instance === null) {
			self::$instance = new DeeplService();
		}

		return self::$instance;
	}

	/**
	 * @since 0.0.1
	 */
	private function startService(): void
	{
		$this->translator = new Translator( $this->options->api_key() );
	}

	/**
	 * @since 0.0.1
	 *
	 * @throws Exception
	 */
	public function translate(array $text, string $target_lang ): array
	{
		$result = $this->translator->translateText( $text, null, $target_lang );

		if ( ! $result) {
			throw new Exception( 'Failed to translate text.' );
		}

		return $this->combineResultWithText( $result, $text );
	}

	/**
	 * @since 0.0.1
	 */
	protected function combineResultWithText(array $result, array $text ): array
	{
		$result = array_map(
			function ($item ) use ($text ) {
				return $item->text ?? '';
			},
			$result
		);

		return array_map(
			function ($target_lang, $translated_to_lang ) {
				return array(
					'text'        => $target_lang,
					'translation' => $translated_to_lang,
				);
			},
			$text,
			$result
		);
	}
}
