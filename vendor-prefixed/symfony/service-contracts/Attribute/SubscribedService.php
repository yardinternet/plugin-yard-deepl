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

namespace YardDeepl\Vendor_Prefixed\Symfony\Contracts\Service\Attribute;

use YardDeepl\Vendor_Prefixed\Symfony\Contracts\Service\ServiceSubscriberTrait;

/**
 * Use with {@see ServiceSubscriberTrait} to mark a method's return type
 * as a subscribed service.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Yard_Deepl_Vendor_Prefixed_Attribute(\Attribute::TARGET_METHOD)]
final class SubscribedService
{
    /**
     * @param string|null $key The key to use for the service
     *                         If null, use "ClassName::methodName"
     */
    public function __construct(
        public ?string $key = null
    ) {
    }
}
