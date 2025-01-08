<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 08-January-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */ declare(strict_types=1);

namespace YDPL\Vendor_Prefixed\Invoker\ParameterResolver\Container;

use YDPL\Vendor_Prefixed\Invoker\ParameterResolver\ParameterResolver;
use YDPL\Vendor_Prefixed\Psr\Container\ContainerInterface;
use ReflectionFunctionAbstract;
use ReflectionNamedType;

/**
 * Inject entries from a DI container using the type-hints.
 */
class TypeHintContainerResolver implements ParameterResolver
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
            $parameterType = $parameter->getType();
            if (! $parameterType) {
                // No type
                continue;
            }
            if (! $parameterType instanceof ReflectionNamedType) {
                // Union types are not supported
                continue;
            }
            if ($parameterType->isBuiltin()) {
                // Primitive types are not supported
                continue;
            }

            $parameterClass = $parameterType->getName();
            if ($parameterClass === 'self') {
                $parameterClass = $parameter->getDeclaringClass()->getName();
            }

            if ($this->container->has($parameterClass)) {
                $resolvedParameters[$index] = $this->container->get($parameterClass);
            }
        }

        return $resolvedParameters;
    }
}
