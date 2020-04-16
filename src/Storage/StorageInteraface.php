<?php

namespace App\Storage;

use Symfony\Component\HttpFoundation\File\File;

interface StorageInteraface
{
    public function isSupported($mimeType):bool;

    public function saveFile(File $file);

    public function archiveFile(File $file);

    public function generateFileUrl(File $file): string;
}
