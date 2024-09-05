<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Illuminate\Events;

class InvokeQueuedClosure
{
    /**
     * Handle the event.
     *
     * @param  \YardDeepl\Vendor_Prefixed\Laravel\SerializableClosure\SerializableClosure  $closure
     * @param  array  $arguments
     * @return void
     */
    public function handle($closure, array $arguments)
    {
        call_user_func($closure->getClosure(), ...$arguments);
    }

    /**
     * Handle a job failure.
     *
     * @param  \YardDeepl\Vendor_Prefixed\Laravel\SerializableClosure\SerializableClosure  $closure
     * @param  array  $arguments
     * @param  array  $catchCallbacks
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed($closure, array $arguments, array $catchCallbacks, $exception)
    {
        $arguments[] = $exception;

        collect($catchCallbacks)->each->__invoke(...$arguments);
    }
}
