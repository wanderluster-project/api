<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Snapshot;

use App\DataModel\Entity\EntityId;
use App\DataModel\Serializer\Serializer;
use App\DataModel\Snapshot\SnapshotId;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SnapshotIdTest extends WebTestCase
{
    public function testConstructor(): void
    {
        $snapshotId = new SnapshotId('10-3-3858f62230ac3c91.100');
        $this->assertInstanceOf(EntityId::class, $snapshotId->getEntityId());
        $this->assertEquals('10-3-3858f62230ac3c91', $snapshotId->getEntityId()->asString());
        $this->assertEquals(100, $snapshotId->getVersion());
    }

    public function testToString(): void
    {
        $snapshotId = new SnapshotId('10-3-3858f62230ac3c91.100');
        $this->assertEquals('10-3-3858f62230ac3c91.100', (string) $snapshotId);
        $this->assertEquals('10-3-3858f62230ac3c91.100', $snapshotId->asString());
    }

    public function testSerializing(): void
    {
        $snapshotId = new SnapshotId('10-3-3858f62230ac3c91.100');
        $this->assertEquals('10-3-3858f62230ac3c91.100', $this->getSerializer()->encode($snapshotId));
    }

    public function testDeserializing(): void
    {
        $snapshotId = $this->getSerializer()->decode('10-3-3858f62230ac3c91.100', SnapshotId::class);
        $this->assertInstanceOf(SnapshotId::class, $snapshotId);
        $this->assertEquals('10-3-3858f62230ac3c91', (string) $snapshotId->getEntityId());
        $this->assertEquals(100, $snapshotId->getVersion());
    }

    protected function getSerializer(): Serializer
    {
        self::bootKernel();

        return self::$container->get('test.serializer');
    }
}
