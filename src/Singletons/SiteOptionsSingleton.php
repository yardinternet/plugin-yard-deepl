<?php

namespace YDPL\Singletons;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YDPL\Clients\DeeplClient;

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
	 * @since 2.1.0
	 */
	public function base_url(): string
	{
		return $this->raw_base_url() ?: DeeplClient::DEFAULT_BASE_URL;
	}

	/**
	 * The base URL as actually stored, without falling back to the default.
	 *
	 * @since 2.1.0
	 */
	public function raw_base_url(): string
	{
		return $this->options['ydpl_base_url'] ?? '';
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
}
