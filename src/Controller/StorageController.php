<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataModel\Entity\EntityId;
use App\Exception\ErrorMessages;
use App\FileStorage\FileAdapters\GenericFileAdapter;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class StorageController
{
    /**
     * @Route("/api/v1/storage", methods={"POST"})
     */
    public function uploadFile(Request $request, GenericFileAdapter $fileStorage): Response
    {
        if (!$request->files->has('file')) {
            throw new BadRequestHttpException(sprintf(ErrorMessages::REQUEST_MISSING_PARAMETER, 'file'));
        }

        $file = $request->files->get('file');

        if (!$file || !$file->isValid()) {
            throw new BadRequestHttpException(ErrorMessages::REQUEST_INVALID_FILE);
        }

        try {
            $response = $fileStorage->saveFileToRemote($file);

            return new JsonResponse(
                $response
            );
        } catch (Exception $e) {
            throw new HttpException(500, ErrorMessages::SERVER_ERROR_UPLOADING, $e);
        }
    }

    /**
     * @Route("/api/v1/storage/{entityId}", methods={"DELETE"})
     *
     * @param string $entityId
     */
    public function deleteFile($entityId, Request $request, GenericFileAdapter $fileStorage): Response
    {
        if (!$entityId) {
            throw new BadRequestHttpException(sprintf(ErrorMessages::REQUEST_MISSING_PARAMETER, 'entityId'));
        }

        try {
            $entityId = new EntityId($entityId);
            $fileStorage->deleteRemoteFile($entityId);

            return new JsonResponse(
                ['status' => 'success']
            );
        } catch (Exception $e) {
            throw new HttpException(500, ErrorMessages::SERVER_ERROR_DELETING, $e);
        }
    }
}
