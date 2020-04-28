<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PingController
{
    /**
     * @Route("/api/v1/ping")
     */
    public function ping(): JsonResponse
    {
        return new JsonResponse(['status' => 'success']);
    }
}
