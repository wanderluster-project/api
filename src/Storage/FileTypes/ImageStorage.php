<?php

namespace App\Storage\FileTypes;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use App\Sharding\EntityType;
use App\Sharding\EntityTypes;
use App\Sharding\Uuid;
use App\Sharding\UuidFactory;
use App\Storage\FileSystemAdapters\StorageAdapterInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Storage\StorageInterface;

class ImageStorage implements StorageInterface
{
    const IMAGE_PATH_PREFIX = 'images/original';

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var UuidFactory
     */
    protected $uuidFactory;

    /**
     * @var StorageAdapterInterface
     */
    protected $storageAdapter;

    /**
     * ImageStorage constructor.
     * @param StorageAdapterInterface $storageAdapter
     * @param UuidFactory $uuidFactory
     */
    public function __construct(StorageAdapterInterface $storageAdapter, UuidFactory $uuidFactory)
    {
        $this->config = [
            [
                'mimeTypes' => ['image/jpeg','image/jpg'],
                'ext' => 'jpg',
                'entityType' => new EntityType(EntityTypes::FILE_IMAGE_JPG)
            ],
            [
                'mimeTypes' => ['image/svg+xml'],
                'ext' => 'svg',
                'entityType' =>  new EntityType(EntityTypes::FILE_IMAGE_SVG)
            ],
            [
                'mimeTypes' => ['image/png'],
                'ext' => 'png',
                'entityType' =>  new EntityType(EntityTypes::FILE_IMAGE_PNG)
            ],
            [
                'mimeTypes' => ['image/gif'],
                'ext' => 'gif',
                'entityType' => new EntityType(EntityTypes::FILE_IMAGE_GIF)
            ],
            [
                'mimeTypes' => ['image/webp'],
                'ext' => 'webp',
                'entityType' => new EntityType(EntityTypes::FILE_IMAGE_WEBP)
            ]
        ];
        $this->storageAdapter = $storageAdapter;
        $this->uuidFactory = $uuidFactory;
    }

    public function isSupportedFile(File $file): bool
    {
        $mimeType = $file->getMimeType();
        return in_array($mimeType, $this->getMimeTypes());
    }

    public function isSupportedEntityType(EntityType $entityType)
    {
        // TODO: Implement isSupportedEntityType() method.
    }


    public function saveFile(File $file)
    {
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $this->getMimeTypes())) {
            throw new BadRequestHttpException(sprintf('Invalid MIME type: %s', $mimeType));
        }

        $fileSize = $file->getSize();
        $ext =$this->getExtForMimeType($file->getMimeType());
        $entityType = $this->getEntityTypeForMimeType($file->getMimeType());
        $uuid = $this->uuidFactory->generateUUID($file->getFilename(), $entityType);
        $filename = $uuid . '.' . $ext;
        $stream = fopen($file->getRealPath(), 'r+');
        $s3Path = self::IMAGE_PATH_PREFIX . '/' . $filename;
        $this->storageAdapter->saveFile($s3Path, $stream);
        fclose($stream);

        return [
            'status' => 'success',
            'uuid' => $uuid,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'url' => $this->generateFileUrl($uuid),
        ];
    }

    public function archiveFile(File $file)
    {
    }

    public function generateFileUrl(Uuid $uuid): string
    {
        $entityType = $uuid->getEntityType();
        $ext = $this->getExtForEntityTypeId($entityType);
        return $this->storageAdapter->generateFileUrl('images/original/'.$uuid.'.'.$ext);
    }

    protected function getMimeTypes()
    {
        $mimeTypes = [];
        foreach ($this->config as $configItem) {
            $mimeTypes= array_merge($mimeTypes, $configItem['mimeTypes']);
        }

        return array_unique($mimeTypes);
    }

    protected function getEntityTypes()
    {
        $entityTypes = [];
        foreach ($this->config as $configItem) {
            $entityTypes[] = $configItem['entityType'];
        }

        return array_unique($entityTypes);
    }

    public function getExtForMimeType($mimeType)
    {
        foreach ($this->config as $configItem) {
            if (in_array($mimeType, $configItem['mimeTypes'])) {
                return $configItem['ext'];
            }
        }
        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_MIMETYPE, $mimeType));
    }

    public function getEntityTypeForMimeType($mimeType)
    {
        foreach ($this->config as $configItem) {
            if (in_array($mimeType, $configItem['mimeTypes'])) {
                return $configItem['entityType'];
            }
        }
        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_MIMETYPE, $mimeType));
    }

    public function getExtForEntityTypeId($entityTypeId)
    {
        foreach ($this->config as $configItem) {
            $curEntityTypeId = $configItem['entityType']->getId();
            if ($entityTypeId ===  $curEntityTypeId) {
                return $configItem['ext'];
            }
        }

        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_ENTITY_TYPE, $entityTypeId));
    }
}
