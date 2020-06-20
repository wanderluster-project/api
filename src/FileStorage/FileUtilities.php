<?php

declare(strict_types=1);

namespace App\FileStorage;

use App\DataModel\Attributes\Attributes;
use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\WanderlusterException;
use App\FileStorage\FileSystemAdapters\StorageAdapterInterface;
use App\Persistence\EntityManager;
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
    public function saveFileToRemote(File $file, $mimeTypes, $fileExt, int $entityType, $pathPrefix): Entity
    {
        $mimeType = $file->getMimeType();
        if (!in_array($file->getMimeType(), $mimeTypes)) {
            throw new BadRequestHttpException(sprintf('Invalid MIME type: %s', $mimeType));
        }

        $entity = $this->entityManager->create($entityType);
        $entityId = $entity->getEntityId();
        $filename = $entityId.'.'.$fileExt;
        $this->remoteStorageAdapter->pushLocalFileToRemote($file->getRealPath(), $pathPrefix.'/'.$filename);

        // @todo determine language
        $entity->load(LanguageCodes::ENGLISH);

        $entity->set(Attributes::CORE_FILE_MIME_TYPE, $mimeType)
            ->set(Attributes::CORE_FILE_SIZE, $file->getSize())
            ->set(Attributes::CORE_FILE_URL, $this->remoteStorageAdapter->generateFileUrl($pathPrefix.'/'.$filename));

        return $entity;
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
