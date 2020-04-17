<?php

namespace App\Storage\FileStorage;

use App\Exception\WanderlusterException;
use App\Sharding\EntityType;
use App\Sharding\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use App\Storage\Coordinator\StorageCoordinator;

class GenericFileStorage implements FileStorageInterface
{
    /**
     * @var StorageCoordinator
     */
    protected $storageCoordinator;

    public function __construct(StorageCoordinator $storageCoordinator)
    {
        $this->storageCoordinator = $storageCoordinator;
    }

    public function saveFileToRemote(File $file)
    {
        $storage = $this->storageCoordinator->getStorageForFile($file);
        return $storage->saveFileToRemote($file);
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

    public function isSupportedEntityType(EntityType $entityType):bool
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
