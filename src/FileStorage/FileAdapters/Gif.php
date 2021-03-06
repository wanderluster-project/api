<?php

declare(strict_types=1);

namespace App\FileStorage\FileAdapters;

use App\DataModel\Entity\EntityTypes;
use App\FileStorage\FileUtilities;

class Gif extends AbstractFileAdapter
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
        return ['image/gif'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getFileExt(): string
    {
        return 'gif';
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
        return EntityTypes::FILE_IMAGE_GIF;
    }
}
