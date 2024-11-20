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

    protected function authenticate(Request $request, bool $throwException = true): ?User {
        $accessTokenRaw = trim($request->headers->get(
            key: 'Authorization',
            default: ''
        ));

        if (strlen($accessTokenRaw) == 0) {
            return $throwException ? throw new UnauthorizedHttpException('') : null;
        }

        $token = explode(
            separator: ' ',
            string: $accessTokenRaw
        )[1];

        if ($token == null || strlen(trim($token)) == 0) {
            return $throwException ? throw new UnauthorizedHttpException('') : null;
        }

        $accessToken = $this->orm->getRepository(AccessToken::class)->findByAccessToken($token);

        if ($accessToken != null && $accessToken->isValid()) {
            return $accessToken->getUser();
        }

        return $throwException ? throw new UnauthorizedHttpException('') : null;
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