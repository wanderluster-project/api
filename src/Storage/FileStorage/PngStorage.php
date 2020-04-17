<?php

namespace App\Storage\FileStorage;

use App\Sharding\EntityTypes;

class PngStorage extends AbstractFileStorage
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
    protected function getMimeTypes(): array
    {
        return ['image/png'];
    }

    /**
     * @inheritdoc
     */
    protected function getFileExt(): string
    {
        return 'png';
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
        return EntityTypes::FILE_IMAGE_PNG;
    }
}
