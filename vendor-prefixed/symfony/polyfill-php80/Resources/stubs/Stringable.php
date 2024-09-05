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

if (\PHP_VERSION_ID < 80000) {
    interface Yard_Deepl_Vendor_Prefixed_Stringable
    {
        /**
         * @return string
         */
        public function __toString();
    }
}
