<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YardDeepl\Vendor_Prefixed\DI\Definition\Resolver;

use YardDeepl\Vendor_Prefixed\DI\Definition\Definition;
use YardDeepl\Vendor_Prefixed\DI\Definition\Exception\InvalidDefinition;

/**
 * Resolves a definition to a value.
 *
 * @since 4.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface DefinitionResolver
{
    /**
     * Resolve a definition to a value.
     *
     * @param Definition $definition Object that defines how the value should be obtained.
     * @param array      $parameters Optional parameters to use to build the entry.
     *
     * @throws InvalidDefinition If the definition cannot be resolved.
     *
     * @return mixed Value obtained from the definition.
     */
    public function resolve(Definition $definition, array $parameters = []);

    /**
     * Check if a definition can be resolved.
     *
     * @param Definition $definition Object that defines how the value should be obtained.
     * @param array      $parameters Optional parameters to use to build the entry.
     */
    public function isResolvable(Definition $definition, array $parameters = []) : bool;
}
