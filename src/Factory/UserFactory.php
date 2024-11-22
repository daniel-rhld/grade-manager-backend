<?php

namespace App\Factory;

use App\DTO\Auth\RegisterDTO;
use App\Entity\User;
use App\Utils\Helpers;

class UserFactory
{
    public static function createFromDTO(RegisterDTO $dto): User
    {
        $user = new User();
        $user->setFirstname($dto->firstName);
        $user->setLastname($dto->lastName);
        $user->setEmailAddress($dto->emailAddress);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setVerified(false);
        $user->setVerificationHash(Helpers::generateRandomString(128));

        return $user;
    }
}