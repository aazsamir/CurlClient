<?php

namespace Samir\CurlClient;

use Furious\Psr7\Request;
use Furious\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class Client implements ClientInterface
{
    /**
     * Curl helper.
     *  - TODO: make CurlFacade
     */
    protected Curl $curl;

    /**
     * Request used to make cURL request.
     */
    protected RequestInterface $request;

    /**
     * Response from cURL request.
     */
    protected ResponseInterface $response;

    /**
     * Request URL.
     */
    protected string $url;

    /**
     * Request data.
     */
    protected mixed $data;

    /**
     * Request headers.
     */
    protected array $headers;

    /**
     * Request method.
     */
    protected string $method;

    public function __construct(string $url = '', mixed $data = null, array $headers = [], string $method = 'GET')
    {
        $this->url = $url;
        $this->data = $data;
        $this->headers = $headers;
        $this->method = $method;
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function data(mixed $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function headers(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function method(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function raw(): ResponseInterface
    {
        $this->request = $this->createRequest();
        $this->curl = new Curl($this->request);
        $this->curl->init();
        $this->response = $this->curl->send();
        $this->response->getBody()->rewind();

        return $this->response;
    }

    public function send(): null|string|array
    {
        $response = $this->raw();
        if (strpos($response->getHeader('Content-Type')[0], 'application/json') !== false) {
            try {
                $this->data = json_decode($this->parseResponseData($response) ?? '', true, 512, JSON_THROW_ON_ERROR);
                return $this->data;
            } catch (\JsonException $exception) {
                // nothing to do?
            }
        }

        $this->data = $this->parseResponseData($response);

        return $this->data;
    }

    public function json(): array
    {
        return json_decode($this->parseResponseData($this->raw()) ?? '', true, 512, JSON_THROW_ON_ERROR);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponseData(): mixed
    {
        $data = $this->parseResponseData($this->response);

        // return empty string as null for convenience
        return empty($data) ? null : $data;
    }

    /**
     * Parse Response data, and change empty string to null.
     */
    protected function parseResponseData(ResponseInterface $response): ?string
    {
        $response->getBody()->rewind();
        $data = $response->getBody()->getContents();

        // return empty string as null for convenience
        return empty($data) ? null : $data;
    }

    /**
     * Create request instance, to pass to Curl instance.
     */
    protected function createRequest(): RequestInterface
    {
        $body = null;

        //POST, PUT, and PATCH have body
        if ($this->isMethod('POST') || $this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $body = $this->data;
        }

        $request = new Request(
            $this->method,
            $this->createUri(),
            $this->headers,
            $body,
        );


        return $request;
    }

    /**
     * Create Uri instance, to pass to Request instance.
     */
    protected function createUri(): UriInterface
    {
        $uri = new Uri($this->url);

        // if method is GET, we add data as query params
        if ($this->isMethod('GET') && is_array($this->data)) {
            //merge query string, if some data were passed in url
            $query = $uri->getQuery();
            if ($query) {
                $query .= '&' . http_build_query($this->data);
            } else {
                $query = http_build_query($this->data);
            }
            $uri = $uri->withQuery($query);
        }

        return $uri;
    }

    /**
     * Check if request method is equal to given method.
     */
    protected function isMethod(string $method): bool
    {
        return strtoupper($this->method) === strtoupper($method);
    }
}
