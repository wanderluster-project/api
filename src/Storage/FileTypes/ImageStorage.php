<?php

namespace App\Storage\FileTypes;

use App\Sharding\EntityTypes;
use App\Sharding\UuidFactory;
use App\Storage\FileSystemAdapters\StorageAdapterInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Storage\StorageInterface;

class ImageStorage implements StorageInterface
{
    const IMAGE_PATH_PREFIX = 'images/original';

    protected $mimeTypes = [
        'image/jpeg' => [
            'ext' => 'jpg',
            'type' => EntityTypes::FILE_IMAGE_JPG
        ],
        'image/svg+xml' => [
            'ext' => 'svg',
            'type' => EntityTypes::FILE_IMAGE_SVG
        ],
        'image/png' => [
            'ext' => 'png',
            'type' => EntityTypes::FILE_IMAGE_PNG
        ],
        'image/gif' => [
            'ext' => 'gif',
            'type' => EntityTypes::FILE_IMAGE_GIF
        ],
        'image/webp' => [
            'ext' => 'webp',
            'type' => EntityTypes::FILE_IMAGE_WEBP
        ]
    ];

    /**
     * @var UuidFactory
     */
    protected $uuidFactory;

    /**
     * @var StorageAdapterInterface
     */
    protected $storageAdapter;

    /**
     * ImageStorage constructor.
     * @param StorageAdapterInterface $storageAdapter
     * @param UuidFactory $uuidFactory
     */
    public function __construct(StorageAdapterInterface $storageAdapter, UuidFactory $uuidFactory)
    {
        $this->storageAdapter = $storageAdapter;
        $this->uuidFactory = $uuidFactory;
    }

    public function isSupported(File $file): bool
    {
        $mimeType = $file->getMimeType();
        return in_array($mimeType, array_keys($this->mimeTypes));
    }

    public function saveFile(File $file)
    {
        $mimeType = $file->getMimeType();
        if (!array_key_exists($mimeType, $this->mimeTypes)) {
            throw new BadRequestHttpException(sprintf('Invalid MIME type: %s', $mimeType));
        }

        $fileSize = $file->getSize();
        $ext = $this->mimeTypes[$file->getMimeType()]['ext'];
        $type = $this->mimeTypes[$file->getMimeType()]['type'];
        $uuid = $this->uuidFactory->generateUUID($file->getFilename(), $type);
        $filename = $uuid . '.' . $ext;
        $stream = fopen($file->getRealPath(), 'r+');
        $s3Path = self::IMAGE_PATH_PREFIX . '/' . $filename;
        $this->storageAdapter->saveFile($s3Path, $stream);
        fclose($stream);

        return [
            'status' => 'success',
            'uuid' => $uuid,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'url' => $this->generateFileUrl($uuid),
        ];
    }

    public function archiveFile(File $file)
    {
    }

    public function generateFileUrl($uuid): string
    {
        return $this->storageAdapter->generateFileUrl('images/original/'.$uuid);
    }
}
