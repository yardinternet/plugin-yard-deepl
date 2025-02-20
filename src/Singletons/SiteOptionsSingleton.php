<?php

namespace YDPL\Singletons;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 0.0.1
 */
class SiteOptionsSingleton
{
	private static $instance = null;
	private array $options;

	private function __construct( array $options )
	{
		$this->options = $options;
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
	public static function get_instance( array $options ): self
	{
		if ( null == self::$instance ) {
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

	/**
	 * @since 0.0.1
	 */
	public function rest_api_param_object_id_is_mandatory(): bool
	{
		$value = $this->options['ydpl_rest_api_param_object_id_is_mandatory'] ?? '';

		return 'on' === $value;
	}

	/**
	 * @since 0.0.1
	 */
	public function secure_mode_enabled(): bool
	{
		$value = $this->options['ydpl_secure_mode_enabled'] ?? '';

		return 'on' === $value;
	}
}
