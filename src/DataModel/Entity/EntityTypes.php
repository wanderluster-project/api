<?php

declare(strict_types=1);

namespace App\DataModel\Entity;

class EntityTypes
{
    // Types 0-1000 reserved
    const TEST_ENTITY_TYPE = 10;

    // IMAGES
    const FILE_IMAGE_JPG = 1000;
    const FILE_IMAGE_PNG = 1001;
    const FILE_IMAGE_SVG = 1002;
    const FILE_IMAGE_GIF = 1003;
    const FILE_IMAGE_WEBP = 1004;

    // PDF
    const FILE_PDF = 1050;
}
