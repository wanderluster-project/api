<?php

declare(strict_types=1);

namespace App\FileStorage;

use App\DataModel\Entity\EntityId;
use App\EntityManager\EntityManager;
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
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * JpegImageStorage constructor.
     */
    public function __construct(StorageAdapterInterface $remoteStorageAdapter, EntityManager $entityManager)
    {
        $this->remoteStorageAdapter = $remoteStorageAdapter;
        $this->entityManager = $entityManager;
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

        $entityId = $this->entityManager->allocateEntityId($file->getFilename(), $entityType);
        $filename = $entityId.'.'.$fileExt;
        $this->remoteStorageAdapter->pushLocalFileToRemote($file->getRealPath(), $pathPrefix.'/'.$filename);

        return [
            'id' => $entityId,
            'status' => 'success',
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'url' => $this->remoteStorageAdapter->generateFileUrl($pathPrefix.'/'.$filename),
        ];
    }

    /**
     * @param string $fileExt
     * @param string $pathPrefix
     */
    public function generateFileUrl(EntityId $entityId, $fileExt, $pathPrefix): string
    {
        return $this->remoteStorageAdapter->generateFileUrl($pathPrefix.'/'.$entityId.'.'.$fileExt);
    }

    /**
     * @param string $fileExt
     * @param string $pathPrefix
     */
    public function deleteRemoteFile(EntityId $entityId, $fileExt, $pathPrefix): void
    {
        $this->remoteStorageAdapter->deleteRemoteFile($pathPrefix.'/'.$entityId.'.'.$fileExt);
    }
}
