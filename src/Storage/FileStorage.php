<?php

namespace App\Storage;

use App\Exception\WanderlusterException;
use App\Sharding\EntityType;
use App\Sharding\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use App\Storage\Coordinator\StorageCoordinator;

class FileStorage implements StorageInterface
{
    /**
     * @var StorageCoordinator
     */
    protected $storageCoordinator;

    public function __construct(StorageCoordinator $storageCoordinator)
    {
        $this->storageCoordinator = $storageCoordinator;
    }

    public function saveFile(File $file)
    {
        $storage = $this->storageCoordinator->getStorageForFile($file);
        return $storage->saveFile($file);
    }

    public function isSupportedFile(File $file): bool
    {
        try {
            $storage =$this->storageCoordinator->getStorageForFile($file);
        } catch (WanderlusterException $e) {
            $storage = null;
        }
        return !is_null($storage);
    }

    public function isSupportedEntityType(EntityType $entityType)
    {
        // TODO: Implement isSupportedEntityType() method.
    }


    public function archiveFile(File $file)
    {
        $storage = $this->storageCoordinator->getStorageForFile($file);
        return $storage->archiveFile($file);
    }

    public function generateFileUrl(Uuid $uuid): string
    {
//        $storage = $this->storageCoordinator->getStorageForFile($uuid);
//        return $storage->generateFileUrl($file);
    }
}
