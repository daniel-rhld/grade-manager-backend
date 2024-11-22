<?php

namespace App\Controller\Api\V1;

use App\Controller\AppController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AppController
{

    #[Route('/api/v1/user/', name: 'user-get-user-info', methods: ['GET'])]
    public function getUserInfo(
        Request $request
    ): JsonResponse
    {
        $user = $this->authenticate($request);

        return $this->json(data: $user->toJson());
    }
}