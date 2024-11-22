<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RefreshAccessTokenDTO;
use App\DTO\Auth\RegisterDTO;
use App\Entity\AccessToken;
use App\Entity\User;
use App\Factory\AccessTokenFactory;
use App\Factory\UserFactory;
use App\Repository\AccessTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends AppController
{
    private AccessTokenRepository $accessTokenRepository;
    private UserRepository $userRepository;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $orm,
        AccessTokenRepository $accessTokenRepository,
        UserRepository $userRepository,

        UserPasswordHasherInterface $passwordHasher
    )
    {
        parent::__construct($orm);

        $this->accessTokenRepository = $accessTokenRepository;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/api/auth/register/', name: 'authentication-register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] RegisterDTO $dto,
        MailerInterface $mailer
    ): JsonResponse {
        $user = UserFactory::createFromDTO($dto);
        $user->setPassword($this->passwordHasher->hashPassword(
            user: $user,
            plainPassword: $dto->password
        ));

        $this->orm()->persist($user);
        $this->orm()->flush();

        $confirmationEmail = (new TemplatedEmail())
            ->from('grademanager@orga-life.de')
            ->to($user->getEmailAddress())
            ->subject('Herzlich wilkommen!')
            ->htmlTemplate('email/registration-confirmation.twig')
            ->context([
                'confirmation_url' => 'https://orga-life.de/auth/verify-registration/?hash=' . $user->getVerificationHash()
            ]);

        try {
            $mailer->send($confirmationEmail);
        } catch (TransportExceptionInterface) {}

        return $this->jsonMessage('Registrierung erfolgreich!');
    }

    #[Route('/api/auth/login/', name: 'authentication-login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload] LoginDTO $dto
    ): JsonResponse {
        $user = $this->userRepository->findUserByEmailAddress($dto->emailAddress);

        if ($user === null) {
            return $this->errorJsonMessage('Es wurde kein Nutzer mit dieser Email-Adresse gefunden');
        }

        if (
            !$this->passwordHasher->isPasswordValid(
                user: $user,
                plainPassword: $dto->password
            )
        ) {
            return $this->errorJsonMessage('Dieses Passwort ist nicht korrekt');
        }

        $accessToken = AccessTokenFactory::build($user);
        $this->orm()->persist($accessToken);
        $this->orm()->flush();

        return $this->json($accessToken->toJson());
    }

    #[Route('/api/auth/refresh-token/', name: 'authentication-refresh-access-token', methods: ['POST'])]
    public function refreshAccessToken(
        #[MapRequestPayload] RefreshAccessTokenDTO $dto
    ): JsonResponse {
        $accessToken = $this->accessTokenRepository->findByRefreshToken($dto->refreshToken);

        if ($accessToken === null || !$accessToken->isRefreshTokenValid()) {
            return $this->errorJsonMessage(
                message: 'Invalid token',
                status: 401
            );
        }

        $accessToken->setRefreshTokenUsed(true);
        $renewedAccessToken = AccessTokenFactory::build($accessToken->getUser());

        $this->orm()->persist($accessToken);
        $this->orm()->persist($renewedAccessToken);
        $this->orm()->flush();

        return $this->json($accessToken->toJson());
    }

}