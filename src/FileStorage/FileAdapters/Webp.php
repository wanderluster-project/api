<?php

declare(strict_types=1);

namespace App\FileStorage\FileAdapters;

use App\DataModel\Entity\EntityTypes;
use App\FileStorage\FileUtilities;

class Webp extends AbstractFileAdapter
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
        return ['image/webp'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getFileExt(): string
    {
        return 'webp';
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
        return EntityTypes::FILE_IMAGE_WEBP;
    }
}
