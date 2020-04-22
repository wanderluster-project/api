<?php

declare(strict_types=1);

namespace App\Storage\FileStorage;

use App\Sharding\EntityType;
use App\Sharding\Uuid;
use Symfony\Component\HttpFoundation\File\File;

interface FileStorageInterface
{
    /**
     * Return TRUE if File is supported, FALSE otherwise.
     */
    public function isSupportedFile(File $file): bool;

    /**
     * Return TRUE if the EntityType is supported, FALSE otherwise.
     */
    public function isSupportedEntityType(int $entityType): bool;

    /**
     * Move a local file to a remote file system.
     *
     * @return string[]
     */
    public function saveFileToRemote(File $file): array;

    /**
     * Generate a URL for a given UUID.
     */
    public function generateFileUrl(Uuid $uuid): string;

    /**
     * Delete a file for a given UUID.
     */
    public function deleteRemoteFile(Uuid $uuid): void;
}
