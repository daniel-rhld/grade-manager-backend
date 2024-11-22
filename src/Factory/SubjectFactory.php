<?php

namespace App\Factory;

use App\DTO\Subject\CreateSubjectDTO;
use App\DTO\Subject\UpdateSubjectDTO;
use App\Entity\Subject;

class SubjectFactory
{
    public static function createFromDTO(CreateSubjectDTO $dto): Subject
    {
        $subject = new Subject();
        $subject->setName($dto->name);
        $subject->setCreatedAt(new \DateTimeImmutable());

        return $subject;
    }

    public static function updateFromDTO(Subject $subject, UpdateSubjectDTO $dto): Subject
    {
        $subject->setName($dto->name);
        $subject->setUpdatedAt(new \DateTimeImmutable());

        return $subject;
    }

    public static function delete(Subject $subject): Subject
    {
        $subject->setDeletedAt(new \DateTimeImmutable());

        return $subject;
    }

}