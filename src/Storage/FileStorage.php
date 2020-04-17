<?php

namespace App\Storage;

use App\Exception\WanderlusterException;
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

    public function isSupported(File $file): bool
    {
        try {
            $storage =$this->storageCoordinator->getStorageForFile($file);
        } catch (WanderlusterException $e) {
            $storage = null;
        }
        return !is_null($storage);
    }

    public function archiveFile(File $file)
    {
        $storage = $this->storageCoordinator->getStorageForFile($file);
        return $storage->archiveFile($file);
    }

    public function generateFileUrl($uuid): string
    {
//        $storage = $this->storageCoordinator->getStorageForFile($uuid);
//        return $storage->generateFileUrl($file);
    }
}
