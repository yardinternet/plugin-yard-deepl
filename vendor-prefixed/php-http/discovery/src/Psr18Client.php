<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Http\Discovery;

use YardDeepl\Vendor_Prefixed\Psr\Http\Client\ClientInterface;
use YardDeepl\Vendor_Prefixed\Psr\Http\Message\RequestFactoryInterface;
use YardDeepl\Vendor_Prefixed\Psr\Http\Message\RequestInterface;
use YardDeepl\Vendor_Prefixed\Psr\Http\Message\ResponseFactoryInterface;
use YardDeepl\Vendor_Prefixed\Psr\Http\Message\ResponseInterface;
use YardDeepl\Vendor_Prefixed\Psr\Http\Message\ServerRequestFactoryInterface;
use YardDeepl\Vendor_Prefixed\Psr\Http\Message\StreamFactoryInterface;
use YardDeepl\Vendor_Prefixed\Psr\Http\Message\UploadedFileFactoryInterface;
use YardDeepl\Vendor_Prefixed\Psr\Http\Message\UriFactoryInterface;

/**
 * A generic PSR-18 and PSR-17 implementation.
 *
 * You can create this class with concrete client and factory instances
 * or let it use discovery to find suitable implementations as needed.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class Psr18Client extends Psr17Factory implements ClientInterface
{
    private $client;

    public function __construct(
        ?ClientInterface $client = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?ResponseFactoryInterface $responseFactory = null,
        ?ServerRequestFactoryInterface $serverRequestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?UploadedFileFactoryInterface $uploadedFileFactory = null,
        ?UriFactoryInterface $uriFactory = null
    ) {
        parent::__construct($requestFactory, $responseFactory, $serverRequestFactory, $streamFactory, $uploadedFileFactory, $uriFactory);

        $this->client = $client ?? Psr18ClientDiscovery::find();
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
}
