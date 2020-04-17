<?php

namespace App\Storage\FileStorage;

use App\Sharding\EntityType;
use App\Sharding\EntityTypes;
use Symfony\Component\HttpFoundation\File\File;

class PdfStorage extends AbstractFileStorage
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
        return $entityType->getId() === $this->getEntityType();
    }

    /**
     * @inheritdoc
     */
    protected function getMimeTypes(): array
    {
        return ['application/pdf'];
    }

    /**
     * @inheritdoc
     */
    protected function getFileExt(): string
    {
        return 'pdf';
    }

    /**
     * @inheritdoc
     */
    protected function getPathPrefix(): string
    {
        return 'pdf';
    }

    /**
     * @inheritdoc
     */
    protected function getEntityType(): int
    {
        return EntityTypes::FILE_PDF;
    }
}
