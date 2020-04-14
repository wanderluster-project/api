<?php

namespace App\Controller;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UploadController
{
    /**
     * @Route("/api/v1/media/image", methods={"POST"})
     */
    function uploadImage(Request $request){
        if(!$request->files->has('file')){
            throw new BadRequestHttpException('Missing parameter: file');
        }

        $file = $request->files->get('file');

        dump($file);
        exit;
    }
}