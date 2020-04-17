<?php

namespace App\Storage;

use Symfony\Component\HttpFoundation\File\File;
use App\Sharding\EntityType;
use App\Sharding\Uuid;

interface FileStorageInterface
{
    public function isSupportedFile(File $file):bool;

    public function isSupportedEntityType(EntityType $entityType);

    public function saveFile(File $file);

    public function archiveFile(File $file);

    public function generateFileUrl(Uuid $uuid): string;
}
