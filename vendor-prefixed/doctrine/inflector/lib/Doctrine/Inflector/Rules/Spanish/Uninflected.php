<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YardDeepl\Vendor_Prefixed\Doctrine\Inflector\Rules\Spanish;

use YardDeepl\Vendor_Prefixed\Doctrine\Inflector\Rules\Pattern;

final class Uninflected
{
    /** @return Pattern[] */
    public static function getSingular(): iterable
    {
        yield from self::getDefault();
    }

    /** @return Pattern[] */
    public static function getPlural(): iterable
    {
        yield from self::getDefault();
    }

    /** @return Pattern[] */
    private static function getDefault(): iterable
    {
        yield new Pattern('lunes');
        yield new Pattern('rompecabezas');
        yield new Pattern('crisis');
    }
}
