<?php

declare(strict_types=1);

namespace App\FileStorage\FileAdapters;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use Symfony\Component\HttpFoundation\File\File;

interface FileAdapterInterface
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
     * Return TRUE if the file extension is supported, FALSE otherwise.
     *
     * @param string $ext
     */
    public function isSupportedExt($ext): bool;

    /**
     * Move a local file to a remote file system.
     */
    public function saveFileToRemote(File $file): Entity;

    /**
     * Generate a URL for a given EntityId.
     *
     * @param string $ext
     */
    public function generateFileUrl(EntityId $entityId, $ext): string;

    /**
     * Delete a file for a given EntityId.
     *
     * @param string $ext
     */
    public function deleteRemoteFile(EntityId $entityId, $ext): void;
}
