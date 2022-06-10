<?php

namespace Samir\CurlClient;

/**
 * JWT generator.
 */
class Jwt
{
    /**
     * JWT typ
     */
    protected string $typ;

    /**
     * JWT algorithm
     */
    protected string $alg;

    /**
     * JWT algorithms to use.
     */
    protected array $algorithms = [
        'HS256' => 'sha256',
    ];

    /**
     * Secret for JWT generation.
     */
    protected string $secret;

    /**
     * Make new JWT instance.
     */
    public function __construct(string $secret, string $typ = 'JWT', string $alg = 'HS256')
    {
        $this->secret = $secret;
        $this->typ = $typ;
        $this->alg = $alg;
    }

    /**
     * Generate JWT.
     */
    public function generate(array $payload): string
    {
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256',
        ]);

        $payload = json_encode($payload);

        $base_header = $this->base64Url($header);
        $base_payload = $this->base64Url($payload);

        $signature = hash_hmac($this->algorithms[$this->alg], $base_header . '.' . $base_payload, $this->secret, true);
        $base_signature = $this->base64Url($signature);

        $jwt = $this->base64Url($base_header . '.' . $base_payload . '.' . $base_signature);
        return $jwt;
    }

    /**
     * Make URL friendly base64.
     */
    protected function base64Url(string $string): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
    }
}
