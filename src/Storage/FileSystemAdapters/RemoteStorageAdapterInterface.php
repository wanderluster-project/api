<?php

declare(strict_types=1);

namespace App\Storage\FileSystemAdapters;

interface RemoteStorageAdapterInterface
{
    /**
     * @param string $fromPath
     * @param string $toPath
     */
    public function pushLocalFileToRemote($fromPath, $toPath): void;

    /**
     * @param string $path
     */
    public function deleteRemoteFile($path): void;

    /**
     * @param string $path
     */
    public function generateFileUrl($path): string;
}
