<?php

declare(strict_types=1);

namespace App\FileStorage\FileAdapters;

use App\DataModel\EntityTypes;
use App\FileStorage\FileUtilities;

class Png extends AbstractFileAdapter
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
        return ['image/png'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getFileExt(): string
    {
        return 'png';
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
        return EntityTypes::FILE_IMAGE_PNG;
    }
}
