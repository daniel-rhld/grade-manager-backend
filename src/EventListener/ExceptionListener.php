<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exceptionHandled = false;
        $exception = $event->getThrowable();

        $jsonResponse = [
            'success' => false,
            'message' => 'Ein Fehler ist aufgetreten'
        ];
        $statusCode = 400;

        if ($exception instanceof UnprocessableEntityHttpException) {
            $originException = $exception->getPrevious();

            if ($originException instanceof ValidationFailedException) {
                $violations = $originException->getViolations();

                if ($violations->count() >= 1) {
                    $jsonResponse['message'] = $violations->get(0)->getMessage();
                    $exceptionHandled = true;
                }
            }
        }

        if ($exception instanceof UnauthorizedHttpException) {
            $jsonResponse['message'] = 'Unauthorized';
            $exceptionHandled = true;
        }

        if (!$exceptionHandled) {
            $jsonResponse['message'] = $exception->getMessage();
        }

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
        }

        $event->setResponse(
            response: new JsonResponse(
                data: $jsonResponse,
                status: $statusCode
            )
        );
    }
}