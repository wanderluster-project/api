<?php

namespace App\Storage;

use App\Sharding\Types;
use App\Sharding\UuidFactory;
use Aws\S3\S3Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\File;
use League\Flysystem\Filesystem;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImageStorage implements StorageInteraface
{
    const IMAGE_PATH_PREFIX = 'images/original';

    protected $mimeTypes = [
        'image/jpeg' => 'jpg',
        'image/svg+xml' => 'svg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];

    /**
     * @var S3Client
     */
    protected $s3Client;

    /**
     * @var string
     */
    protected $bucket;

    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var UuidFactory
     */
    protected $uuidFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * ImageStorage constructor.
     * @param S3Client $s3Client
     * @param ParameterBagInterface $parameterBag
     * @param UuidFactory $uuidFactory
     */
    public function __construct(S3Client $s3Client, ParameterBagInterface $parameterBag, UuidFactory $uuidFactory)
    {
        $this->s3Client= $s3Client;
        $this->parameterBag = $parameterBag;
        $this->uuidFactory =$uuidFactory;

        $this->bucket = $parameterBag->get('wanderluster_s3_bucket');
        $adapter = new AwsS3Adapter(
            $s3Client,
            $this->bucket,
            "",
            ['ACL' => 'public-read']
        );

        // The FilesystemOperator
        $this->filesystem = new Filesystem($adapter);
        $this->uuidFactory = $uuidFactory;
    }

    public function isSupported($mimeType):bool
    {
        return in_array($mimeType, array_keys($this->mimeTypes));
    }

    public function saveFile(File $file)
    {
        $mimeType = $file->getMimeType();
        if (!array_key_exists($mimeType, $this->mimeTypes)) {
            throw new BadRequestHttpException(sprintf('Invalid MIME type: %s', $mimeType));
        }

        $fileSize = $file->getSize();
        $ext = $this->mimeTypes[$file->getMimeType()];
        $uuid = $this->uuidFactory->generateUUID($file->getFilename(), Types::FILE_IMAGE_JPG);
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

    public function archiveFile(File $file)
    {
    }

    public function generateFileUrl(File $file): string
    {
        // TODO: Implement generateFileUrl() method.
    }
}
