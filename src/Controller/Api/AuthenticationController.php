<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RefreshAccessTokenDTO;
use App\DTO\Auth\RegisterDTO;
use App\Entity\AccessToken;
use App\Entity\User;
use App\Factory\AccessTokenFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends AppController
{

    #[Route('/api/auth/register/', name: 'authentication-register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] RegisterDTO $dto,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $user = new User();
        $user->setFirstname($dto->firstName);
        $user->setLastname($dto->lastName);
        $user->setEmailAddress($dto->emailAddress);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setPassword($passwordHasher->hashPassword(
            user: $user,
            plainPassword: $dto->password
        ));

        $this->orm()->persist($user);
        $this->orm()->flush();

        return $this->jsonMessage('Registrierung erfolgreich!');
    }

    #[Route('/api/auth/login/', name: 'authentication-login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload] LoginDTO $dto,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        /**
         * @var $user User
         */
        $user = $this->orm()->getRepository(User::class)->findUserByEmailAddress($dto->emailAddress);

        if ($user == null) {
            return $this->errorJsonMessage('Es wurde kein Nutzer mit dieser Email-Adresse gefunden');
        }

        if (
            !$passwordHasher->isPasswordValid(
                user: $user,
                plainPassword: $dto->password
            )
        ) {
            return $this->errorJsonMessage('Dieses Passwort ist nicht korrekt');
        }

        $accessToken = AccessTokenFactory::build($user);
        $this->orm()->persist($accessToken);
        $this->orm()->flush();

        return $this->jsonResponse(
            data: [
                'access_token' => [
                    'token' => $accessToken->getAccessToken(),
                    'expiration' => $accessToken->getCreatedAt()->getTimestamp() + AccessToken::ACCESS_TOKEN_TTL
                ],
                'refresh_token' => $accessToken->getRefreshToken(),
                'user' => $user->toJson()
            ]
        );
    }

    #[Route('/api/auth/refresh-token/', name: 'authentication-refresh-access-token', methods: ['POST'])]
    public function refreshAccessToken(
        #[MapRequestPayload] RefreshAccessTokenDTO $dto
    ): JsonResponse {
        /**
         * @var $accessToken AccessToken
         */
        $accessToken = $this->orm()->getRepository(AccessToken::class)->findByRefreshToken($dto->refreshToken);

        if ($accessToken == null || !$accessToken->isRefreshTokenValid()) {
            return $this->errorJsonMessage(
                message: 'Token expired',
                status: 401
            );
        }

        $renewedAccessToken = AccessTokenFactory::build($accessToken->getUser());
        $this->orm()->persist($renewedAccessToken);
        $this->orm()->flush();

        return $this->jsonResponse(
            data: [
                'access_token' => [
                    'token' => $renewedAccessToken->getAccessToken(),
                    'expiration' => $renewedAccessToken->getCreatedAt()->getTimestamp() + AccessToken::ACCESS_TOKEN_TTL
                ],
                'refresh_token' => $renewedAccessToken->getRefreshToken(),
                'user' => $renewedAccessToken->getUser()->toJson()
            ]
        );
    }

}