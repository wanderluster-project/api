<?php

declare(strict_types=1);

namespace App\Storage\FileSystemAdapters;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StorageAdapterFactory
{
    public static function create(S3StorageAdapter $s3StorageAdapter, TestingAdapter $testingAdapter, ParameterBagInterface $parameterBag): StorageAdapterInterface
    {
        $env = $parameterBag->get('kernel.environment');
        if ('test' === $env) {
            return $testingAdapter;
        } else {
            return $s3StorageAdapter;
        }
    }
}
