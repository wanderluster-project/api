<?php

declare(strict_types=1);

namespace App\Tests\Persistence\CouchDB;

use App\Persistence\CouchDB\Config;
use App\Persistence\CouchDB\Session;
use App\Tests\FunctionalTest;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request;

class SessionTest extends FunctionalTest
{
    public function testIsSessionValid(): void
    {
        $config = new Config();
        $config->username = 'admin';
        $config->password = 'passpass';
        $config->httpClient = new HttpClient();
        $config->sessionTimeout = 600;
        $config->host = 'couchdb';
        $config->protocol = 'http';
        $config->port = 5984;
        $sut = new Session($config);
        $this->assertTrue($sut->isSessionValid());
    }

    public function testInvalidUsername(): void
    {
        $config = new Config();
        $config->username = 'iDoNoExist';
        $config->password = 'passpass';
        $config->httpClient = new HttpClient();
        $config->sessionTimeout = 600;
        $config->host = 'couchdb';
        $config->protocol = 'http';
        $config->port = 5984;
        try {
            $sut = new Session($config);
            $this->fail('Exception not thrown.');
        } catch (Exception $e) {
            $this->assertEquals('Invalid CouchDB Session', $e->getMessage());
        }
    }

    public function testInvalidPassword(): void
    {
        $config = new Config();
        $config->username = 'admin';
        $config->password = 'iDoNoExist';
        $config->httpClient = new HttpClient();
        $config->sessionTimeout = 600;
        $config->host = 'couchdb';
        $config->protocol = 'http';
        $config->port = 5984;
        try {
            $sut = new Session($config);
            $this->fail('Exception not thrown.');
        } catch (Exception $e) {
            $this->assertEquals('Invalid CouchDB Session', $e->getMessage());
        }
    }

    public function testInvalidHost(): void
    {
        $config = new Config();
        $config->username = 'admin';
        $config->password = 'passpass';
        $config->httpClient = new HttpClient();
        $config->sessionTimeout = 600;
        $config->host = 'iDoNotExist';
        $config->protocol = 'http';
        $config->port = 5984;
        try {
            $sut = new Session($config);
            $this->fail('Exception not thrown.');
        } catch (Exception $e) {
            $this->assertEquals('Invalid CouchDB Session', $e->getMessage());
        }
    }

    public function testInvalidProtocol(): void
    {
        $config = new Config();
        $config->username = 'admin';
        $config->password = 'passpass';
        $config->httpClient = new HttpClient();
        $config->sessionTimeout = 600;
        $config->host = 'couchdb';
        $config->protocol = 'iDoNotExist';
        $config->port = 5984;
        try {
            $sut = new Session($config);
            $this->fail('Exception not thrown.');
        } catch (Exception $e) {
            $this->assertEquals('Invalid CouchDB Session', $e->getMessage());
        }
    }

    public function testInvalidPort(): void
    {
        $config = new Config();
        $config->username = 'admin';
        $config->password = 'passpass';
        $config->httpClient = new HttpClient();
        $config->sessionTimeout = 600;
        $config->host = 'couchdb';
        $config->protocol = 'http';
        $config->port = 4040;
        try {
            $sut = new Session($config);
            $this->fail('Exception not thrown.');
        } catch (Exception $e) {
            $this->assertEquals('Invalid CouchDB Session', $e->getMessage());
        }
    }

    public function testAddAuthentication(): void
    {
        $config = new Config();
        $config->username = 'admin';
        $config->password = 'passpass';
        $config->httpClient = new HttpClient();
        $config->sessionTimeout = 600;
        $config->host = 'couchdb';
        $config->protocol = 'http';
        $config->port = 5984;

        $sut = new Session($config);
        $request = new Request('GET', 'foo');
        $this->assertFalse($request->hasHeader('Cookie'));
        $request = $sut->addAuthentication($request);
        $this->assertTrue($request->hasHeader('Cookie'));
    }
}
