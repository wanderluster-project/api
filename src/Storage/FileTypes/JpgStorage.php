<?php

namespace App\Storage\FileTypes;

use App\Sharding\EntityType;
use App\Sharding\EntityTypes;
use App\Sharding\Uuid;
use App\Sharding\UuidFactory;
use App\Storage\FileStorageInterface;
use App\Storage\FileSystemAdapters\StorageAdapterInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JpgStorage implements FileStorageInterface
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

    public function isSupportedFile(File $file): bool
    {
        $mimeType = $file->getMimeType();
        return in_array($mimeType, $this->getMimeTypes());
    }

    public function isSupportedEntityType(EntityType $entityType)
    {
        return $entityType->getId() === EntityTypes::FILE_IMAGE_JPG;
    }

    public function saveFile(File $file)
    {
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $this->getMimeTypes())) {
            throw new BadRequestHttpException(sprintf('Invalid MIME type: %s', $mimeType));
        }

        $uuid = $this->uuidFactory->generateUUID($file->getFilename(), EntityTypes::FILE_IMAGE_JPG);
        $filename = $uuid . $this->getFileExt() ;
        $this->storageAdapter->copyFromLocal($file->getRealPath(), $this->getPathPrefix() . '/' . $filename);

        return [
            'status' => 'success',
            'uuid' => $uuid,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'url' => $this->generateFileUrl($uuid),
        ];
    }

    public function archiveFile(File $file)
    {
        // TODO: Implement archiveFile() method.
    }

    public function generateFileUrl(Uuid $uuid): string
    {
        return $this->storageAdapter->generateFileUrl($this->getPathPrefix().'/'.$uuid.'.'.$this->getFileExt());
    }

    /**
     * @return string[]
     */
    protected function getMimeTypes():array
    {
        return ['image/jpeg','image/jpg'];
    }

    protected function getFileExt():string{
        return 'jpg';
    }

    protected function getPathPrefix(){
        return 'images/original';
    }
}
