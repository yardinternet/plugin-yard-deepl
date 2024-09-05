<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Http\Discovery\Strategy;

use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Mock\Client as Mock;

/**
 * Find the Mock client.
 *
 * @author Sam Rapaport <me@samrapdev.com>
 */
final class MockClientStrategy implements DiscoveryStrategy
{
    public static function getCandidates($type)
    {
        if (is_a(HttpClient::class, $type, true) || is_a(HttpAsyncClient::class, $type, true)) {
            return [['class' => Mock::class, 'condition' => Mock::class]];
        }

        return [];
    }
}
