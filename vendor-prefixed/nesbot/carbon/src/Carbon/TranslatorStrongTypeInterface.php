<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Carbon;

use YardDeepl\Vendor_Prefixed\Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Mark translator using strong type from symfony/translation >= 6.
 */
interface TranslatorStrongTypeInterface
{
    public function getFromCatalogue(MessageCatalogueInterface $catalogue, string $id, string $domain = 'messages');
}
