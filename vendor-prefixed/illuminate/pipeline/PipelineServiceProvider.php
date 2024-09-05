<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Illuminate\Pipeline;

use YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Pipeline\Hub as PipelineHubContract;
use YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Support\DeferrableProvider;
use YardDeepl\Vendor_Prefixed\Illuminate\Support\ServiceProvider;

class PipelineServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            PipelineHubContract::class, Hub::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            PipelineHubContract::class,
        ];
    }
}
