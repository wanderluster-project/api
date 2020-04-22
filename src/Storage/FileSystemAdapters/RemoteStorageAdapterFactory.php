<?php

declare(strict_types=1);

namespace App\Storage\FileSystemAdapters;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RemoteStorageAdapterFactory
{
    public static function create(S3StorageAdapter $s3StorageAdapter, ParameterBagInterface $parameterBag): RemoteStorageAdapterInterface
    {
        return $s3StorageAdapter;
    }
}
