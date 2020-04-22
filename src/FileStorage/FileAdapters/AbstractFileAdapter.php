<?php

declare(strict_types=1);

namespace App\FileStorage\FileAdapters;

use App\DataModel\Uuid;
use App\FileStorage\FileUtilities;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractFileAdapter implements FileAdapterInterface
{
    /**
     * @var FileUtilities
     */
    protected $fileUtilities;

    /**
     * JpgStorage constructor.
     */
    public function __construct(FileUtilities $fileUtilities)
    {
        $this->fileUtilities = $fileUtilities;
    }

    /**
     * @return string[]
     */
    abstract protected function getMimeTypes(): array;

    abstract protected function getFileExt(): string;

    abstract protected function getPathPrefix(): string;

    abstract protected function getEntityType(): int;

    /**
     * {@inheritdoc}
     */
    public function isSupportedFile(File $file): bool
    {
        $mimeType = $file->getMimeType();

        return in_array($mimeType, $this->getMimeTypes());
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedEntityType(int $entityType): bool
    {
        return $entityType === $this->getEntityType();
    }

    /**
     * {@inheritdoc}
     */
    public function saveFileToRemote(File $file): array
    {
        return $this->fileUtilities->saveFileToRemote(
            $file,
            $this->getMimeTypes(),
            $this->getFileExt(),
            $this->getEntityType(),
            $this->getPathPrefix()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateFileUrl(Uuid $uuid): string
    {
        return $this->fileUtilities->generateFileUrl($uuid, $this->getFileExt(), $this->getPathPrefix());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteRemoteFile(Uuid $uuid): void
    {
        $this->fileUtilities->deleteRemoteFile($uuid, $this->getFileExt(), $this->getPathPrefix());
    }
}
