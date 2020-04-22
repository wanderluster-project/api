<?php

declare(strict_types=1);

namespace App\Storage\FileStorage;

use App\RDF\EntityTypes;

class JpgStorage extends AbstractFileStorage
{
    /**
     * JpgStorage constructor.
     */
    public function __construct(FileUtilities $fileUtilities)
    {
        parent::__construct($fileUtilities);
    }

    /**
     * {@inheritdoc}
     */
    protected function getMimeTypes(): array
    {
        return ['image/jpeg', 'image/jpg'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getFileExt(): string
    {
        return 'jpg';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPathPrefix(): string
    {
        return 'images/original';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityType(): int
    {
        return EntityTypes::FILE_IMAGE_JPG;
    }
}
