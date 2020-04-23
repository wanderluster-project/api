<?php

declare(strict_types=1);

namespace App\FileStorage\FileAdapters;

use App\DataModel\Entity\EntityId;
use App\Exception\WanderlusterException;
use App\FileStorage\FileAdapterRegistry;
use Symfony\Component\HttpFoundation\File\File;

class GenericFileAdapter implements FileAdapterInterface
{
    /**
     * @var FileAdapterRegistry
     */
    protected $storageCoordinator;

    /**
     * GenericFileAdapter constructor.
     */
    public function __construct(FileAdapterRegistry $storageCoordinator)
    {
        $this->storageCoordinator = $storageCoordinator;
    }

    /**
     * {@inheritdoc}
     */
    public function saveFileToRemote(File $file): array
    {
        $storage = $this->storageCoordinator->getFileAdapterForFile($file);

        return $storage->saveFileToRemote($file);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedFile(File $file): bool
    {
        try {
            $storage = $this->storageCoordinator->getFileAdapterForFile($file);
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
        $storage = $this->storageCoordinator->getFileAdapterForEntityType($entityType);

        return $storage->isSupportedEntityType($entityType);
    }

    /**
     * {@inheritdoc}
     */
    public function generateFileUrl(EntityId $entityId): string
    {
        $storage = $this->storageCoordinator->getFileAdapterForEntityId($entityId);

        return $storage->generateFileUrl($entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteRemoteFile(EntityId $entityId): void
    {
        $storage = $this->storageCoordinator->getFileAdapterForEntityId($entityId);

        $storage->deleteRemoteFile($entityId);
    }
}
