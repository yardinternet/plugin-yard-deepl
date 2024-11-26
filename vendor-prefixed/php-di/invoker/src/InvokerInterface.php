<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 26-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */ declare(strict_types=1);

namespace YardDeepl\Vendor_Prefixed\Invoker;

use YardDeepl\Vendor_Prefixed\Invoker\Exception\InvocationException;
use YardDeepl\Vendor_Prefixed\Invoker\Exception\NotCallableException;
use YardDeepl\Vendor_Prefixed\Invoker\Exception\NotEnoughParametersException;

/**
 * Invoke a callable.
 */
interface InvokerInterface
{
    /**
     * Call the given function using the given parameters.
     *
     * @param callable|array|string $callable Function to call.
     * @param array $parameters Parameters to use.
     * @return mixed Result of the function.
     * @throws InvocationException Base exception class for all the sub-exceptions below.
     * @throws NotCallableException
     * @throws NotEnoughParametersException
     */
    public function call($callable, array $parameters = []);
}
