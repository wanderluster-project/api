<?php

declare(strict_types=1);

namespace App\FileStorage\FileSystemAdapters;

use League\Flysystem\FilesystemInterface;

class TestingAdapter implements StorageAdapterInterface
{
    /**
     * @var FilesystemInterface
     */
    protected $fileSystem;

    /**
     * TestingAdapter constructor.
     */
    public function __construct(FilesystemInterface $defaultStorage)
    {
        $this->fileSystem = $defaultStorage;
    }

    /**
     * @param string $fromPath
     * @param string $toPath
     *
     * @throws \League\Flysystem\FileExistsException
     */
    public function pushLocalFileToRemote($fromPath, $toPath): void
    {
        $stream = fopen($fromPath, 'r+');
        $this->fileSystem->writeStream($toPath, $stream);
    }

    /**
     * @param string $path
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function deleteRemoteFile($path): void
    {
        $this->fileSystem->delete($path);
    }

    /**
     * @param string $path
     */
    public function generateFileUrl($path): string
    {
        return 'localhost/storage/default/'.$path;
    }
}
