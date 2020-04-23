<?php

declare(strict_types=1);

namespace App\FileStorage;

use App\DataModel\Entity\EntityId;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use App\FileStorage\FileAdapters\FileAdapterInterface;
use SplPriorityQueue;
use Symfony\Component\HttpFoundation\File\File;

class FileAdapterRegistry
{
    /**
     * @var SplPriorityQueue
     */
    protected $storageSystems;

    /**
     * FileAdapterCoordinator constructor.
     */
    public function __construct()
    {
        $this->storageSystems = new SplPriorityQueue();
    }

    /**
     * Given a file, selects the correct storage.
     *
     * @throws WanderlusterException
     */
    public function getFileAdapterForFile(File $file): FileAdapterInterface
    {
        foreach ($this->storageSystems as $storage) {
            /**
             * @var FileAdapterInterface $storage
             */
            if ($storage->isSupportedFile($file)) {
                return $storage;
            }
        }
        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_MIMETYPE, $file->getMimeType()));
    }

    /**
     * Given an Entity Type, selects the correct file adapter.
     *
     * @throws WanderlusterException
     */
    public function getFileAdapterForEntityType(int $entityType): FileAdapterInterface
    {
        foreach ($this->storageSystems as $storage) {
            /**
             * @var FileAdapterInterface $storage
             */
            if ($storage->isSupportedEntityType($entityType)) {
                return $storage;
            }
        }
        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_ENTITY_TYPE, $entityType));
    }

    /**
     * @throws WanderlusterException
     */
    public function getFileAdapterForEntityId(EntityId $entityId): FileAdapterInterface
    {
        return $this->getFileAdapterForEntityType($entityId->getEntityType());
    }

    public function register(FileAdapterInterface $storage, int $priority): void
    {
        $this->storageSystems->insert($storage, $priority);
    }
}
