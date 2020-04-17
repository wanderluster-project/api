<?php

namespace App\Storage\Coordinator;

use App\Storage\FileTypes\ImageStorage;
use App\Storage\FileTypes\PdfStorage;

class StorageCoordinatorFactory
{
    public function create(ImageStorage $imageStorage, PdfStorage $pdfStorage)
    {
        $storageCoordinator = new StorageCoordinator();
        $storageCoordinator->register($imageStorage, 1);
        $storageCoordinator->register($pdfStorage, 1);
        return $storageCoordinator;
    }
}
