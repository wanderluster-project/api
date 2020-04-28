<?php

declare(strict_types=1);

namespace App\Security;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    /**
     * @var UserUtilities
     */
    protected $userUtilites;

    public function __construct(UserUtilities $userUtilities)
    {
        $this->userUtilites = $userUtilities;
    }

    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @param string $username
     *
     * @throws WanderlusterException
     * @throws \ReflectionException|UsernameNotFoundException
     */
    public function loadUserByUsername($username): User
    {
        if ('simpkevin@gmail.com' !== $username) {
            throw new UsernameNotFoundException(sprintf(ErrorMessages::INVALID_USERNAME, $username));
        }

        // @todo pull actual user
        $user = new User();
        $this->userUtilites->setPassword($user, 'P@ssword123');
        $this->userUtilites->setUsername($user, 'simpkevin');

        return $user;

//        // Load a User object from your data source or throw UsernameNotFoundException.
//        // The $username argument may not actually be a username:
//        // it is whatever value is being returned by the getUsername()
//        // method in your User class.
//        throw new \Exception('TODO: fill in loadUserByUsername() inside '.__FILE__);
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     */
    public function refreshUser(UserInterface $user): User
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        // @todo pull actual user
        $user = new User();
        $this->userUtilites->setPassword($user, 'P@ssword123');
        $this->userUtilites->setUsername($user, 'simpkevin');

        return $user;

//        // Return a User object after making sure its data is "fresh".
//        // Or throw a UsernameNotFoundException if the user no longer exists.
//        throw new \Exception('TODO: fill in refreshUser() inside '.__FILE__);
    }

    /**
     * Tells Symfony to use this provider for this User class.
     *
     * @param string $class
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }

    /**
     * Upgrades the encoded password of a user, typically for using a better hash algorithm.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        throw new WanderlusterException(sprintf(ErrorMessages::METHOD_NOT_IMPLEMENTED, 'upgradePassword'));
        // TODO: when encoded passwords are in use, this method should:
        // 1. persist the new password in the user storage
        // 2. update the $user object with $user->setPassword($newEncodedPassword);
    }
}
