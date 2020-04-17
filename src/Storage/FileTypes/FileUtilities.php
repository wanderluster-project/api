<?php

namespace App\Storage\FileTypes;

use App\Sharding\Uuid;
use App\Sharding\UuidFactory;
use App\Storage\FileSystemAdapters\StorageAdapterInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileUtilities
{
    /**
     * @var StorageAdapterInterface
     */
    protected $storageAdapter;

    /**
     * @var UuidFactory
     */
    protected $uuidFactory;

    /**
     * JpegImageStorage constructor.
     * @param StorageAdapterInterface $storageAdapter
     * @param UuidFactory $uuidFactory
     */
    public function __construct(StorageAdapterInterface $storageAdapter, UuidFactory $uuidFactory)
    {
        $this->storageAdapter=$storageAdapter;
        $this->uuidFactory =$uuidFactory;
    }

    public function saveFile(File $file, $mimeTypes, $fileExt, int $entityType, $pathPrefix)
    {
        $mimeType = $file->getMimeType();
        if (!in_array($file->getMimeType(), $mimeTypes)) {
            throw new BadRequestHttpException(sprintf('Invalid MIME type: %s', $mimeType));
        }

        $uuid = $this->uuidFactory->generateUUID($file->getFilename(), $entityType);
        $filename = $uuid . '.'.$fileExt ;
        $this->storageAdapter->copyFromLocal($file->getRealPath(), $pathPrefix . '/' . $filename);

        return [
            'status' => 'success',
            'uuid' => $uuid,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'url' => $this->storageAdapter->generateFileUrl($pathPrefix. '/'.$filename),
        ];
    }

    public function generateFileUrl(Uuid $uuid, $fileExt, $pathPrefix): string
    {
        return $this->storageAdapter->generateFileUrl($pathPrefix.'/'.$uuid.'.'.$fileExt);
    }
}
