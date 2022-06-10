<?php

namespace App\Api;

use Samir\CurlClient\Client;

/**
 * Placeholder REST API
 */
class RestApi
{
    /**
     * Base URL of api endpoints
     */
    public string $base_url;

    /**
     * Bearer token
     */
    protected string $token;

    public function __construct()
    {
        $this->base_url = 'https://gorest.co.in/';
        // this token sadly have limited usage
        $this->token = '97fcd348f8d7d11ef079ebf4e62bdd7c8c49d46f7747d830557b5ef51940b084';
    }

    /**
     * Get users with pagination
     */
    public function get(int $page = 1): ?array
    {
        $client = (new Client())
            ->url($this->endpoint('/public/v2/users?q=x'))
            ->method('GET')
            ->data([
                'page' => $page,
            ]);

        $data = $client->send();

        return $data;
    }

    /**
     * Get user by id
     */
    public function getById($id): ?array
    {
        $client = (new Client())
            ->url($this->endpoint('public/v2/users/' . $id))
            ->method('GET');

        $data = $client->send();

        return $data;
    }

    /**
     * Create user
     */
    public function post($data): ?array
    {
        $client = (new Client())
            ->url($this->endpoint('public/v2/users'))
            ->method('POST')
            ->headers($this->prepareHeaders())
            ->data($data);

        $data = $client->send();

        return $data;
    }

    /**
     * Update user
     */
    public function put(int $id, array $data): ?array
    {
        $client = (new Client())
            ->url($this->endpoint('public/v2/users/' . $id))
            ->method('PUT')
            ->headers($this->prepareHeaders())
            ->data($data);

        $data = $client->send();

        return $data;
    }

    /**
     * Delete user
     */
    public function delete(int $id): ?array
    {
        $client = (new Client())
            ->url($this->endpoint('public/v2/users/' . $id))
            ->headers($this->prepareHeaders())
            ->method('DELETE');

        $data = $client->send();

        return $data;
    }

    /**
     * Update user
     */
    public function patch(int $id, array $data): ?array
    {
        $client = (new Client())
            ->url($this->endpoint('public/v2/users/' . $id))
            ->headers($this->prepareHeaders())
            ->method('PATCH')
            ->data($data);

        $data = $client->send();

        return $data;
    }

    /**
     * Prepare headers with bearer token
     */
    protected function prepareHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Make endpoint url
     */
    protected function endpoint(string $url)
    {
        return ($this->sanitizeUrl($this->base_url) . '/' . $this->sanitizeUrl($url));
    }

    /**
     * Sanitize URL and get off with the trailing slash
     */
    protected function sanitizeUrl(string $url)
    {
        return trim($url, "\t\n\r\0\x0B\\\/");
    }
}
