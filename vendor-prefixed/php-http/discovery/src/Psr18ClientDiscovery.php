<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Http\Discovery;

use YardDeepl\Vendor_Prefixed\Http\Discovery\Exception\DiscoveryFailedException;
use YardDeepl\Vendor_Prefixed\Http\Discovery\Exception\NotFoundException as RealNotFoundException;
use YardDeepl\Vendor_Prefixed\Psr\Http\Client\ClientInterface;

/**
 * Finds a PSR-18 HTTP Client.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class Psr18ClientDiscovery extends ClassDiscovery
{
    /**
     * Finds a PSR-18 HTTP Client.
     *
     * @return ClientInterface
     *
     * @throws RealNotFoundException
     */
    public static function find()
    {
        try {
            $client = static::findOneByType(ClientInterface::class);
        } catch (DiscoveryFailedException $e) {
            throw new RealNotFoundException('No PSR-18 clients found. Make sure to install a package providing "psr/http-client-implementation". Example: "php-http/guzzle7-adapter".', 0, $e);
        }

        return static::instantiateClass($client);
    }
}
