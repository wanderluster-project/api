<?php

declare(strict_types=1);

namespace App\FileStorage;

use App\DataModel\Uuid;
use App\EntityManager\UuidManager;
use App\Exception\WanderlusterException;
use App\FileStorage\FileSystemAdapters\StorageAdapterInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileUtilities
{
    /**
     * @var StorageAdapterInterface
     */
    protected $remoteStorageAdapter;

    /**
     * @var UuidManager
     */
    protected $uuidFactory;

    /**
     * JpegImageStorage constructor.
     */
    public function __construct(StorageAdapterInterface $remoteStorageAdapter, UuidManager $uuidFactory)
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

    /**
     * @param string $fileExt
     * @param string $pathPrefix
     */
    public function deleteRemoteFile(Uuid $uuid, $fileExt, $pathPrefix): void
    {
        $this->remoteStorageAdapter->deleteRemoteFile($pathPrefix.'/'.$uuid.'.'.$fileExt);
    }
}
