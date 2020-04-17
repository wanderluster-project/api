<?php
/**
 * Created by PhpStorm.
 * User: simpkevin
 * Date: 4/17/20
 * Time: 2:50 PM
 */

namespace App\Storage\FileTypes;


use App\Sharding\EntityType;
use App\Sharding\Uuid;
use App\Storage\FileStorageInterface;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractFileStorage implements FileStorageInterface
{
    abstract public function isSupportedFile(File $file): bool;

    abstract public function isSupportedEntityType(EntityType $entityType);

    abstract public function saveFile(File $file);

    abstract public function archiveFile(File $file);

    abstract public function generateFileUrl(Uuid $uuid): string;

}