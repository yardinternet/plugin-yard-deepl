<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Illuminate\Support\Facades;

/**
 * @method static \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\View\Factory addNamespace(string $namespace, string|array $hints)
 * @method static \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\View\View first(array $views, \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Support\Arrayable|array $data = [], array $mergeData = [])
 * @method static \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\View\Factory replaceNamespace(string $namespace, string|array $hints)
 * @method static \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\View\Factory addExtension(string $extension, string $engine, \Closure|null $resolver = null)
 * @method static \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\View\View file(string $path, array $data = [], array $mergeData = [])
 * @method static \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\View\View make(string $view, array $data = [], array $mergeData = [])
 * @method static array composer(array|string $views, \Closure|string $callback)
 * @method static array creator(array|string $views, \Closure|string $callback)
 * @method static bool exists(string $view)
 * @method static mixed share(array|string $key, $value = null)
 *
 * @see \YardDeepl\Vendor_Prefixed\Illuminate\View\Factory
 */
class View extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'view';
    }
}
