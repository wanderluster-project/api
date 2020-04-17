<?php

namespace App\Storage\FileTypes;

use App\Sharding\EntityTypes;

class WebpStorage extends AbstractFileStorage
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
        return ['image/webp'];
    }

    /**
     * @inheritdoc
     */
    protected function getFileExt(): string
    {
        return 'webp';
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
        return EntityTypes::FILE_IMAGE_WEBP;
    }
}
