<?php

namespace App\Filesystem;

use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\File;
use League\Flysystem\Filesystem;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

class MediaFilesystem
{
    protected $filesystem;

    public function __construct(S3Client $s3Client){
        $adapter = new AwsS3Adapter(
            $s3Client,
            'bucket-name'
        );

        // The FilesystemOperator
        $this->filesystem = new Filesystem($adapter);
    }

    function saveFile(File $file){

    }

    function deleteFile($uuid){

    }
}