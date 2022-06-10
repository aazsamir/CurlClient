<?php

namespace Samir\CurlClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Helper class for handling with Curls
 */
interface CurlInterface
{
    /**
     * Make new Curl instance.
     */
    public function __construct(RequestInterface $request);

    /**
     * Init Curl request and return self.
     */
    public function init(): self;

    /**
     * Send request and return Response instance.
     */
    public function send(): ResponseInterface;
}
