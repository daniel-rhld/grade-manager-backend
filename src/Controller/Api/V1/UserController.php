<?php

namespace App\Controller\Api\V1;

use App\Controller\AppController;
use App\Utils\Helpers;
use PHPUnit\TextUI\Help;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AppController
{

    #[Route('/send-test-email/', name: 'send-test-email', methods: ['POST'])]
    public function sendTestEmail(
        Request $request,
        MailerInterface $mailer
    ): JsonResponse
    {
        $recipient = $request->get('recipient');
        $subject = $request->get('subject');
        $message = $request->get('message');

        $testEmail = (new TemplatedEmail())
            ->from('grademanager@orga-life.de')
            ->to($recipient)
            ->subject('Herzlich wilkommen!')
            ->htmlTemplate('email/registration-confirmation.twig')
            ->context([
                'confirmation_url' => 'https://www.google.de'
            ]);

        $jsonResponse = ['success' => true, 'message' => 'Email sent to ' . $recipient . '!'];

        try {
            $mailer->send($testEmail);
        } catch (TransportExceptionInterface $e) {
            $jsonResponse = ['success' => false, 'message' => $e->getMessage()];
        }

        return $this->json($jsonResponse);
    }

    #[Route('/api/v1/user/', name: 'user-get-user-info', methods: ['GET'])]
    public function getUserInfo(
        Request $request
    ): JsonResponse
    {
        $user = $this->authenticate($request);

        return $this->json(data: $user->toJson());
    }
}