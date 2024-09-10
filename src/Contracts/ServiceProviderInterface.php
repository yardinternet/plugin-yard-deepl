<?php

namespace YardDeepl\Contracts;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 0.0.1
 */
interface ServiceProviderInterface
{
	/**
	 * @since 0.0.1
	 */
	public function register(): void;
}
