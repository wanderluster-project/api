<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataModel\Entity\EntityId;
use App\DataModel\Serializer\Serializer;
use App\EntityManager\EntityManager;
use App\Exception\ErrorMessages;
use App\Exception\InvalidEntityIdFormatException;
use App\FileStorage\FileAdapters\ChainFileAdapter;
use Exception;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class StorageController
{
    /**
     * @Route("/api/v1/storage", methods={"POST"})
     */
    public function uploadFile(Request $request, ChainFileAdapter $fileAdapter, Serializer $serializer, EntityManager $entityManager): Response
    {
        if (!$request->files->has('file')) {
            throw new BadRequestHttpException(sprintf(ErrorMessages::REQUEST_MISSING_PARAMETER, 'file'));
        }

        $file = $request->files->get('file');

        if (!$file || !$file->isValid()) {
            throw new BadRequestHttpException(ErrorMessages::REQUEST_INVALID_FILE);
        }

        try {
            $entity = $fileAdapter->saveFileToRemote($file);
            $entityManager->commit();

            return new JsonResponse($serializer->encode($entity), Response::HTTP_OK, [], true);
        } catch (Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, ErrorMessages::SERVER_ERROR_UPLOADING, $e);
        }
    }

    /**
     * @Route("/api/v1/storage/{entityId}", methods={"DELETE"})
     *
     * @param string $entityId
     */
    public function deleteFile($entityId, Request $request, ChainFileAdapter $fileAdapter): Response
    {
        try {
            $entityId = new EntityId($entityId);
            // @todo pull entity to find out the file extension.
//            $fileAdapter->deleteRemoteFile($entityId,'png');

            return new JsonResponse(
                ['status' => 'success']
            );
        } catch (Exception $e) {
            if ($e instanceof FileNotFoundException) {
                throw new NotFoundHttpException(ErrorMessages::SERVER_ERROR_DELETING, $e);
            }
            if ($e instanceof InvalidEntityIdFormatException) {
                throw new BadRequestHttpException($e->getMessage());
            }
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, ErrorMessages::SERVER_ERROR_DELETING, $e);
        }
    }

    /**
     * @Route("/api/v1/storage/{entityId}", methods={"GET"})
     *
     * @param string $entityId
     */
    public function getFile($entityId, Request $request): Response
    {
        return new Response('Not implemented yet', Response::HTTP_NOT_IMPLEMENTED);
    }
}
