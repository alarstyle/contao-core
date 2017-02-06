<?php

namespace Grow\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

abstract class Message implements MessageInterface
{
    /**
     * Protocol version
     *
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * Headers values
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Original header names
     *
     * @var array
     */
    protected $headerNames = [];

    /**
     * Body object
     *
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $body;

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        if ($this->protocolVersion === $version) {
            return $this;
        }

        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        $normalized = $this->normalizeHeaderName($name);

        return isset($this->headerNames[$normalized]);
    }

    public function getHeader($name)
    {
        $normalized = $this->normalizeHeaderName($name);

        if (!isset($this->headerNames[$normalized])) {
            return [];
        }

        $name = $this->headerNames[$normalized];

        return $this->headers[$name];
    }

    public function getHeaderLine($name)
    {
        return implode(',', $this->getHeader($name));
    }

    public function withHeader($name, $value)
    {
        $normalized = $this->normalizeHeaderName($name);
        $value = is_array($value) ? $value : [$value];

        $clone = clone $this;
        if (isset($clone->headerNames[$normalized])) {
            unset($clone->headers[$clone->headerNames[$normalized]]);
        }
        $clone->headerNames[$normalized] = $name;
        $clone->headers[$name] = $value;

        return $clone;
    }

    public function withAddedHeader($name, $value)
    {
        $normalized = $this->normalizeHeaderName($name);
        $value = is_array($value) ? $value : [$value];

        $clone = clone $this;
        if (isset($clone->headerNames[$normalized])) {
            $name = $this->headerNames[$normalized];
            $clone->headers[$name] = array_merge($this->headers[$name], $value);
        } else {
            $clone->headerNames[$normalized] = $name;
            $clone->headers[$name] = $value;
        }

        return $clone;
    }

    public function withoutHeader($name)
    {
        $normalized = $this->normalizeHeaderName($name);

        if (!isset($this->headerNames[$normalized])) {
            return $this;
        }

        $name = $this->headerNames[$normalized];

        $clone = clone $this;
        unset($clone->headers[$name], $clone->headerNames[$normalized]);

        return $clone;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        if ($body === $this->body) {
            return $this;
        }

        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    protected function normalizeHeaderName($name)
    {
        $name = strtr(strtolower($name), '_', '-');
        if (strpos($name, 'http-') === 0) {
            $name = substr($name, 5);
        }

        return $name;
    }
}
