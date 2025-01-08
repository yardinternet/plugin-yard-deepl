<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 08-January-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YDPL\Vendor_Prefixed\DI\Definition;

use YDPL\Vendor_Prefixed\Psr\Container\ContainerInterface;

/**
 * Definition of a value for dependency injection.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ValueDefinition implements Definition, SelfResolvingDefinition
{
    /**
     * Entry name.
     */
    private string $name = '';

    public function __construct(
        private mixed $value,
    ) {
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    public function getValue() : mixed
    {
        return $this->value;
    }

    public function resolve(ContainerInterface $container) : mixed
    {
        return $this->getValue();
    }

    public function isResolvable(ContainerInterface $container) : bool
    {
        return true;
    }

    public function replaceNestedDefinitions(callable $replacer) : void
    {
        // no nested definitions
    }

    public function __toString() : string
    {
        return sprintf('Value (%s)', var_export($this->value, true));
    }
}
