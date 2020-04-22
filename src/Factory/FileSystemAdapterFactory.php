<?php

declare(strict_types=1);

namespace App\Factory;

use App\FileStorage\FileSystemAdapters\S3StorageAdapter;
use App\FileStorage\FileSystemAdapters\StorageAdapterInterface;
use App\FileStorage\FileSystemAdapters\TestingAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileSystemAdapterFactory
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
