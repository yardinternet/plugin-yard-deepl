<?php

namespace YardDeepl\Singletons;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YardDeepl\Vendor_Prefixed\Jenssegers\Blade\Blade;

/**
 * @since 0.0.1
 */
class BladeSingleton
{
	private static $instance = null;
	private Blade $blade;

	private function __construct()
	{
		$views       = YDPL_PLUGIN_DIR_PATH . 'src/Views';
		$cache       = YDPL_PLUGIN_DIR_PATH . 'src/Views/cache';
		$this->blade = new Blade( $views, $cache );
	}

	private function __clone()
	{
	}

	private function __wakeup()
	{
	}

	/**
	 * @since 0.0.1
	 */
	public static function get_instance()
	{
		if (null == self::$instance) {
			self::$instance = new BladeSingleton();
		}

		return self::$instance;
	}

	/**
	 * @since 0.0.1
	 */
	public function render($view, $data = array() )
	{
		return $this->blade->render( $view, $data );
	}
}
