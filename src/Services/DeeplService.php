<?php

namespace YardDeepl\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;
use YardDeepl\Clients\DeeplClient;
use YardDeepl\Singletons\SiteOptionsSingleton;

/**
 * @since 0.0.1
 */
class DeeplService
{
	private static $instance = null;
	protected SiteOptionsSingleton $options;
	protected DeeplClient $client;

	private function __construct()
	{
		$this->options = yard_deepl_resolve_from_container( 'ydpl.site_options' );
		$this->start_service();
	}

	private function __clone()
	{
	}

	public function __wakeup()
	{
	}

	/**
	 * @since 0.0.1
	 */
	public static function get_instance(): self
	{
		if ( null === self::$instance ) {
			self::$instance = new DeeplService();
		}

		return self::$instance;
	}

	/**
	 * @since 0.0.1
	 */
	private function start_service(): void
	{
		$this->client = new DeeplClient( $this->options->api_key() );
	}

	/**
	 * @since 0.0.1
	 *
	 * @throws Exception
	 */
	public function translate( array $text, string $target_lang ): array
	{
		$result = $this->client->translateText( $text, $target_lang );

		if ( ! $result ) {
			throw new Exception( 'Failed to translate text.' );
		}

		return $this->combine_result_with_initial_text( $result, $text );
	}

	/**
	 * @since 0.0.1
	 */
	protected function combine_result_with_initial_text( array $result, array $text ): array
	{
		$result = array_map(
			function ( $item ) use ( $text ) {
				return $item['text'] ?? '';
			},
			$result
		);

		return array_map(
			function ( $target_lang, $translated_to_lang ) {
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
