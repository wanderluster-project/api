<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentationController
{
    /**
     * @Route("/docs", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        return new Response('Success');
    }
}
