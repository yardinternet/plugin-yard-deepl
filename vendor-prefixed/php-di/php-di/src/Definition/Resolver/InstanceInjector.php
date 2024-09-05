<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YardDeepl\Vendor_Prefixed\DI\Definition\Resolver;

use YardDeepl\Vendor_Prefixed\DI\Definition\Definition;
use YardDeepl\Vendor_Prefixed\DI\Definition\InstanceDefinition;
use YardDeepl\Vendor_Prefixed\DI\DependencyException;
use YardDeepl\Vendor_Prefixed\Psr\Container\NotFoundExceptionInterface;

/**
 * Injects dependencies on an existing instance.
 *
 * @since 5.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class InstanceInjector extends ObjectCreator
{
    /**
     * Injects dependencies on an existing instance.
     *
     * @param InstanceDefinition $definition
     */
    public function resolve(Definition $definition, array $parameters = [])
    {
        try {
            $this->injectMethodsAndProperties($definition->getInstance(), $definition->getObjectDefinition());
        } catch (NotFoundExceptionInterface $e) {
            $message = sprintf(
                'Error while injecting dependencies into %s: %s',
                get_class($definition->getInstance()),
                $e->getMessage()
            );

            throw new DependencyException($message, 0, $e);
        }

        return $definition;
    }

    public function isResolvable(Definition $definition, array $parameters = []) : bool
    {
        return true;
    }
}
