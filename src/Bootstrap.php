<?php

namespace YardDeepl;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YardDeepl\Providers\AssetsServiceProvider;
use YardDeepl\Providers\RestAPIServiceProvider;
use YardDeepl\Providers\SettingsServiceProvider;
use YardDeepl\Vendor_Prefixed\DI\ContainerBuilder;
use YardDeepl\Vendor_Prefixed\Psr\Container\ContainerInterface;

require_once __DIR__ . '/helpers.php';

class Bootstrap
{
	/**
	 * @since 0.0.1
	 */
	private static ContainerInterface $container;

	/**
	 * @since 0.0.1
	 */
	private array $providers;

	/**
	 * @since 0.0.1
	 */
	public function __construct()
	{
		self::$container = $this->build_container();
		$this->providers = $this->get_providers();
		$this->register_providers();
		$this->register_plugin_text_domain();
	}

	/**
	 * @since 0.0.1
	 */
	protected function build_container(): ContainerInterface
	{
		$builder = new ContainerBuilder();
		$builder->addDefinitions( YDPL_PLUGIN_DIR_PATH . 'config/php-di.php' );
		$builder->useAnnotations( true );
		$container = $builder->build();

		return $container;
	}

	/**
	 * @since 0.0.1
	 */
	protected function get_providers(): array
	{
		return array(
			new AssetsServiceProvider(),
			new SettingsServiceProvider(),
			new RestAPIServiceProvider(),
		);
	}

	/**
	 * @since 0.0.1
	 */
	protected function register_providers(): void
	{
		foreach ( $this->providers as $provider ) {
			$provider->register();
		}
	}

	/**
	 * @since 0.0.1
	 */
	protected function register_plugin_text_domain(): void
	{
		load_plugin_textdomain( YDPL_PLUGIN_NAME, false, YDPL_PLUGIN_NAME . '/languages/' );
	}

	/**
	 * @since 0.0.1
	 */
	public static function get_container(): ContainerInterface
	{
		return self::$container;
	}
}
