<?php

namespace App\Storage\FileSystemAdapters;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class S3StorageAdapter
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(S3Client $s3Client, ParameterBagInterface $parameterBag)
    {
        $this->bucket = $parameterBag->get('wanderluster_s3_bucket');
        $adapter = new AwsS3Adapter(
            $s3Client,
            $this->bucket,
            "",
            ['ACL' => 'public-read']
        );

        // The FilesystemOperator
        $this->filesystem = new Filesystem($adapter);
    }

    public function writeStream($path, $resource, array $config = []){
        $this->filesystem->writeStream($path, $resource, $config);
    }
}