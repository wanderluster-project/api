<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Snapshot;

use App\DataModel\Entity\EntityId;
use App\DataModel\Snapshot\SnapshotId;
use App\Exception\WanderlusterException;
use Exception;
use PHPUnit\Framework\TestCase;

class SnapshotIdTest extends TestCase
{
    public function testConstructor(): void
    {
        $snapshotId = new SnapshotId('10-3-3858f62230ac3c91.100');
        $this->assertInstanceOf(EntityId::class, $snapshotId->getEntityId());
        $this->assertEquals('10-3-3858f62230ac3c91', $snapshotId->getEntityId()->asString());
        $this->assertEquals(100, $snapshotId->getVersion());
    }

    public function testInvalidSnapshotId(): void
    {
        try {
            $snapshotId = new SnapshotId('INVALID-SNAPSHOT-ID');
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Invalid SnapshotId format - INVALID-SNAPSHOT-ID', $e->getMessage());
        }
    }

    public function testToString(): void
    {
        $snapshotId = new SnapshotId('10-3-3858f62230ac3c91.100');
        $this->assertEquals('10-3-3858f62230ac3c91.100', (string) $snapshotId);
        $this->assertEquals('10-3-3858f62230ac3c91.100', $snapshotId->asString());
    }
}
