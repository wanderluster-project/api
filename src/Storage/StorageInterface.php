<?php

namespace App\Storage;

use Symfony\Component\HttpFoundation\File\File;

interface StorageInterface
{
    public function isSupported(File $file):bool;

    public function saveFile(File $file);

    public function archiveFile(File $file);

    public function generateFileUrl($uuid): string;
}
