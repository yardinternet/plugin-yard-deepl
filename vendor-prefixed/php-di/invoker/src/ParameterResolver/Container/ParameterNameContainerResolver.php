<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 16-December-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */ declare(strict_types=1);

namespace YDPL\Vendor_Prefixed\Invoker\ParameterResolver\Container;

use YDPL\Vendor_Prefixed\Invoker\ParameterResolver\ParameterResolver;
use YDPL\Vendor_Prefixed\Psr\Container\ContainerInterface;
use ReflectionFunctionAbstract;

/**
 * Inject entries from a DI container using the parameter names.
 */
class ParameterNameContainerResolver implements ParameterResolver
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @param ContainerInterface $container The container to get entries from.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getParameters(
        ReflectionFunctionAbstract $reflection,
        array $providedParameters,
        array $resolvedParameters
    ): array {
        $parameters = $reflection->getParameters();

        // Skip parameters already resolved
        if (! empty($resolvedParameters)) {
            $parameters = array_diff_key($parameters, $resolvedParameters);
        }

        foreach ($parameters as $index => $parameter) {
            $name = $parameter->name;

            if ($name && $this->container->has($name)) {
                $resolvedParameters[$index] = $this->container->get($name);
            }
        }

        return $resolvedParameters;
    }
}
