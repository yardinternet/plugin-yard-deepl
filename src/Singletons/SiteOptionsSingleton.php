<?php

namespace YardDeepl\Singletons;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * @since 0.0.1
 */
class SiteOptionsSingleton
{
	private static $instance = null;
	private array $options;

	private function __construct(array $options )
	{
		$this->options = $options;
	}

	private function __clone() {}
	private function __wakeup() {}

	/**
	 * @since 0.0.1
	 */
	public static function getInstance(array $options ): self
	{
		if (self::$instance == null) {
			self::$instance = new SiteOptionsSingleton( $options );
		}

		return self::$instance;
	}

	/**
	 * @since 0.0.1
	 */
	public function api_key(): string
	{
		return $this->options['ydpl_api_key'] ?? '';
	}

	/**
	 * @since 0.0.1
	 */
	public function configured_supported_languages(): array
	{
		return $this->options['ydpl_supported_target_languages'] ?? array();
	}
}
