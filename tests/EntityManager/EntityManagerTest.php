<?php

declare(strict_types=1);

namespace App\Tests\EntityManager;

use App\DataModel\Attributes\Attributes;
use App\DataModel\Entity\Entity;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\WanderlusterException;
use App\Tests\FunctionalTest;

class EntityManagerTest extends FunctionalTest
{
    public function testCommit(): void
    {
        $sut = $this->getEntityManager();
        $entity = new Entity($this->getSerializer(), $this->getAttributeMangager(), 0, LanguageCodes::ENGLISH);
        $entity->set(Attributes::CORE_TEST_STRING, 'test');
        $this->assertNull($entity->getEntityId()->getUuid());
        $sut->commit($entity);
        $this->assertNotNull($entity->getEntityId()->getUuid());
    }

    public function testCommitException(): void
    {
        // Test Case: Invalid Language
        $sut = $this->getEntityManager();
        $entity = new Entity($this->getSerializer(), $this->getAttributeMangager(), 0, 'INVALID');
        $entity->set(Attributes::CORE_TEST_STRING, 'test');

        try {
            $sut->commit($entity);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid language code - INVALID.', $e->getMessage());
        }

        // Test Case: Invalid EntityType
        $sut = $this->getEntityManager();
        $entity = new Entity($this->getSerializer(), $this->getAttributeMangager(), -1);

        try {
            $sut->commit($entity);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid Entity Type - -1.', $e->getMessage());
        }
    }

    public function testCreate(): void
    {
        $sut = $this->getEntityManager();
        $this->assertInstanceOf(Entity::class, $sut->create());
    }
}
