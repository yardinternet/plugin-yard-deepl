<?php

namespace YDPL\Exceptions;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;

/**
 * @since 0.0.1
 */
class ObjectNotFoundException extends Exception
{
}
