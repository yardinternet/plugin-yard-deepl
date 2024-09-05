<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Illuminate\Support\Facades;

use YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Auth\Access\Gate as GateContract;

/**
 * @method static \Illuminate\Auth\Access\Gate guessPolicyNamesUsing(callable $callback)
 * @method static \Illuminate\Auth\Access\Response authorize(string $ability, array|mixed $arguments = [])
 * @method static \Illuminate\Auth\Access\Response inspect(string $ability, array|mixed $arguments = [])
 * @method static \Illuminate\Auth\Access\Response allowIf(\Closure|bool $condition, string|null $message = null, mixed $code = null)
 * @method static \Illuminate\Auth\Access\Response denyIf(\Closure|bool $condition, string|null $message = null, mixed $code = null)
 * @method static \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Auth\Access\Gate after(callable $callback)
 * @method static \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Auth\Access\Gate before(callable $callback)
 * @method static \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Auth\Access\Gate define(string $ability, callable|string $callback)
 * @method static \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Auth\Access\Gate forUser(\YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Auth\Authenticatable|mixed $user)
 * @method static \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Auth\Access\Gate policy(string $class, string $policy)
 * @method static array abilities()
 * @method static bool allows(string $ability, array|mixed $arguments = [])
 * @method static bool any(iterable|string $abilities, array|mixed $arguments = [])
 * @method static bool check(iterable|string $abilities, array|mixed $arguments = [])
 * @method static bool denies(string $ability, array|mixed $arguments = [])
 * @method static bool has(string $ability)
 * @method static mixed getPolicyFor(object|string $class)
 * @method static mixed raw(string $ability, array|mixed $arguments = [])
 *
 * @see \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Auth\Access\Gate
 */
class Gate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return GateContract::class;
    }
}
