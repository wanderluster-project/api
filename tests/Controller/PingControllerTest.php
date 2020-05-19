<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Security\JwtTokenUtilities;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PingControllerTest extends WebTestCase
{
    public function testNoJwt(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/ping');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"message":"Authentication Required"}', (string) $client->getResponse()->getContent());
    }

    public function testInvalidJwt(): void
    {
        $client = static::createClient();

        // Missing JWT
        $client->request('GET', '/api/v1/ping', [], [], ['HTTP_AUTHENTICATION' => 'Bearer:']);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"message":"Invalid JWT."}', (string) $client->getResponse()->getContent());

        // Malformed JWT
        $client->request('GET', '/api/v1/ping', [], [], ['HTTP_AUTHENTICATION' => 'Bearer: XXX']);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"message":"Invalid JWT."}', (string) $client->getResponse()->getContent());

        // Invalid JWT
        $client->request('GET', '/api/v1/ping', [], [], ['HTTP_AUTHENTICATION' => 'Bearer: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c']);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"message":"Invalid JWT."}', (string) $client->getResponse()->getContent());

        // Signed with Invalid Username
        $tokenUtilities = new JwtTokenUtilities();
        $jwt = $tokenUtilities->generate('invalidUser@not-exist.com');
        $client->request('GET', '/api/v1/ping', [], [], ['HTTP_AUTHENTICATION' => 'Bearer: '.$jwt]);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"message":"Invalid username - invalidUser@not-exist.com."}', (string) $client->getResponse()->getContent());
    }

    public function testSuccess(): void
    {
        $tokenUtilities = new JwtTokenUtilities();
        $jwt = $tokenUtilities->generate('simpkevin@gmail.com');

        $client = static::createClient();
        $client->request('GET', '/api/v1/ping', [], [], ['HTTP_AUTHENTICATION' => 'Bearer: '.$jwt]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
