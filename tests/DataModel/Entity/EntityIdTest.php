<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Entity;

use App\DataModel\Entity\EntityId;
use App\Exception\WanderlusterException;
use Exception;
use PHPUnit\Framework\TestCase;

class EntityIdTest extends TestCase
{
    public function testConstructor(): void
    {
        $uuid = '9a1f64b2-47bf-441d-a9ac-47094e852af5';
        $sut = new EntityId($uuid);

        $this->assertEquals($uuid, $sut->getUuid());
    }

    public function testToString(): void
    {
        $uuid = 'dd25eacd-0ca9-4b26-87f8-7cceca184b03';
        $sut = new EntityId($uuid);

        $this->assertEquals($uuid, (string) $sut);
        $this->assertEquals($uuid, $sut->asString());
    }

    public function testInvalidEntityIdFormat(): void
    {
        try {
            new EntityId('kevin');
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Invalid EntityID format - kevin.', $e->getMessage());
        }

        try {
            new EntityId('01-01-kevin');
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Invalid EntityID format - 01-01-kevin.', $e->getMessage());
        }
    }
}
