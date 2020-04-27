<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Entity;

use App\DataModel\Entity\EntityId;
use App\DataModel\Serializer\Serializer;
use App\Exception\WanderlusterException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EntityIdTest extends WebTestCase
{
    public function testConstructor(): void
    {
        $entityIdString = '10-3-'.substr(md5('foobar'), 0, 16);
        $sut = new EntityId($entityIdString);

        $this->assertEquals(10, $sut->getShard());
        $this->assertEquals(3, $sut->getEntityType());
        $this->assertEquals('3858f62230ac3c91', $sut->getIdentifier());
    }

    public function testToString(): void
    {
        $entityIdString = '10-3-'.substr(md5('foobar'), 0, 16);
        $sut = new EntityId($entityIdString);

        $this->assertEquals('10-3-3858f62230ac3c91', (string) $sut);
        $this->assertEquals('10-3-3858f62230ac3c91', $sut->asString());
    }

    public function testInvalidEntityIdFormat(): void
    {
        try {
            new EntityId('kevin');
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Invalid EntityID format - kevin', $e->getMessage());
        }

        try {
            new EntityId('01-01-kevin');
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Invalid EntityID format - 01-01-kevin', $e->getMessage());
        }
    }

    public function testSerializing(): void
    {
        $sut = new EntityId('10-3-3858f62230ac3c91');
        $this->assertEquals('10-3-3858f62230ac3c91', $this->getSerializer()->encode($sut));
    }

    public function testDeserializing(): void
    {
        $sut = $this->getSerializer()->decode('10-3-3858f62230ac3c91', EntityId::class);
        $this->assertInstanceOf(EntityId::class, $sut);
        $this->assertEquals(10, $sut->getShard());
        $this->assertEquals(3, $sut->getEntityType());
        $this->assertEquals('3858f62230ac3c91', $sut->getIdentifier());
    }

    protected function getSerializer(): Serializer
    {
        self::bootKernel();

        return self::$container->get('test.serializer');
    }
}
