<?php

namespace App\Controller\Api\V1;

use App\Controller\AppController;
use App\DTO\Subject\CreateSubjectDTO;
use App\DTO\Subject\DeleteSubjectDTO;
use App\DTO\Subject\UpdateSubjectDTO;
use App\Entity\Subject;
use App\Factory\SubjectFactory;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class SubjectController extends AppController
{
    private SubjectRepository $subjectRepository;

    public function __construct(
        EntityManagerInterface $orm,

        SubjectRepository $subjectRepository
    )
    {
        parent::__construct($orm);

        $this->subjectRepository = $subjectRepository;
    }

    #[Route('/api/v1/subjects/', name: 'subjects-get-subjects', methods: ['GET'])]
    public function getSubjects(Request $request): JsonResponse
    {
        $user = $this->authenticate($request);

        return $this->json(
            data: $user->getSubjects()->map(function (Subject $subject) {
                return $subject->toJson();
            })->getValues()
        );
    }

    #[Route('/api/v1/subjects/', name: 'subjects-create-subject', methods: ['POST'])]
    public function createSubject(
        #[MapRequestPayload] CreateSubjectDTO $dto,
        Request $request
    ): JsonResponse {
        $user = $this->authenticate($request);
        $doesSubjectAlreadyExist = $this->subjectRepository->doesSubjectAlreadyExist($dto->name, $user);

        if ($doesSubjectAlreadyExist) {
            return $this->errorJsonMessage('Es existiert bereits ein Fach mit diesem Namen');
        }

        $subject = SubjectFactory::createFromDTO($dto);
        $user->addSubject($subject);

        $this->orm()->persist($subject);
        $this->orm()->persist($user);
        $this->orm()->flush();

        return $this->jsonResponse(
            data: $subject->toJson(),
        );
    }

    #[Route('/api/v1/subjects/', name: 'subjects-update-subject', methods: ['PUT'])]
    public function updateSubject(
        #[MapRequestPayload] UpdateSubjectDTO $dto,
        Request $request
    ): JsonResponse {
        $user = $this->authenticate($request);

        $doesSubjectAlreadyExist = $this->subjectRepository->doesSubjectAlreadyExist($dto->name, $user);

        if ($doesSubjectAlreadyExist) {
            return $this->errorJsonMessage('Es existiert bereits ein Fach mit diesem Namen');
        }

        $subject = $this->subjectRepository->findSubjectById($dto->id, $user);

        if ($subject === null) {
            return $this->errorJsonMessage('Dieses Fach wurde nicht gefunden');
        }

        $subject = SubjectFactory::updateFromDTO($subject, $dto);

        $this->orm()->persist($subject);
        $this->orm()->flush();

        return $this->jsonResponse(
            data: $subject->toJson()
        );
    }

    #[Route('/api/v1/subjects/', name: 'subjects-delete-subject', methods: ['DELETE'])]
    public function deleteSubject(
        #[MapRequestPayload] DeleteSubjectDTO $dto,
        Request $request
    ): JsonResponse {
        $user = $this->authenticate($request);

        $subject = $this->subjectRepository->findSubjectById($dto->id, $user);

        if ($subject === null) {
            return $this->errorJsonMessage('Dieses Fach wurde nicht gefunden');
        }

        $subject = SubjectFactory::delete($subject);

        $this->orm()->persist($subject);
        $this->orm()->flush();

        return $this->jsonMessage('Fach wurde gelöscht');
    }

}