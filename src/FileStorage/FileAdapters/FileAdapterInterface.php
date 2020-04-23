<?php

declare(strict_types=1);

namespace App\FileStorage\FileAdapters;

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
     * Move a local file to a remote file system.
     *
     * @return string[]
     */
    public function saveFileToRemote(File $file): array;

    /**
     * Generate a URL for a given EntityId.
     */
    public function generateFileUrl(EntityId $entityId): string;

    /**
     * Delete a file for a given EntityId.
     */
    public function deleteRemoteFile(EntityId $entityId): void;
}
