<?php

namespace App\Controller\Frontend;

use App\Controller\AppController;
use App\Repository\UserRepository;
use App\Utils\Helpers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendAuthenticationController extends AppController
{
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $orm,
        UserRepository $userRepository
    )
    {
        parent::__construct($orm);

        $this->userRepository = $userRepository;
    }

    #[Route('/auth/verify-registration/', name: 'fronted-authentication-verify-registration', methods: ['GET'])]
    public function viewVerificationEmail(
        Request $request
    ): Response {
        $hash = $request->get('hash');
        $success = false;

        if ($hash !== null && trim($hash) !== '') {
            $user = $this->userRepository->findUserByHash($hash);

            if ($user !== null && !$user->isVerified()) {
                $user->setVerified(true);
                $user->setVerificationHash(null);

                $this->orm()->persist($user);
                $this->orm()->flush();

                $success = true;
            }
        }

        return $this->render(
            view: 'registration/verify-registration.twig',
            parameters: [
                'success' => $success
            ]
        );
    }
}