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

namespace YardDeepl\Vendor_Prefixed\Symfony\Component\HttpClient;

use YardDeepl\Vendor_Prefixed\Psr\Log\LoggerAwareInterface;
use YardDeepl\Vendor_Prefixed\Psr\Log\LoggerInterface;
use YardDeepl\Vendor_Prefixed\Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use YardDeepl\Vendor_Prefixed\Symfony\Contracts\HttpClient\HttpClientInterface;
use YardDeepl\Vendor_Prefixed\Symfony\Contracts\HttpClient\ResponseInterface;
use YardDeepl\Vendor_Prefixed\Symfony\Contracts\HttpClient\ResponseStreamInterface;
use YardDeepl\Vendor_Prefixed\Symfony\Contracts\Service\ResetInterface;

/**
 * Auto-configure the default options based on the requested URL.
 *
 * @author Anthony Martin <anthony.martin@sensiolabs.com>
 */
class ScopingHttpClient implements HttpClientInterface, ResetInterface, LoggerAwareInterface
{
    use HttpClientTrait;

    private $client;
    private $defaultOptionsByRegexp;
    private $defaultRegexp;

    public function __construct(HttpClientInterface $client, array $defaultOptionsByRegexp, ?string $defaultRegexp = null)
    {
        $this->client = $client;
        $this->defaultOptionsByRegexp = $defaultOptionsByRegexp;
        $this->defaultRegexp = $defaultRegexp;

        if (null !== $defaultRegexp && !isset($defaultOptionsByRegexp[$defaultRegexp])) {
            throw new InvalidArgumentException(sprintf('No options are mapped to the provided "%s" default regexp.', $defaultRegexp));
        }
    }

    public static function forBaseUri(HttpClientInterface $client, string $baseUri, array $defaultOptions = [], ?string $regexp = null): self
    {
        if (null === $regexp) {
            $regexp = preg_quote(implode('', self::resolveUrl(self::parseUrl('.'), self::parseUrl($baseUri))));
        }

        $defaultOptions['base_uri'] = $baseUri;

        return new self($client, [$regexp => $defaultOptions], $regexp);
    }

    /**
     * {@inheritdoc}
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $e = null;
        $url = self::parseUrl($url, $options['query'] ?? []);

        if (\is_string($options['base_uri'] ?? null)) {
            $options['base_uri'] = self::parseUrl($options['base_uri']);
        }

        try {
            $url = implode('', self::resolveUrl($url, $options['base_uri'] ?? null));
        } catch (InvalidArgumentException $e) {
            if (null === $this->defaultRegexp) {
                throw $e;
            }

            $defaultOptions = $this->defaultOptionsByRegexp[$this->defaultRegexp];
            $options = self::mergeDefaultOptions($options, $defaultOptions, true);
            if (\is_string($options['base_uri'] ?? null)) {
                $options['base_uri'] = self::parseUrl($options['base_uri']);
            }
            $url = implode('', self::resolveUrl($url, $options['base_uri'] ?? null, $defaultOptions['query'] ?? []));
        }

        foreach ($this->defaultOptionsByRegexp as $regexp => $defaultOptions) {
            if (preg_match("{{$regexp}}A", $url)) {
                if (null === $e || $regexp !== $this->defaultRegexp) {
                    $options = self::mergeDefaultOptions($options, $defaultOptions, true);
                }
                break;
            }
        }

        return $this->client->request($method, $url, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function stream($responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }

    public function reset()
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(LoggerInterface $logger): void
    {
        if ($this->client instanceof LoggerAwareInterface) {
            $this->client->setLogger($logger);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function withOptions(array $options): self
    {
        $clone = clone $this;
        $clone->client = $this->client->withOptions($options);

        return $clone;
    }
}
