<?php

namespace App\Storage\FileTypes;

use App\Sharding\EntityTypes;

class GifStorage extends AbstractFileStorage
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
        return ['image/gif'];
    }

    /**
     * @inheritdoc
     */
    protected function getFileExt(): string
    {
        return 'gif';
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
        return EntityTypes::FILE_IMAGE_GIF;
    }
}
