<?php

declare(strict_types=1);

namespace App\Storage\Coordinator;

use App\Storage\FileStorage\GifStorage;
use App\Storage\FileStorage\JpgStorage;
use App\Storage\FileStorage\PdfStorage;
use App\Storage\FileStorage\PngStorage;
use App\Storage\FileStorage\WebpStorage;

class StorageCoordinatorFactory
{
    /**
     * @return StorageCoordinator
     */
    public function create(JpgStorage $jpegStorage, PngStorage $pngStorage, GifStorage $gifStorage, WebpStorage $webpStorage, PdfStorage $pdfStorage)
    {
        $storageCoordinator = new StorageCoordinator();
        $storageCoordinator->register($jpegStorage, 1);
        $storageCoordinator->register($pngStorage, 1);
        $storageCoordinator->register($gifStorage, 1);
        $storageCoordinator->register($webpStorage, 1);
        $storageCoordinator->register($pdfStorage, 1);

        return $storageCoordinator;
    }
}
