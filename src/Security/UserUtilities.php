<?php

declare(strict_types=1);

namespace App\Security;

use ReflectionClass;
use Symfony\Component\Security\Core\User\UserInterface;

class UserUtilities
{
    /**
     * @param string $username
     *
     * @throws \ReflectionException
     */
    public function setUsername(UserInterface $user, $username): void
    {
        $reflection = new ReflectionClass(User::class);
        $prop = $reflection->getProperty('username');
        $prop->setAccessible(true);
        $prop->setValue($user, $username);
    }

    /**
     * @param string $password
     *
     * @throws \ReflectionException
     */
    public function setPassword(UserInterface $user, $password): void
    {
        $reflection = new ReflectionClass(User::class);
        $prop = $reflection->getProperty('password');
        $prop->setAccessible(true);
        $prop->setValue($user, $password);
    }

    /**
     * @throws \ReflectionException
     */
    public function setRoles(UserInterface $user, array $roles): void
    {
        $reflection = new ReflectionClass(User::class);
        $prop = $reflection->getProperty('roles');
        $prop->setAccessible(true);
        $prop->setValue($user, $roles);
    }

    /**
     * @param string $role
     *
     * @throws \ReflectionException
     */
    public function addRole(UserInterface $user, $role): void
    {
        $roles = $user->getRoles();
        if (!in_array($role, $roles)) {
            $roles[] = $role;
            $this->setRoles($user, $roles);
        }
    }
}
