<?php

namespace App\Controller;

use App\Filesystem\MediaFilesystem;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;

class UploadController
{
    /**
     * @Route("/api/v1/media/image", methods={"POST"})
     */
    function uploadImage(Request $request, FilesystemInterface $defaultStorage, MediaFilesystem $mediaFilesystem)
    {
        if (!$request->files->has('file')) {
            throw new BadRequestHttpException('Missing parameter: file');
        }

        $file = $request->files->get('file');
        /**
         * @var $file File
         */

        if (!$file->isValid()) {
            throw new BadRequestHttpException('Invalid file');
        }


        $mimeType = $file->getMimeType();
        $whitelisted = [
            'image/jpeg' => 'jpeg'
        ];
        if (!array_key_exists($mimeType, $whitelisted)) {
            throw new BadRequestHttpException(sprintf('Invalid MIME type: %s', $mimeType));
        }

        $fileSize = $file->getSize();
        $ext = $whitelisted[$file->getMimeType()];
        $uuid = Uuid::uuid4();
        $filename = $uuid . '.' . $ext;
        $stream = fopen($file->getRealPath(), 'r+');
        $defaultStorage->writeStream('uploads/' . $filename, $stream);
        fclose($stream);

        return new JsonResponse(
            [
                'status' => 'success',
                'uuid' => $uuid,
                'mime_type' => $mimeType,
                'file_size'=>$fileSize,
                'url' => 'https://wanderluster-media.s3.amazonaws.com/'.$filename,
            ]
        );
    }
}