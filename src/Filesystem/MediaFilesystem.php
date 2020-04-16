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
    const IMAGE_PATH_PREFIX = 'images/original';

    /**
     * @var string
     */
    protected $bucket;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $whitelistedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/svg+xml' => 'svg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];

    /**
     * MediaFilesystem constructor.
     * @param S3Client $s3Client
     * @param ParameterBagInterface $parameterBag
     */
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

    /**
     * @param File $file
     * @return array
     * @throws \League\Flysystem\FileExistsException
     */
    public function saveImage(File $file)
    {
        $mimeType = $file->getMimeType();
        if (!array_key_exists($mimeType, $this->whitelistedMimeTypes)) {
            throw new BadRequestHttpException(sprintf('Invalid MIME type: %s', $mimeType));
        }

        $fileSize = $file->getSize();
        $ext = $this->whitelistedMimeTypes[$file->getMimeType()];
        $uuid = Uuid::uuid4();
        $filename = $uuid . '.' . $ext;
        $stream = fopen($file->getRealPath(), 'r+');
        $s3Path = self::IMAGE_PATH_PREFIX . '/' . $filename;
        $this->filesystem->writeStream($s3Path, $stream);
        fclose($stream);

        return [
            'status' => 'success',
            'uuid' => $uuid,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'url' => 'https://' . $this->bucket . '.s3.amazonaws.com/' . $s3Path,
        ];
    }

    public function deleteImage($uuid)
    {
    }

    public function getPath($uuid)
    {
    }
}
