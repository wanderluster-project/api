<?php

namespace App\Storage\FileSystemAdapters;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StorageAdapterFactory
{
    public function create(S3StorageAdapter $s3StorageAdapter, LocalAdapter $localAdapter, ParameterBagInterface $parameterBag):StorageAdapterInterface
    {
        return $s3StorageAdapter;
    }
}
