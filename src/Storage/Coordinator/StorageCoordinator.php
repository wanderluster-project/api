<?php

namespace App\Storage\Coordinator;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use App\Sharding\EntityType;
use App\Storage\StorageInterface;
use SplPriorityQueue;
use Symfony\Component\HttpFoundation\File\File;

class StorageCoordinator
{
    /**
     * @var SplPriorityQueue
     */
    protected $storageSystems;

    /**
     * StorageCoordinator constructor.
     */
    public function __construct()
    {
        $this->storageSystems = new SplPriorityQueue();
    }

    /**
     * Given a file, selects the correct storage.
     *
     * @param File $file
     * @return StorageInterface
     * @throws WanderlusterException
     */
    public function getStorageForFile(File $file):StorageInterface
    {
        foreach ($this->storageSystems as $storage) {
            /**
             * @var StorageInterface $storage
             */
            if ($storage->isSupportedFile($file)) {
                return $storage;
            }
        }
        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_MIMETYPE, $file->getMimeType()));
    }

    /**
     * Given an Entity Type, selects the correct storage.
     *
     * @param EntityType $entityType
     * @return StorageInterface
     * @throws WanderlusterException
     */
    public function getStorageForEntityType(EntityType $entityType):StorageInterface
    {
        foreach ($this->storageSystems as $storage) {
            /**
             * @var StorageInterface $storage
             */
            if ($storage->isSupportedEntityType($entityType)) {
                return $storage;
            }
        }
        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_ENTITY_TYPE, $entityType->getId()));
    }

    /**
     * @param StorageInterface $storage
     * @param int $priority
     */
    public function register(StorageInterface $storage, int $priority)
    {
        $this->storageSystems->insert($storage, $priority);
    }
}
