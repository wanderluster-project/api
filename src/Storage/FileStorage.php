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
        $mimeType = $file->getMimeType();
        $adapter = $this->getStorageAdapter($mimeType);
        return $adapter->saveFile($file);
    }

    public function isSupported($mimeType): bool
    {
        try {
            $adapter = $this->getStorageAdapter($mimeType);
        } catch (WanderlusterException $e) {
            $adapter = null;
        }
        return !is_null($adapter);
    }

    public function archiveFile(File $file)
    {
        $mimeType = $file->getMimeType();
        $adapter = $this->getStorageAdapter($mimeType);
        return $adapter->archiveFile($file);
    }

    public function generateFileUrl(File $file): string
    {
        $mimeType = $file->getMimeType();
        $adapter = $this->getStorageAdapter($mimeType);
        return $adapter->generateFileUrl($file);
    }

    protected function getStorageAdapter($mimeType): StorageInteraface
    {
        foreach ($this->storageAdapters as $adapter) {
            if ($adapter->isSupported($mimeType)) {
                return $adapter;
            }
        }
        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_MIMETYPE));
    }
}
