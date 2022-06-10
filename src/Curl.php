<?php

namespace Samir\CurlClient;

use CurlHandle;
use Furious\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Curl implements CurlInterface
{
    /**
     * Request.
     */
    protected RequestInterface $request;

    /**
     * CurlHandle instance.
     */
    protected ?CurlHandle $curl;

    /**
     * Delimiter between headers.
     */
    protected const HEADER_DELIMITER = '; ';

    /**
     * Delimiter between header name and header value.
     */
    protected const HEADER_NAME_DELIMITER = ': ';

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function init(): self
    {
        $curl = curl_init();

        if (is_null($curl)) {
            // TODO: proper exception
            throw new \Exception('Curl initialization failed');
        }

        $options = [
            CURLOPT_URL => (string) $this->request->getUri(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
        ];

        // add body
        if ($this->isMethod('POST') || $this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $this->request->getBody()->rewind();
            $options[CURLOPT_POSTFIELDS] = $this->request->getBody()->getContents();
        }

        // set curl method
        switch ($this->method()) {
            case 'GET':
                $options[CURLOPT_HTTPGET] = true;
                break;

            case 'POST':
                $options[CURLOPT_POST] = true;
                break;

            case 'PUT':
                $options[CURLOPT_PUT] = true;
                break;

            default:
                $options[CURLOPT_CUSTOMREQUEST] = $this->method();
                break;
        }

        // add headers
        $options[CURLOPT_HTTPHEADER] = $this->getHeaders();
        curl_setopt_array($curl, $options);
        $this->curl = $curl;

        return $this;
    }

    public function send(): ResponseInterface
    {
        if (is_null($this->curl)) {
            // TODO: proper exception
            throw new \Exception('Curl is not initialized');
        }
        $response = curl_exec($this->curl);

        $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        $headers = $this->parseHeaders(substr($response, 0, $header_size));
        $body = substr($response, $header_size);
        curl_close($this->curl);
        $error = curl_error($this->curl);

        if ($error) {
            // TODO: proper exception
            throw new \Exception($error);
        }

        return $this->createResponse($status, $body, $headers);
    }

    /**
     * Create Response instance.
     */
    protected function createResponse(int $status, string $body, array $headers): ResponseInterface
    {
        return (new Response(
            $status,
            $headers,
            $body,
        ));
    }

    /**
     * Parse headers string to array.
     */
    protected function parseHeaders(string $string): array
    {
        $string = rtrim($string, "\r\n");
        $headers_lines = explode("\r\n", $string);
        $headers = [];
        $first = true;
        foreach ($headers_lines as $header_line) {
            // omit first line, as it is always status code
            if ($first) {
                $first = false;
                continue;
            }

            $header_line = explode(self::HEADER_NAME_DELIMITER, $header_line, 2);

            if (empty($header_line[0]) || empty($header_line[1])) {
                continue;
            }

            $headers[$header_line[0]] = $header_line[1];
        }

        return $headers;
    }

    /**
     * Get request method.
     */
    protected function method(): string
    {
        return strtoupper($this->request->getMethod());
    }

    /**
     * Check if request method is given method.
     */
    protected function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }

    /**
     * Get request headers as array for CurlHandle instance.
     */
    protected function getHeaders(): array
    {
        $headers = [];
        foreach ($this->request->getHeaders() as $name => $values) {
            $headers[] = $name . static::HEADER_NAME_DELIMITER . implode(static::HEADER_DELIMITER, $values);
        }

        return $headers;
    }
}
