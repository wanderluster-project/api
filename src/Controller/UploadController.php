<?php

declare(strict_types=1);

namespace App\Controller;

use App\Storage\FileStorage\GenericFileStorage;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class UploadController
{
    /**
     * @Route("/api/v1/storage/image", methods={"POST"})
     */
    public function uploadImage(Request $request, GenericFileStorage $fileStorage): Response
    {
        if (!$request->files->has('file')) {
            throw new BadRequestHttpException('Missing parameter: file');
        }

        $file = $request->files->get('file');

        if (!$file || !$file->isValid()) {
            throw new BadRequestHttpException('Invalid file');
        }

        try {
            $response = $fileStorage->saveFileToRemote($file);

            return new JsonResponse(
                $response
            );
        } catch (Exception $e) {
            throw new HttpException(500, 'Error encountered saving file.  Please try again later.', $e);
        }
    }
}
