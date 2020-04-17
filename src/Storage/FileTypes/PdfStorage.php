<?php

namespace App\Storage\FileTypes;

use Symfony\Component\HttpFoundation\File\File;
use App\Storage\StorageInterface;

class PdfStorage implements StorageInterface
{
    public function isSupported(File $file): bool
    {
        return $file->getMimeType() === 'application/pdf';
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
