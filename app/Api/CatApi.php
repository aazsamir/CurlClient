<?php

namespace App\Api;

use Samir\CurlClient\Client;
use Samir\CurlClient\Jwt;

/**
 * Api for cat facts
 */
class CatApi
{
    /**
     * Get random cat fact
     */
    public function getCatFact(): ?array
    {
        $jwt = (new Jwt('secret'))->generate(['id_user' => 1]);

        $client = new Client('https://catfact.ninja/fact', null, [
            'Authorization' => 'Bearer ' . $jwt,
        ], 'GET');
        $data = $client->send();

        return $data;
    }
}
