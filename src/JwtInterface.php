<?php

namespace Samir\CurlClient;

/**
 * JWT generator.
 */
interface JwtInterface
{
    /**
     * Make new JWT instance.
     */
    public function __construct(string $secret, string $typ = 'JWT', string $alg = 'HS256');

    /**
     * Generate JWT.
     */
    public function generate(array $payload): string;
}
