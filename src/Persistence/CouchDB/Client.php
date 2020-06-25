<?php

declare(strict_types=1);

namespace App\Persistence\CouchDB;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use Exception;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LogLevel;

class Client
{
    protected array $dbs = [];
    protected Session $session;
    protected Config $config;

    /**
     * Client constructor.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->session = new Session($this->config);
    }

    public function isAvailable(): bool
    {
        try {
            $body = $this->get('/');
            if (!$body || !isset($body['couchdb'])) {
                return false;
            }

            return 'Welcome' === $body['couchdb'];
        } catch (Exception $e) {
            $this->config->logger->log(LogLevel::ERROR, sprintf(ErrorMessages::COUCHDB_ERROR, $e->getMessage()));

            return false;
        }
    }

    public function createDB(string $name): Database
    {
        $this->put('/'.$name);

        return $this->getDB($name);
    }

    public function getDB(string $name): Database
    {
        if (isset($this->dbs[$name])) {
            return $this->dbs[$name];
        }
        $this->dbs[$name] = new Database($name, $this);

        return $this->dbs[$name];
    }

    /**
     * @throws WanderlusterException
     */
    public function hasDB(string $name): bool
    {
        $data = $this->get('/_all_dbs');
        if (!$data) {
            throw new WanderlusterException(sprintf(ErrorMessages::COUCHDB_ERROR, 'Error accessing _all_dbs'));
        }

        return in_array($name, $data);
    }

    public function deleteDB(string $name): bool
    {
        $data = $this->delete('/'.$name);

        return isset($data['ok']) ? true : false;
    }

    /**
     * @throws WanderlusterException
     */
    public function get(string $path): array
    {
        $request = new Request('GET', $this->config->getDBEndpoint().$path);

        return $this->send($request);
    }

    /**
     * @throws WanderlusterException
     */
    public function post(string $path, array $data = []): array
    {
        $request = new Request('POST', $this->config->getDBEndpoint().$path, [], json_encode($data));

        return $this->send($request);
    }

    /**
     * @throws WanderlusterException
     */
    public function put(string $path, array $data = []): array
    {
        $request = new Request('PUT', $this->config->getDBEndpoint().$path, [], json_encode($data));

        return $this->send($request);
    }

    /**
     * @throws WanderlusterException
     */
    public function delete(string $path): array
    {
        $request = new Request('DELETE', $this->config->getDBEndpoint().$path);

        return $this->send($request);
    }

    protected function addAuthentication(RequestInterface $request): RequestInterface
    {
        if (!$this->session->isSessionValid()) {
            $this->session->generateSessionToken();
        }
        $request = $this->session->addAuthentication($request);

        return $request;
    }

    /**
     * @throws WanderlusterException
     */
    protected function send(RequestInterface $request): array
    {
        $request = $this->addAuthentication($request);
        $response = $this->config->httpClient->send($request);
        $body = json_decode((string) $response->getBody(), true);

        if (!$body) {
            throw new WanderlusterException(ErrorMessages::COUCHDB_JSON_ERROR);
        }

        return $body;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getSession(): Session
    {
        return $this->session;
    }
}
