<?php

namespace YardDeepl\Traits;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

trait ErrorLog
{
	public function logError(string $message ): void
	{
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG) {
			return;
		}

		error_log( sprintf( 'Yard Deepl: %s', $message ) );
	}
}
