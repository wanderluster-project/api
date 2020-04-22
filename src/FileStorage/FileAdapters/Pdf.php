<?php

declare(strict_types=1);

namespace App\FileStorage\FileAdapters;

use App\DataModel\EntityTypes;
use App\FileStorage\FileUtilities;

class Pdf extends AbstractFileAdapter
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
        return ['application/pdf'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getFileExt(): string
    {
        return 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPathPrefix(): string
    {
        return 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityType(): int
    {
        return EntityTypes::FILE_PDF;
    }
}
