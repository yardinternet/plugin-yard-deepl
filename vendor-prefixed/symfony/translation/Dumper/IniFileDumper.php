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

namespace YardDeepl\Vendor_Prefixed\Symfony\Component\Translation\Dumper;

use YardDeepl\Vendor_Prefixed\Symfony\Component\Translation\MessageCatalogue;

/**
 * IniFileDumper generates an ini formatted string representation of a message catalogue.
 *
 * @author Stealth35
 */
class IniFileDumper extends FileDumper
{
    /**
     * {@inheritdoc}
     */
    public function formatCatalogue(MessageCatalogue $messages, string $domain, array $options = [])
    {
        $output = '';

        foreach ($messages->all($domain) as $source => $target) {
            $escapeTarget = str_replace('"', '\"', $target);
            $output .= $source.'="'.$escapeTarget."\"\n";
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtension()
    {
        return 'ini';
    }
}
