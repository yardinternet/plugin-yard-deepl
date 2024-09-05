<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Illuminate\Support\Facades;

use YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Broadcasting\Factory as BroadcastingFactoryContract;

/**
 * @method static \Illuminate\Broadcasting\Broadcasters\Broadcaster channel(string $channel, callable|string  $callback, array $options = [])
 * @method static mixed auth(\Illuminate\Http\Request $request)
 * @method static \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Broadcasting\Broadcaster connection($name = null);
 * @method static void routes(array $attributes = null)
 * @method static \Illuminate\Broadcasting\BroadcastManager socket($request = null)
 *
 * @see \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Broadcasting\Factory
 */
class Broadcast extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BroadcastingFactoryContract::class;
    }
}
