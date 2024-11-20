<?php

namespace App\Controller\Api\V1;

use App\Controller\AppController;
use App\DTO\Grade\AddGradeDTO;
use App\DTO\Grade\DeleteGradeDTO;
use App\DTO\Grade\GetGradesDTO;
use App\DTO\Grade\RestoreGradeDTO;
use App\Entity\Grade;
use App\Entity\Subject;
use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class GradeController extends AppController
{
    #[Route('/api/v1/grades/', name: 'grades-get-grades', methods: ['GET'])]
    public function getGradesForSubject(
        #[MapRequestPayload] GetGradesDTO $dto,
        Request $request
    ): JsonResponse
    {
        $user = $this->authenticate($request);

        /**
         * @var $subject Subject
         */
        $subject = $this->orm()->getRepository(Subject::class)->findSubjectById(
            $dto->subjectId,
            $user
        );

        if ($subject == null) {
            return $this->errorJsonMessage('Dieses Fach wurde nicht gefunden');
        }

        return $this->json(
            data: $subject->getGrades()->map(function (Grade $grade) {
                return $grade->toJson();
            })->getValues()
        );
    }

    #[Route('/api/v1/grades/', name: 'grades-add-grade', methods: ['POST'])]
    public function addGradeToSubject(
        #[MapRequestPayload] AddGradeDTO $dto,
        Request $request
    ): JsonResponse
    {
        $user = $this->authenticate($request);

        /**
         * @var $subject Subject
         */
        $subject = $this->orm()->getRepository(Subject::class)->findSubjectById(
            $dto->subjectId,
            $user
        );

        if ($subject == null) {
            return $this->errorJsonMessage('Dieses Fach wurde nicht gefunden');
        }

        if ($dto->receivedAt != null && $dto->receivedAt > (new \DateTime())->modify('+1 day')->setTime(0, 0)->getTimestamp()) {
            return $this->jsonMessage('Das Datum der Aushändigung darf nicht in der Zukunft liegen.');
        }

        $receivedAt = $dto->receivedAt != null ? (new \DateTimeImmutable())->setTimestamp($dto->receivedAt) : null;

        $grade = new Grade();
        $grade->setValue($dto->grade);
        $grade->setWeighting($dto->weighting);
        $grade->setReceivedAt($receivedAt);
        $grade->setNote($dto->note);
        $grade->setCreatedAt(new \DateTimeImmutable());

        $subject->addGrade($grade);

        $this->orm()->persist($grade);
        $this->orm()->persist($subject);
        $this->orm()->flush();

        return $this->jsonResponse(
            data: $grade->toJson()
        );
    }

    #[Route('/api/v1/grades/', name: 'grades-delete-grade', methods: ['DELETE'])]
    public function deleteGradeFromSubject(
        #[MapRequestPayload] DeleteGradeDTO $dto,
        Request $request
    ): JsonResponse {
        $user = $this->authenticate($request);

        /**
         * @var $grade Grade
         */
        $grade = $this->orm()->getRepository(Grade::class)->findGradeById(
            $dto->gradeId,
            $user
        );

        if ($grade == null) {
            return $this->errorJsonMessage('Diese Note wurde nicht gefunden');
        }

        $grade->setDeletedAt(new \DateTimeImmutable());

        $this->orm()->persist($grade);
        $this->orm()->flush();

        return $this->jsonMessage('Note wurde gelöscht');
    }

    #[Route('/api/v1/grades/restore/', name: 'grades-restore-grade', methods: ['PATCH'])]
    public function restoreGradeToSubject(
        #[MapRequestPayload] RestoreGradeDTO $dto,
        Request $request
    ): JsonResponse
    {
        $user = $this->authenticate($request);

        /**
         * @var $grade Grade
         */
        $grade = $this->orm()->getRepository(Grade::class)->findGradeById(
            id: $dto->gradeId,
            user: $user,
            includeDeleted: true
        );

        if ($grade == null) {
            return $this->errorJsonMessage('Diese Note wurde nicht gefunden');
        }

        $grade->setDeletedAt(null);

        $this->orm()->persist($grade);
        $this->orm()->flush();

        return $this->jsonResponse(
            data: $grade->toJson()
        );
    }

}