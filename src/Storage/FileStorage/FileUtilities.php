<?php

declare(strict_types=1);

namespace App\Storage\FileStorage;

use App\Exception\WanderlusterException;
use App\Sharding\Uuid;
use App\Sharding\UuidFactory;
use App\Storage\FileSystemAdapters\RemoteStorageAdapterInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileUtilities
{
    /**
     * @var RemoteStorageAdapterInterface
     */
    protected $remoteStorageAdapter;

    /**
     * @var UuidFactory
     */
    protected $uuidFactory;

    /**
     * JpegImageStorage constructor.
     */
    public function __construct(RemoteStorageAdapterInterface $remoteStorageAdapter, UuidFactory $uuidFactory)
    {
        $this->remoteStorageAdapter = $remoteStorageAdapter;
        $this->uuidFactory = $uuidFactory;
    }

    /**
     * @param string[] $mimeTypes
     * @param string   $fileExt
     * @param string   $pathPrefix
     *
     * @throws WanderlusterException
     */
    public function saveFileToRemote(File $file, $mimeTypes, $fileExt, int $entityType, $pathPrefix): array
    {
        $mimeType = $file->getMimeType();
        if (!in_array($file->getMimeType(), $mimeTypes)) {
            throw new BadRequestHttpException(sprintf('Invalid MIME type: %s', $mimeType));
        }

        $uuid = $this->uuidFactory->generateUUID($file->getFilename(), $entityType);
        $filename = $uuid.'.'.$fileExt;
        $this->remoteStorageAdapter->pushLocalFileToRemote($file->getRealPath(), $pathPrefix.'/'.$filename);

        return [
            'status' => 'success',
            'uuid' => $uuid,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'url' => $this->remoteStorageAdapter->generateFileUrl($pathPrefix.'/'.$filename),
        ];
    }

    /**
     * @param string $fileExt
     * @param string $pathPrefix
     */
    public function generateFileUrl(Uuid $uuid, $fileExt, $pathPrefix): string
    {
        return $this->remoteStorageAdapter->generateFileUrl($pathPrefix.'/'.$uuid.'.'.$fileExt);
    }
}
