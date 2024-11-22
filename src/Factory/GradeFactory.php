<?php

namespace App\Factory;

use App\DTO\Grade\AddGradeDTO;
use App\Entity\Grade;
use App\Exception\ValidationException;

class GradeFactory
{
    /**
     * @throws ValidationException
     */
    public static function createFromDTO(AddGradeDTO $dto): Grade
    {
        $dtoIncludesReceivedAt = $dto->receivedAt !== null;

        $grade = new Grade();
        $grade->setValue($dto->grade);
        $grade->setWeighting($dto->weighting);
        $grade->setNote($dto->note);
        $grade->setCreatedAt(new \DateTimeImmutable());

        if ($dtoIncludesReceivedAt) {
            $maximumReceivedAtDate = new \DateTime();
            try {
                $maximumReceivedAtDate = $maximumReceivedAtDate->modify('+1 day')->setTime(0, 0);
            } catch (\DateMalformedStringException $e) {
                throw new ValidationException($e->getMessage());
            }

            if ($dto->receivedAt >= $maximumReceivedAtDate->getTimestamp()) {
                throw new ValidationException('Das Datum der Aushändigung darf nicht in der Zukunft liegen');
            }

            $grade->setReceivedAt((new \DateTimeImmutable())->setTimestamp($dto->receivedAt));
        }

        return $grade;
    }

    public static function delete(Grade $grade): Grade
    {
        $grade->setDeletedAt(new \DateTimeImmutable());

        return $grade;
    }

    public static function restore(Grade $grade): Grade
    {
        $grade->setDeletedAt(null);

        return $grade;
    }

}