<?php

declare(strict_types=1);

namespace App\FileStorage\FileAdapters;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use SplPriorityQueue;
use Symfony\Component\HttpFoundation\File\File;

class ChainFileAdapter implements FileAdapterInterface
{
    /**
     * @var SplPriorityQueue
     */
    protected $fileAdapters;

    /**
     * FileAdapterCoordinator constructor.
     */
    public function __construct()
    {
        $this->fileAdapters = new SplPriorityQueue();
    }

    /**
     * Register a file adapter.
     */
    public function register(FileAdapterInterface $storage, int $priority): void
    {
        $this->fileAdapters->insert($storage, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedFile(File $file): bool
    {
        try {
            $this->getFileAdapterForFile($file);

            return true;
        } catch (WanderlusterException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedEntityType(int $entityType): bool
    {
        try {
            $this->getFileAdapterForEntityType($entityType);

            return true;
        } catch (WanderlusterException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedExt($ext): bool
    {
        try {
            $this->getFileAdapterForExt($ext);

            return true;
        } catch (WanderlusterException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveFileToRemote(File $file): Entity
    {
        $adapter = $this->getFileAdapterForFile($file);

        return  $adapter->saveFileToRemote($file);
    }

    /**
     * {@inheritdoc}
     */
    public function generateFileUrl(EntityId $entityId, $ext): string
    {
        $adapter = $this->getFileAdapterForExt($ext);

        return   $adapter->generateFileUrl($entityId, $ext);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteRemoteFile(EntityId $entityId, $ext): void
    {
        $adapter = $this->getFileAdapterForExt($ext);
        $adapter->deleteRemoteFile($entityId, $ext);
    }

    /**
     * Given a file, selects the correct storage.
     *
     * @throws WanderlusterException
     */
    protected function getFileAdapterForFile(File $file): FileAdapterInterface
    {
        foreach ($this->fileAdapters as $adapter) {
            if ($adapter->isSupportedFile($file)) {
                return $adapter;
            }
        }
        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_MIMETYPE, $file->getMimeType()));
    }

    /**
     * Given an Entity Type, selects the correct file adapter.
     *
     * @throws WanderlusterException
     */
    protected function getFileAdapterForEntityType(int $entityType): FileAdapterInterface
    {
        foreach ($this->fileAdapters as $adapter) {
            if ($adapter->isSupportedEntityType($entityType)) {
                return $adapter;
            }
        }
        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_ENTITY_TYPE, $entityType));
    }

    /**
     * @param string $ext
     *
     * @return FileAdapterInterface|mixed
     *
     * @throws WanderlusterException
     */
    protected function getFileAdapterForExt($ext)
    {
        foreach ($this->fileAdapters as $adapter) {
            if ($adapter->isSupportedExt($ext)) {
                return $adapter;
            }
        }
        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_EXT, $ext));
    }
}
