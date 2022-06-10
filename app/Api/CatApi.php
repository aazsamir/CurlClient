<?php

namespace App\Api;

use Samir\CurlClient\Client;

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
        $client = new Client('https://catfact.ninja/fact', [], [], 'GET');
        $data = $client->send();

        return $data;
    }
}
