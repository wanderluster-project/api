<?php

namespace App\Storage\Coordinator;

use App\Storage\FileTypes\GifStorage;
use App\Storage\FileTypes\JpgStorage;
use App\Storage\FileTypes\PdfStorage;
use App\Storage\FileTypes\PngStorage;
use App\Storage\FileTypes\WebpStorage;

class StorageCoordinatorFactory
{
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
