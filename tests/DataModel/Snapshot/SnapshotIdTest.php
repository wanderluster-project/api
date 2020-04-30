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
        $snapshotId = new SnapshotId('79d20459-f79b-489d-97fd-22881fdf4fc9.en-100');
        $this->assertInstanceOf(EntityId::class, $snapshotId->getEntityId());
        $this->assertEquals('79d20459-f79b-489d-97fd-22881fdf4fc9', $snapshotId->getEntityId()->asString());
        $this->assertEquals('en', $snapshotId->getLanguage());
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
        $snapshotId = new SnapshotId('bc9884b3-a0a2-4ec9-ac65-c14b5cc2908c.en-100');
        $this->assertEquals('bc9884b3-a0a2-4ec9-ac65-c14b5cc2908c.en-100', (string) $snapshotId);
        $this->assertEquals('bc9884b3-a0a2-4ec9-ac65-c14b5cc2908c.en-100', $snapshotId->asString());
    }
}
