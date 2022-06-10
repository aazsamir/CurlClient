<?php

namespace Samir\CurlClient;

class Jwt implements JwtInterface
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

    public function __construct(string $secret, string $typ = 'JWT', string $alg = 'HS256')
    {
        $this->secret = $secret;
        $this->typ = $typ;
        $this->alg = $alg;
    }

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
