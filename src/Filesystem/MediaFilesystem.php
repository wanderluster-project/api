<?php

namespace App\Filesystem;

use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\File;
use League\Flysystem\Filesystem;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Ramsey\Uuid\Uuid;

class MediaFilesystem
{
    /**
     * @var string
     */
    protected $bucket;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * MediaFilesystem constructor.
     * @param S3Client $s3Client
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(S3Client $s3Client, ParameterBagInterface $parameterBag){
        $this->bucket= $parameterBag->get('wanderluster_s3_bucket');
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
     * @param File $file
     * @return array
     * @throws \League\Flysystem\FileExistsException
     */
    function saveImage(File $file){
        $mimeType = $file->getMimeType();
        $whitelisted = [
            'image/jpeg' => 'jpeg'
        ];
        if (!array_key_exists($mimeType, $whitelisted)) {
            throw new BadRequestHttpException(sprintf('Invalid MIME type: %s', $mimeType));
        }

        $fileSize = $file->getSize();
        $ext = $whitelisted[$file->getMimeType()];
        $uuid = Uuid::uuid4();
        $filename = $uuid . '.' . $ext;
        $stream = fopen($file->getRealPath(), 'r+');
        $s3Path = 'images/original/'.$filename;
        $this->filesystem->writeStream($s3Path, $stream);
        fclose($stream);

        return             [
            'status' => 'success',
                'uuid' => $uuid,
                'mime_type' => $mimeType,
                'file_size'=>$fileSize,
                'url' => 'https://'.$this->bucket.'.s3.amazonaws.com/'.$s3Path,
        ];
    }

    function deleteImage($uuid){

    }
}