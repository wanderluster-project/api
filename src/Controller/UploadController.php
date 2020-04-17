<?php

namespace App\Controller;

use App\Storage\FileStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Exception;

class UploadController
{
    /**
     * @Route("/api/v1/storage/image", methods={"POST"})
     */
    public function uploadImage(Request $request, FileStorage $fileStorage)
    {
        if (!$request->files->has('file')) {
            throw new BadRequestHttpException('Missing parameter: file');
        }

        $file = $request->files->get('file');

        if (!$file  || !$file->isValid()) {
            throw new BadRequestHttpException('Invalid file');
        }

        try {
            $response = $fileStorage->saveFile($file);
            return new JsonResponse(
                $response
            );
        } catch (Exception $e) {
            throw $e;
            throw new HttpException(500, 'Error encountered saving file.  Please try again later.', $e);
        }
    }
}
