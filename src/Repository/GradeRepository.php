<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Subject;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Grade>
 */
class GradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grade::class);
    }

    public function findGradeById(int $id, User $user, bool $includeDeleted = false): ?Grade
    {
        try {
            $query = $this->createQueryBuilder('g')
                ->innerJoin(
                    join: Subject::class,
                    alias: 's',
                    conditionType: 'WITH',
                    condition: 's = g.subject'
                )
                ->where('g.id = :id')
                ->andWhere('s.user = :user')
                ->andWhere('s.deletedAt IS NULL')
                ->setMaxResults(1)
                ->setParameter('id', $id)
                ->setParameter('user', $user);

            if (!$includeDeleted) {
                $query->andWhere('g.deletedAt IS NULL');
            }

            return $query->getQuery()->getSingleResult();
        } catch (\Exception) {
            return null;
        }
    }

}
