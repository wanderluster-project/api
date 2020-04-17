<?php

namespace App\Storage\FileTypes;

use App\Sharding\EntityType;
use App\Sharding\EntityTypes;
use Symfony\Component\HttpFoundation\File\File;

class JpgStorage extends AbstractFileStorage
{
    /**
     * JpgStorage constructor.
     * @param FileUtilities $fileUtilities
     */
    public function __construct(FileUtilities $fileUtilities)
    {
        parent::__construct($fileUtilities);
    }

    /**
     * @inheritdoc
     */
    public function isSupportedFile(File $file): bool
    {
        $mimeType = $file->getMimeType();
        return in_array($mimeType, $this->getMimeTypes());
    }

    /**
     * @inheritdoc
     */
    public function isSupportedEntityType(EntityType $entityType): bool
    {
        return $entityType->getId() === EntityTypes::FILE_IMAGE_JPG;
    }

    /**
     * @inheritdoc
     */
    protected function getMimeTypes(): array
    {
        return ['image/jpeg', 'image/jpg'];
    }

    /**
     * @inheritdoc
     */
    protected function getFileExt(): string
    {
        return 'jpg';
    }

    /**
     * @inheritdoc
     */
    protected function getPathPrefix(): string
    {
        return 'images/original';
    }

    /**
     * @inheritdoc
     */
    protected function getEntityType(): int
    {
        return EntityTypes::FILE_IMAGE_JPG;
    }
}
