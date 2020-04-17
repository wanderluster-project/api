<?php

namespace App\Storage\FileSystemAdapters;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class S3StorageAdapter implements StorageAdapterInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $bucket;

    /**
     * S3StorageAdapter constructor.
     * @param S3Client $s3Client
     * @param ParameterBagInterface $parameterBag
     * @throws WanderlusterException
     */
    public function __construct(S3Client $s3Client, ParameterBagInterface $parameterBag)
    {
        $this->bucket = (string)$parameterBag->get('wanderluster_s3_bucket');

        if (!$this->bucket) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_S3_BUCKET, $this->bucket));
        }

        $adapter = new AwsS3Adapter(
            $s3Client,
            $this->bucket,
            "",
            ['ACL' => 'public-read']
        );

        // The FilesystemOperator
        $this->filesystem = new Filesystem($adapter);
    }

    /**
     * @param $path
     * @param $resource
     * @throws \League\Flysystem\FileExistsException
     */
    public function saveFile($path, $resource)
    {
        $this->filesystem->writeStream($path, $resource);
    }

    /**
     * @param $path
     * @return string
     */
    public function generateFileUrl($path)
    {
        return 'https://' . $this->bucket . '.s3.amazonaws.com/' . $path;
    }
}
