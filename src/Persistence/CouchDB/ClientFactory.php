<?php

declare(strict_types=1);

namespace App\Persistence\CouchDB;

use App\Http\HttpClientFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ClientFactory
{
    protected Config $config;
    protected Client $client;
    protected bool $init = false;

    /**
     * ClientFactory constructor.
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        HttpClientFactory $httpClientFactory,
        LoggerInterface $logger
    ) {
        $this->config = new Config();
        $this->config->host = (string) $parameterBag->get('couchdb.host');
        $this->config->port = (int) $parameterBag->get('couchdb.port');
        $this->config->username = (string) $parameterBag->get('couchdb.username');
        $this->config->password = (string) $parameterBag->get('couchdb.password');
        $this->config->sessionTimeout = (int) $parameterBag->get('couchdb.session_timeout');
        $this->config->protocol = (string) $parameterBag->get('couchdb.protocol');
        $this->config->httpClient = $httpClientFactory->create('couchdb');
        $this->config->logger = $logger;
        $this->client = new Client($this->config);
    }

    public function build(): Client
    {
        return $this->client;
    }
}
