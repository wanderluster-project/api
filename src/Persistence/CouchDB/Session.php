<?php

declare(strict_types=1);

namespace App\Persistence\CouchDB;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use DateTimeImmutable;
use Exception;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;

class Session
{
    protected string $token;
    protected DateTimeImmutable $tokenTimeout;
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->generateSessionToken();
    }

    public function addAuthentication(RequestInterface $request): RequestInterface
    {
        if (!$this->isSessionValid()) {
            $this->generateSessionToken();
        }

        return $request->withHeader('Cookie', $this->token);
    }

    public function isSessionValid(): bool
    {
        $now = new DateTimeImmutable();

        return $now < $this->tokenTimeout;
    }

    public function generateSessionToken(): void
    {
        try {
            $response = $this->config->httpClient->post(
                $this->config->getDBEndpoint().'/_session',
                [
                    RequestOptions::JSON => [
                        'username' => $this->config->username,
                        'password' => $this->config->password,
                    ],
                ]
            );
            $cookieHeader = $response->getHeader('Set-Cookie');
            $cookieParts = explode(';', $cookieHeader[0]);
            $this->token = $cookieParts[0];
            $tokenTimeout = $this->config->sessionTimeout - 15;
            $this->tokenTimeout = new DateTimeImmutable($tokenTimeout.' seconds');
        } catch (Exception $e) {
            throw new WanderlusterException(ErrorMessages::COUCHDB_INVALID_SESSION, 0, $e);
        }
    }
}
