<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 16-December-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YDPL\Vendor_Prefixed\DI\Definition;

use YDPL\Vendor_Prefixed\DI\Factory\RequestedEntry;

/**
 * Definition.
 *
 * @internal This interface is internal to PHP-DI and may change between minor versions.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface Definition extends RequestedEntry, \Stringable
{
    /**
     * Returns the name of the entry in the container.
     */
    public function getName() : string;

    /**
     * Set the name of the entry in the container.
     */
    public function setName(string $name) : void;

    /**
     * Apply a callable that replaces the definitions nested in this definition.
     */
    public function replaceNestedDefinitions(callable $replacer) : void;

    /**
     * Definitions can be cast to string for debugging information.
     */
    public function __toString() : string;
}
