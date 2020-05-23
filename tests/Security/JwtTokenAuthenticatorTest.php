<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Security\JwtTokenAuthenticator;
use App\Security\JwtTokenUtilities;
use App\Security\UserUtilities;
use App\Tests\FunctionalTest;
use Symfony\Component\HttpFoundation\Request;

class JwtTokenAuthenticatorTest extends FunctionalTest
{
    protected function getSut(): JwtTokenAuthenticator
    {
        $tokenUtilities = new JwtTokenUtilities();
        $userUtilities = new UserUtilities();

        return new JwtTokenAuthenticator($tokenUtilities, $userUtilities);
    }

    public function testSupports(): void
    {
        $sut = $this->getSut();

        // not supported
        $request = new Request();
        $this->assertFalse($sut->supports($request));

        // supported
        $request = new Request([], [], [], [], [], ['HTTP_AUTHENTICATION' => 'Bearer: ']);
        $this->assertTrue($sut->supports($request));
    }

    public function testGetCredentials(): void
    {
        $sut = $this->getSut();

        $request = new Request([], [], [], [], [], ['HTTP_AUTHENTICATION' => 'Bearer: FOO']);
        $this->assertEquals('FOO', $sut->getCredentials($request));
    }
}
