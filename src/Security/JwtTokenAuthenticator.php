<?php

declare(strict_types=1);

namespace App\Security;

use App\Exception\ErrorMessages;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtTokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var JwtTokenUtilities
     */
    protected $jwtTokenUtilties;

    /**
     * @var UserUtilities
     */
    protected $userUtilities;

    public function __construct(JwtTokenUtilities $jwtTokenUtilities, UserUtilities $userUtilities)
    {
        $this->jwtTokenUtilties = $jwtTokenUtilities;
        $this->userUtilities = $userUtilities;
    }

    public function supports(Request $request)
    {
        if (!$request->headers->has('Authentication')) {
            return false;
        }

        $authHeaders = $request->headers->get('Authentication');
        if (!preg_match('/Bearer:(.*)/', $authHeaders, $matches)) {
            return false;
        }

        return true;
    }

    public function getCredentials(Request $request)
    {
        $authHeaders = $request->headers->get('Authentication');
        preg_match('/Bearer:(.*)/', $authHeaders, $matches);

        return trim($matches[1]);
    }

    public function getUser($jwt, UserProviderInterface $userProvider)
    {
        try {
            $jwtData = $this->jwtTokenUtilties->decode($jwt);
            $username = $jwtData['username'];
        } catch (Exception $e) {
            throw new AuthenticationException(ErrorMessages::JWT_INVALID, Response::HTTP_UNAUTHORIZED, $e);
        }

        try {
            $user = $userProvider->loadUserByUsername($username);
            /*
             * @var User $user
             */
            $this->userUtilities->addRole($user, 'ROLE_API_USER');

            return $user;
        } catch (Exception $e) {
            throw new AuthenticationException(sprintf(ErrorMessages::INVALID_USERNAME, $username), Response::HTTP_UNAUTHORIZED, $e);
        }
    }

    public function checkCredentials($jwt, UserInterface $user)
    {
        return $this->jwtTokenUtilties->isValid($jwt);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => $exception->getMessage(),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // TODO: Implement onAuthenticationSuccess() method.
        return null;
    }

    public function supportsRememberMe()
    {
        // TODO: Implement supportsRememberMe() method.
        return false;
    }

    /**
     * Called when authentication is needed, but it's not sent.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
