<?php

namespace App\Storage\FileTypes;

use App\Sharding\EntityType;
use Symfony\Component\HttpFoundation\File\File;
use App\Storage\FileStorageInterface;

class PdfStorage implements FileStorageInterface
{
    public function isSupportedFile(File $file): bool
    {
        return $file->getMimeType() === 'application/pdf';
    }

    public function isSupportedEntityType(EntityType $entityType):bool
    {
        // TODO: Implement isSupportedEntityType() method.
    }


    public function saveFile(File $file)
    {
        // TODO: Implement saveFile() method.
    }

    public function archiveFile(File $file)
    {
        // TODO: Implement archiveFile() method.
    }

    public function generateFileUrl($uuid): string
    {
        // TODO: Implement generateFileUrl() method.
    }
}
