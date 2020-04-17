<?php

namespace App\Storage\Coordinator;

use App\Storage\FileTypes\JpgStorage;
use App\Storage\FileTypes\PdfStorage;

class StorageCoordinatorFactory
{
    public function create(JpgStorage $jpegStorage, PdfStorage $pdfStorage)
    {
        $storageCoordinator = new StorageCoordinator();
        $storageCoordinator->register($jpegStorage, 1);
        $storageCoordinator->register($pdfStorage, 1);
        return $storageCoordinator;
    }
}
