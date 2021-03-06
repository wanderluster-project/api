<?php

declare(strict_types=1);

namespace App\Tests;

use App\DataModel\Attributes\AttributeManager;
use App\DataModel\EntityManager;
use App\DataModel\Serializer\Serializer;
use App\FileStorage\FileAdapters\ChainFileAdapter;
use App\Security\JwtTokenUtilities;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FunctionalTest extends WebTestCase
{
    public function testInterface(): void
    {
        $this->assertInstanceOf(WebTestCase::class, $this);
        $this->assertInstanceOf(TestCase::class, $this);
    }

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
}
