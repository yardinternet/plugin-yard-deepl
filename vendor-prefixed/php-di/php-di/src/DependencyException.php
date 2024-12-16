<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 16-December-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YDPL\Vendor_Prefixed\DI;

use YDPL\Vendor_Prefixed\Psr\Container\ContainerExceptionInterface;

/**
 * Exception for the Container.
 */
class DependencyException extends \Exception implements ContainerExceptionInterface
{
}
