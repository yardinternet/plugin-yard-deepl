<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 26-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YardDeepl\Vendor_Prefixed\DI\Definition\Source;

use YardDeepl\Vendor_Prefixed\DI\Definition\Exception\InvalidDefinition;
use YardDeepl\Vendor_Prefixed\DI\Definition\ObjectDefinition;

/**
 * Implementation used when autowiring is completely disabled.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class NoAutowiring implements Autowiring
{
    public function autowire(string $name, ?ObjectDefinition $definition = null) : ObjectDefinition|null
    {
        throw new InvalidDefinition(sprintf(
            'Cannot autowire entry "%s" because autowiring is disabled',
            $name
        ));
    }
}
