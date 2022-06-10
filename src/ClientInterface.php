<?php

namespace Samir\CurlClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * PSR7 Client wrapper for cURL
 */
interface ClientInterface
{
    /**
     * Make new Client instance.
     */
    public function __construct(string $url = '', mixed $data = null, array $headers = [], string $method = 'GET');

    /**
     * Set URL and return self.
     */
    public function url(string $url): self;
    /**
     * Set data and return self.
     */
    public function data(mixed $data): self;
    /**
     * Set headers and return self.
     */
    public function headers(array $headers): self;
    /**
     * Set method and return self.
     */
    public function method(string $method): self;
    /**
     * Send request, and return raw Response
     */
    public function raw(): ResponseInterface;

    /**
     * Send request, and try to resolve response format
     */
    public function send(): null|string|array;

    /**
     * Send request, but expects json response and return an array.
     */
    public function json(): array;

    /**
     * Get response.
     */
    public function getResponse(): ResponseInterface;

    /**
     * Get request.
     */
    public function getRequest(): RequestInterface;

    /**
     * Get response data.
     */
    public function getResponseData(): mixed;
}
