<?php

namespace App\Controller\Api\V1;

use App\Controller\AppController;
use App\DTO\Grade\AddGradeDTO;
use App\DTO\Grade\DeleteGradeDTO;
use App\DTO\Grade\GetGradesDTO;
use App\DTO\Grade\RestoreGradeDTO;
use App\DTO\PagingData\PagingRequestDto;
use App\Entity\Grade;
use App\Entity\Subject;
use App\Exception\ValidationException;
use App\Factory\GradeFactory;
use App\Repository\GradeRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class GradeController extends AppController
{
    private SubjectRepository $subjectRepository;
    private GradeRepository $gradeRepository;

    public function __construct(
        EntityManagerInterface $orm,

        SubjectRepository $subjectRepository,
        GradeRepository $gradeRepository
    )
    {
        parent::__construct($orm);

        $this->subjectRepository = $subjectRepository;
        $this->gradeRepository = $gradeRepository;
    }

    #[Route('/api/v1/grades/', name: 'grades-get-grades', methods: ['GET'])]
    public function getGradesForSubject(
        #[MapQueryString] GetGradesDTO $dto,
        Request $request
    ): JsonResponse
    {
        $user = $this->authenticate($request);

        $subject = $this->subjectRepository->findSubjectById($dto->subjectId, $user);

        if ($subject === null) {
            return $this->errorJsonMessage('Dieses Fach wurde nicht gefunden');
        }

        $gradesPagingData = $this->gradeRepository->getGradesForSubject(
            subject: $subject,
            page: $dto->page,
            perPage: $dto->perPage
        );

        return $this->json($gradesPagingData->toJson());
    }

    /**
     * @throws ValidationException
     */
    #[Route('/api/v1/grades/', name: 'grades-add-grade', methods: ['POST'])]
    public function addGradeToSubject(
        #[MapRequestPayload] AddGradeDTO $dto,
        Request $request
    ): JsonResponse
    {
        $user = $this->authenticate($request);
        $subject = $this->subjectRepository->findSubjectById($dto->subjectId, $user);

        if ($subject === null) {
            return $this->errorJsonMessage('Dieses Fach wurde nicht gefunden');
        }

        $grade = GradeFactory::createFromDTO($dto);
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
        $grade = $this->gradeRepository->findGradeById($dto->gradeId, $user);

        if ($grade === null) {
            return $this->errorJsonMessage('Diese Note wurde nicht gefunden');
        }

        $grade = GradeFactory::delete($grade);

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
        $grade = $this->gradeRepository->findGradeById(
            id: $dto->gradeId,
            user: $user,
            includeDeleted: true
        );

        if ($grade === null) {
            return $this->errorJsonMessage('Diese Note wurde nicht gefunden');
        }

        $grade = GradeFactory::restore($grade);

        $this->orm()->persist($grade);
        $this->orm()->flush();

        return $this->jsonResponse($grade->toJson());
    }

}