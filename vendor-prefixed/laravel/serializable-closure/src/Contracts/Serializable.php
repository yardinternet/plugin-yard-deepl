<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 26-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Laravel\SerializableClosure\Contracts;

interface Serializable
{
    /**
     * Resolve the closure with the given arguments.
     *
     * @return mixed
     */
    public function __invoke();

    /**
     * Gets the closure that got serialized/unserialized.
     *
     * @return \Closure
     */
    public function getClosure();
}
