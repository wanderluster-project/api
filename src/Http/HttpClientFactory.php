<?php

declare(strict_types=1);

namespace App\Http;

use GuzzleHttp\Client;

class HttpClientFactory
{
    /**
     * @var Client[]
     */
    protected $clients = [];

    /**
     * @return Client
     */
    public function create(string $namespace)
    {
        if (isset($this->clients[$namespace])) {
            return $this->clients[$namespace];
        }

        $client = new Client();
        $this->clients[$namespace] = $client;

        return $client;
    }
}
