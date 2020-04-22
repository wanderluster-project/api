<?php

declare(strict_types=1);

namespace App\Storage\FileStorage;

use App\Exception\WanderlusterException;
use App\RDF\Uuid;
use App\Storage\Coordinator\StorageCoordinator;
use Symfony\Component\HttpFoundation\File\File;

class GenericFileStorage implements FileStorageInterface
{
    /**
     * @var StorageCoordinator
     */
    protected $storageCoordinator;

    /**
     * GenericFileStorage constructor.
     */
    public function __construct(StorageCoordinator $storageCoordinator)
    {
        $this->storageCoordinator = $storageCoordinator;
    }

    /**
     * {@inheritdoc}
     */
    public function saveFileToRemote(File $file): array
    {
        $storage = $this->storageCoordinator->getStorageForFile($file);

        return $storage->saveFileToRemote($file);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedFile(File $file): bool
    {
        try {
            $storage = $this->storageCoordinator->getStorageForFile($file);
        } catch (WanderlusterException $e) {
            $storage = null;
        }

        return !is_null($storage);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedEntityType(int $entityType): bool
    {
        $storage = $this->storageCoordinator->getStorageForEntityType($entityType);

        return $storage->isSupportedEntityType($entityType);
    }

    /**
     * {@inheritdoc}
     */
    public function generateFileUrl(Uuid $uuid): string
    {
        $storage = $this->storageCoordinator->getStorageForUuid($uuid);

        return $storage->generateFileUrl($uuid);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteRemoteFile(Uuid $uuid): void
    {
        $storage = $this->storageCoordinator->getStorageForUuid($uuid);

        $storage->deleteRemoteFile($uuid);
    }
}
