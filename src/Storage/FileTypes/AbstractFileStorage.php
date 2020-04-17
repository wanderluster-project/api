<?php

namespace App\Storage\FileTypes;

use App\Sharding\EntityType;
use App\Sharding\Uuid;
use App\Storage\FileStorageInterface;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractFileStorage implements FileStorageInterface
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
        $this->fileUtilities = $fileUtilities;
    }

    abstract public function isSupportedFile(File $file): bool;

    abstract public function isSupportedEntityType(EntityType $entityType): bool;

    abstract protected function getMimeTypes(): array;

    abstract protected function getFileExt(): string;

    abstract protected function getPathPrefix(): string;

    abstract protected function getEntityType(): int;

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

}