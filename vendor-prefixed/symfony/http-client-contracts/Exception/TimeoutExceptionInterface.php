<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Symfony\Contracts\HttpClient\Exception;

/**
 * When an idle timeout occurs.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
interface TimeoutExceptionInterface extends TransportExceptionInterface
{
}
