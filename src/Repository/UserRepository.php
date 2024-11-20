<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUserById(int $id): ?User {
        try {
            return $this->createQueryBuilder('u')
                ->where('u.id = :id')
                ->setMaxResults(1)
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleResult();
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Checks if there already exists a user with a specific email address (for DTO validation)
     *
     * @param array $data An associative array containing the email address to check: <code>['emailAddress' => <value>]</code>
     * @return array
     */
    public function checkIfEmailAddressAlreadyExists(array $data): array {
        return $this->createQueryBuilder('u')
            ->where('u.emailAddress = :email')
            ->andWhere('u.deletedAt IS NULL')
            ->setMaxResults(1)
            ->setParameter('email', $data['emailAddress'])
            ->getQuery()
            ->getResult();
    }

    public function findUserByEmailAddress(string $emailAddress): ?User {
        try {
            return $this->createQueryBuilder('u')
                ->where('u.emailAddress = :email')
                ->andWhere('u.deletedAt IS NULL')
                ->setMaxResults(1)
                ->setParameter('email',$emailAddress)
                ->getQuery()
                ->getSingleResult();
        } catch (\Exception) {
            return null;
        }
    }

}
