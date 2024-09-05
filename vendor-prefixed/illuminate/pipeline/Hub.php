<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Illuminate\Pipeline;

use Closure;
use YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Container\Container;
use YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Pipeline\Hub as HubContract;

class Hub implements HubContract
{
    /**
     * The container implementation.
     *
     * @var \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Container\Container|null
     */
    protected $container;

    /**
     * All of the available pipelines.
     *
     * @var array
     */
    protected $pipelines = [];

    /**
     * Create a new Hub instance.
     *
     * @param  \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Container\Container|null  $container
     * @return void
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }

    /**
     * Define the default named pipeline.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function defaults(Closure $callback)
    {
        return $this->pipeline('default', $callback);
    }

    /**
     * Define a new named pipeline.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return void
     */
    public function pipeline($name, Closure $callback)
    {
        $this->pipelines[$name] = $callback;
    }

    /**
     * Send an object through one of the available pipelines.
     *
     * @param  mixed  $object
     * @param  string|null  $pipeline
     * @return mixed
     */
    public function pipe($object, $pipeline = null)
    {
        $pipeline = $pipeline ?: 'default';

        return call_user_func(
            $this->pipelines[$pipeline], new Pipeline($this->container), $object
        );
    }

    /**
     * Get the container instance used by the hub.
     *
     * @return \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set the container instance used by the hub.
     *
     * @param  \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
