<?php

declare(strict_types=1);

namespace App\Security;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use Exception;
use Firebase\JWT\JWT;

class JwtTokenUtilities
{
    const JWT_ISS = 'wanderluster.dev';
    const JWT_AUD = 'wanderluster.dev';

    /**
     * @param string $username
     */
    public function generate($username): string
    {
        $iat = time();
        $payload = [
            'iss' => self::JWT_ISS,
            'aud' => self::JWT_AUD,
            'iat' => $iat,
            'exp' => $iat + 5 * 60,
            'username' => $username,
        ];

        return JWT::encode($payload, $this->getPrivateKey(), 'RS256');
    }

    /**
     * @param string $jwt
     */
    public function decode($jwt): array
    {
        return (array) JWT::decode($jwt, $this->getPublicKey(), ['RS256']);
    }

    /**
     * @param string $jwt
     */
    public function isValid($jwt): bool
    {
        if (!$jwt) {
            return false;
        }

        $jwt = trim($jwt);

        try {
            $data = (array) JWT::decode($jwt, $this->getPublicKey(), ['RS256']);
            if (!isset($data['iss']) || self::JWT_ISS !== $data['iss']) {
                return false;
            }
            if (!isset($data['aud']) || self::JWT_AUD !== $data['aud']) {
                return false;
            }
            if (!isset($data['username'])) {
                return false;
            }
            if (!isset($data['iat'])) {
                return false;
            }
            if (!isset($data['exp'])) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return string
     *
     * @throws WanderlusterException
     */
    protected function getPrivateKey()
    {
        $contents = file_get_contents(__DIR__.'/../../config/certs/jwt.key');
        if (!$contents) {
            throw new WanderlusterException(ErrorMessages::JWT_KEYS_MISSING);
        }

        return $contents;
    }

    /**
     * @return string
     *
     * @throws WanderlusterException
     */
    protected function getPublicKey()
    {
        $contents = file_get_contents(__DIR__.'/../../config/certs/jwt.cert');

        if (!$contents) {
            throw new WanderlusterException(ErrorMessages::JWT_KEYS_MISSING);
        }

        return $contents;
    }
}
