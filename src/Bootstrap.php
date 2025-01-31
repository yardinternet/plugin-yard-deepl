<?php

namespace YDPL;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YDPL\Providers\AssetsServiceProvider;
use YDPL\Providers\RestAPIServiceProvider;
use YDPL\Providers\SettingsServiceProvider;
use YDPL\Vendor_Prefixed\DI\ContainerBuilder;
use YDPL\Vendor_Prefixed\Psr\Container\ContainerInterface;

require_once __DIR__ . '/helpers.php';

/**
 * @since 0.0.1
 */
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
		add_action(
			'init',
			function () {
				$this->register_plugin_text_domain();
				self::$container = $this->build_container();
				$this->providers = $this->get_providers();
				$this->register_providers();
			}
		);
	}

	/**
	 * @since 0.0.1
	 */
	protected function build_container(): ContainerInterface
	{
		$builder = new ContainerBuilder();
		$builder->addDefinitions( YDPL_PLUGIN_DIR_PATH . 'config/php-di.php' );
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
