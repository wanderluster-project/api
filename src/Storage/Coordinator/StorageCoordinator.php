<?php

namespace App\Storage\Coordinator;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use SplPriorityQueue;
use Symfony\Component\HttpFoundation\File\File;
use App\Storage\StorageInterface;

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
             * @var StorageInterface $adapter
             */
            if ($storage->isSupported($file)) {
                return $storage;
            }
        }
        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_MIMETYPE, $file->getMimeType()));
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
