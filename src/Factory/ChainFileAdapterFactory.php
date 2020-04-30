<?php

declare(strict_types=1);

namespace App\Factory;

use App\FileStorage\FileAdapters\ChainFileAdapter;
use App\FileStorage\FileAdapters\Gif;
use App\FileStorage\FileAdapters\Jpeg;
use App\FileStorage\FileAdapters\Pdf;
use App\FileStorage\FileAdapters\Png;
use App\FileStorage\FileAdapters\Svg;
use App\FileStorage\FileAdapters\Webp;

class ChainFileAdapterFactory
{
    /**
     * @return ChainFileAdapter
     */
    public static function create(Jpeg $jpegStorage, Png $pngStorage, Gif $gifStorage, Svg $svgStorage, Webp $webpStorage, Pdf $pdfStorage)
    {
        $storageCoordinator = new ChainFileAdapter();
        $storageCoordinator->register($jpegStorage, 1);
        $storageCoordinator->register($pngStorage, 1);
        $storageCoordinator->register($gifStorage, 1);
        $storageCoordinator->register($svgStorage, 1);
        $storageCoordinator->register($webpStorage, 1);
        $storageCoordinator->register($pdfStorage, 1);

        return $storageCoordinator;
    }
}
