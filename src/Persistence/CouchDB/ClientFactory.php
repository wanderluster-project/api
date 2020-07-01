<?php

declare(strict_types=1);

namespace App\Persistence\CouchDB;

use App\Http\HttpClientFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ClientFactory
{
    public static function create(ParameterBagInterface $parameterBag, HttpClientFactory $httpClientFactory, LoggerInterface $logger): Client
    {
        $config = new Config();
        $config->host = (string) $parameterBag->get('couchdb.host');
        $config->port = (int) $parameterBag->get('couchdb.port');
        $config->username = (string) $parameterBag->get('couchdb.username');
        $config->password = (string) $parameterBag->get('couchdb.password');
        $config->sessionTimeout = (int) $parameterBag->get('couchdb.session_timeout');
        $config->protocol = (string) $parameterBag->get('couchdb.protocol');
        $config->httpClient = $httpClientFactory->create('couchdb');
        $config->logger = $logger;

        return new Client($config);
    }

    public static function createWithParameters(
        string $protocol,
        string $host,
        int $port,
        string $username,
        string $password,
        int $sessionTimeout,
        HttpClientFactory $httpClientFactory,
        LoggerInterface $logger): Client
    {
        $config = new Config();
        $config->protocol = $protocol;
        $config->host = $host;
        $config->port = $port;
        $config->username = $username;
        $config->password = $password;
        $config->sessionTimeout = $sessionTimeout;
        $config->httpClient = $httpClientFactory->create('couchdb');
        $config->logger = $logger;

        return new Client($config);
    }
}
