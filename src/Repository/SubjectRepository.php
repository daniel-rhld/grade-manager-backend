<?php

namespace App\Repository;

use App\Entity\Subject;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subject>
 */
class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    public function doesSubjectAlreadyExist(string $subjectName, User $user): bool
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.user = :user')
            ->andWhere('LOWER(s.name) = LOWER(:subjectName)')
            ->andWhere('s.deletedAt IS NULL')
            ->setParameter('user', $user)
            ->setParameter('subjectName', $subjectName)
            ->getQuery()
            ->getSingleScalarResult() >= 1;
    }

    public function findSubjectById(int $id, User $user): ?Subject
    {
        try {
            return $this->createQueryBuilder('s')
                ->where('s.id = :id')
                ->andWhere('s.deletedAt IS NULL')
                ->andWhere('s.user = :user')
                ->setMaxResults(1)
                ->setParameter('id', $id)
                ->setParameter('user', $user)
                ->getQuery()
                ->getSingleResult();
        } catch (\Exception) {
            return null;
        }
    }


}
