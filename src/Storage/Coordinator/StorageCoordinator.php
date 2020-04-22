<?php

declare(strict_types=1);

namespace App\Storage\Coordinator;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use App\RDF\Uuid;
use App\Storage\FileStorage\FileStorageInterface;
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
     * @throws WanderlusterException
     */
    public function getStorageForFile(File $file): FileStorageInterface
    {
        foreach ($this->storageSystems as $storage) {
            /**
             * @var FileStorageInterface $storage
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
     * @throws WanderlusterException
     */
    public function getStorageForEntityType(int $entityType): FileStorageInterface
    {
        foreach ($this->storageSystems as $storage) {
            /**
             * @var FileStorageInterface $storage
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
    public function getStorageForUuid(Uuid $uuid): FileStorageInterface
    {
        return $this->getStorageForEntityType($uuid->getEntityType());
    }

    public function register(FileStorageInterface $storage, int $priority): void
    {
        $this->storageSystems->insert($storage, $priority);
    }
}
