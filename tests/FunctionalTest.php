<?php

declare(strict_types=1);

namespace App\Tests;

use App\DataModel\Serializer\Serializer;
use App\FileStorage\FileAdapters\ChainFileAdapter;
use App\Http\HttpClientFactory;
use App\Persistence\AttributeManager;
use App\Persistence\CouchDB\Client;
use App\Persistence\CouchDB\ClientFactory;
use App\Persistence\EntityManager;
use App\Security\JwtTokenUtilities;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FunctionalTest extends WebTestCase
{
    /**
     * @param string $username
     */
    protected static function getClient($username = null): KernelBrowser
    {
        $jwt = '';
        if ($username) {
            $tokenUtilities = new JwtTokenUtilities();
            $jwt = $tokenUtilities->generate($username);
        }

        return static::createClient([], ['HTTP_AUTHENTICATION' => 'Bearer: '.$jwt]);
    }

    protected function getSerializer(): Serializer
    {
        self::bootKernel();

        return self::$container->get('test.serializer');
    }

    protected function getFileAdapter(): ChainFileAdapter
    {
        self::bootKernel();

        return self::$container->get('test.file_adapter');
    }

    protected function getEntityManager(): EntityManager
    {
        self::bootKernel();

        return self::$container->get('test.entity_manager');
    }

    public function getAttributeMangager(): AttributeManager
    {
        self::bootKernel();

        return self::$container->get('test.attribute_manager');
    }

    public static function getCouchClientFactory(): ClientFactory
    {
        self::bootKernel();

        return self::$container->get('test.couch_db.client_factory');
    }

    public static function getHttpClientFactory(): HttpClientFactory
    {
        self::bootKernel();

        return self::$container->get('test.http_client_factory');
    }

    public static function getLogger(): LoggerInterface
    {
        self::bootKernel();

        return self::$container->get('logger');
    }

    public static function getCouchDbClient(): Client
    {
        $clientFactory = self::getCouchClientFactory();

        $protocol = self::$container->getParameter('couchdb.protocol');
        $host = self::$container->getParameter('couchdb.host');
        $port = (int) self::$container->getParameter('couchdb.port');
        $sessionTimeout = (int) self::$container->getParameter('couchdb.session_timeout');
        $username = self::$container->getParameter('couchdb.username');
        $password = self::$container->getParameter('couchdb.password');
        $httpClientFactory = self::getHttpClientFactory();
        $logger = self::getLogger();

        return $clientFactory->createWithParameters($protocol, $host, $port, $username, $password, $sessionTimeout, $httpClientFactory, $logger);
    }
}
