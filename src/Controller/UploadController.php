<?php

namespace App\Controller;

use App\Filesystem\MediaFilesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Exception;

class UploadController
{
    /**
     * @Route("/api/v1/media/image", methods={"POST"})
     */
    function uploadImage(Request $request, MediaFilesystem $mediaFilesystem)
    {
        if (!$request->files->has('file')) {
            throw new BadRequestHttpException('Missing parameter: file');
        }

        $file = $request->files->get('file');

        if (!$file->isValid()) {
            throw new BadRequestHttpException('Invalid file');
        }

        try{
            return new JsonResponse(
                $mediaFilesystem->saveImage($file)
            );
        } catch(Exception $e){
            throw new HttpException(500,'Error encountered saving file.  Please try again later.', $e);
        }
    }
}