<?php

namespace App\Storage\FileTypes;

use App\Sharding\EntityType;
use App\Sharding\EntityTypes;
use App\Sharding\Uuid;
use App\Sharding\UuidFactory;
use App\Storage\FileStorageInterface;
use App\Storage\FileSystemAdapters\StorageAdapterInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JpgStorage implements FileStorageInterface
{
    /**
     * @var FileUtilities
     */
    protected $fileUtilities;

    /**
     * JpgStorage constructor.
     * @param FileUtilities $fileUtilities
     */
    public function __construct(FileUtilities $fileUtilities)
    {
        $this->fileUtilities= $fileUtilities;
    }

    public function isSupportedFile(File $file): bool
    {
        $mimeType = $file->getMimeType();
        return in_array($mimeType, $this->getMimeTypes());
    }

    public function isSupportedEntityType(EntityType $entityType)
    {
        return $entityType->getId() === EntityTypes::FILE_IMAGE_JPG;
    }

    public function saveFile(File $file)
    {
        return $this->fileUtilities->saveFile(
            $file,
            $this->getMimeTypes(),
            $this->getFileExt(),
            $this->getEntityType(),
            $this->getPathPrefix()
        );
    }

    public function archiveFile(File $file)
    {
        // TODO: Implement archiveFile() method.
    }

    public function generateFileUrl(Uuid $uuid): string
    {
        return $this->fileUtilities->generateFileUrl($uuid, $this->getFileExt(), $this->getPathPrefix());
    }

    /**
     * @return string[]
     */
    protected function getMimeTypes():array
    {
        return ['image/jpeg','image/jpg'];
    }

    protected function getFileExt():string
    {
        return 'jpg';
    }

    protected function getPathPrefix()
    {
        return 'images/original';
    }

    protected function getEntityType()
    {
        return EntityTypes::FILE_IMAGE_JPG;
    }
}
