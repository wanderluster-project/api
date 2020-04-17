<?php

namespace App\Storage;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use Symfony\Component\HttpFoundation\File\File;

class FileStorage implements StorageInteraface
{
    /**
     * @var StorageInteraface[]
     */
    protected $storageAdapters = [];

    public function __construct(ImageStorage $imageStorage)
    {
        $this->storageAdapters[] = $imageStorage;
    }

    public function saveFile(File $file)
    {
        $adapter = $this->getStorageAdapter($file);
        return $adapter->saveFile($file);
    }

    public function isSupported(File $file): bool
    {
        try {
            $adapter = $this->getStorageAdapter($file);
        } catch (WanderlusterException $e) {
            $adapter = null;
        }
        return !is_null($adapter);
    }

    public function archiveFile(File $file)
    {
        $adapter = $this->getStorageAdapter($file);
        return $adapter->archiveFile($file);
    }

    public function generateFileUrl(File $file): string
    {
        $adapter = $this->getStorageAdapter($file);
        return $adapter->generateFileUrl($file);
    }

    protected function getStorageAdapter(File $file): StorageInteraface
    {
        foreach ($this->storageAdapters as $adapter) {
            if ($adapter->isSupported($file)) {
                return $adapter;
            }
        }
        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_MIMETYPE));
    }
}
