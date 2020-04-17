<?php

namespace App\Storage\FileSystemAdapters;

interface StorageAdapterInterface
{
    public function copyFromLocal($fromPath, $toPath);

    public function generateFileUrl($path);
}
