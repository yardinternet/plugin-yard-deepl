<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 16-December-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YDPL\Vendor_Prefixed\DI\Definition\Source;

use YDPL\Vendor_Prefixed\DI\Definition\Exception\InvalidDefinition;
use YDPL\Vendor_Prefixed\DI\Definition\ObjectDefinition;

/**
 * Source of definitions for entries of the container.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface Autowiring
{
    /**
     * Autowire the given definition.
     *
     * @throws InvalidDefinition An invalid definition was found.
     */
    public function autowire(string $name, ?ObjectDefinition $definition = null) : ObjectDefinition|null;
}
