<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Illuminate\Support\Facades;

/**
 * @method static \YardDeepl\Vendor_Prefixed\Psr\Log\LoggerInterface channel(string $channel = null)
 * @method static \YardDeepl\Vendor_Prefixed\Psr\Log\LoggerInterface stack(array $channels, string $channel = null)
 * @method static \YardDeepl\Vendor_Prefixed\Psr\Log\LoggerInterface build(array $config)
 * @method static \Illuminate\Log\Logger withContext(array $context = [])
 * @method static \Illuminate\Log\Logger withoutContext()
 * @method static void alert(string $message, array $context = [])
 * @method static void critical(string $message, array $context = [])
 * @method static void debug(string $message, array $context = [])
 * @method static void emergency(string $message, array $context = [])
 * @method static void error(string $message, array $context = [])
 * @method static void info(string $message, array $context = [])
 * @method static void log($level, string $message, array $context = [])
 * @method static void notice(string $message, array $context = [])
 * @method static void warning(string $message, array $context = [])
 * @method static void write(string $level, string $message, array $context = [])
 * @method static void listen(\Closure $callback)
 *
 * @see \Illuminate\Log\Logger
 */
class Log extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'log';
    }
}
