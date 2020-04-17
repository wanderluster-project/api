<?php

namespace App\Storage\FileSystemAdapters;

interface StorageAdapterInterface
{
    public function saveFile($path, $resource);

    public function generateFileUrl($uuid);
}
