<?php

namespace App\Controller;

use App\Entity\AccessToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AppController extends AbstractController
{
    private EntityManagerInterface $orm;

    public function __construct(EntityManagerInterface $orm)
    {
        $this->orm = $orm;
    }

    protected function orm(): EntityManagerInterface {
        return $this->orm;
    }

    protected function authenticate(Request $request): User {
        $accessTokenRaw = trim($request->headers->get(
            key: 'Authorization',
            default: ''
        ));

        if ($accessTokenRaw === '') {
            return throw new UnauthorizedHttpException('');
        }

        $tokenData = explode(
            separator: ' ',
            string: $accessTokenRaw
        );

        if (count($tokenData) !== 2) {
            return throw new UnauthorizedHttpException('');
        }

        if (trim($tokenData[0]) !== 'Bearer') {
            return throw new UnauthorizedHttpException('');
        }

        $token = $tokenData[1];

        if ($token === null || trim($token) === '') {
            return throw new UnauthorizedHttpException('');
        }

        $accessToken = $this->orm->getRepository(AccessToken::class)->findByAccessToken($token);

        if ($accessToken !== null && $accessToken->isValid()) {
            return $accessToken->getUser();
        }

        return throw new UnauthorizedHttpException('');
    }

    public function errorJsonResponse(array $data, int $status = 400): JsonResponse
    {
        return $this->json(
            data: [
                'success' => false,
                'data' => $data
            ],
            status: $status
        );
    }

    public function errorJsonMessage(string $message, int $status = 400): JsonResponse
    {
        return $this->json(
            data: [
                'success' => false,
                'message' => $message
            ],
            status: $status
        );
    }

    public function jsonMessage(string $message, int $status = 200): JsonResponse
    {
        return $this->json(
            data: [
                'success' => true,
                'message' => $message
            ],
            status: $status
        );
    }

    public function jsonResponse(array $data, int $status = 200): JsonResponse
    {
        return $this->json(
            data: [
                'success' => true,
                'data' => $data
            ],
            status: $status
        );
    }

}