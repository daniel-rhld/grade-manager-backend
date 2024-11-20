<?php

namespace App\Repository;

use App\Entity\AccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccessToken>
 */
class AccessTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessToken::class);
    }

    public function findByAccessToken(string $token): ?AccessToken
    {
        try {
            return $this->createQueryBuilder('at')
                ->where('at.accessToken = :token')
                ->setMaxResults(1)
                ->setParameter('token', trim($token))
                ->getQuery()
                ->getSingleResult();
        } catch (\Exception) {
            return null;
        }
    }

    public function findByRefreshToken(string $token): ?AccessToken
    {
        try {
            return $this->createQueryBuilder('at')
                ->where('at.refreshToken = :token')
                ->setMaxResults(1)
                ->setParameter('token', trim($token))
                ->getQuery()
                ->getSingleResult();
        } catch (\Exception) {
            return null;
        }
    }

}
