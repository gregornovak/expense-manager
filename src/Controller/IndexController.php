<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class IndexController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return new JsonResponse(['salutation' => "Hello there"]);
    }

    /**
     * @Route("/me/{name}")
     */
    public function getMe(string $name)
    {
        return new JsonResponse(['name' => $name]);
    }
}