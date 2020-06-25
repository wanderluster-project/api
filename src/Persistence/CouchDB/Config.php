<?php

declare(strict_types=1);

namespace App\Persistence\CouchDB;

use GuzzleHttp\Client as HttpClient;
use Psr\Log\LoggerInterface;

class Config
{
    public string $protocol;
    public string $host;
    public int $port;
    public string $username;
    public string $password;
    public int $sessionTimeout;
    public HttpClient $httpClient;
    public LoggerInterface $logger;

    public function getDBEndpoint(): string
    {
        return $this->protocol.'://'.$this->host.':'.$this->port;
    }
}
