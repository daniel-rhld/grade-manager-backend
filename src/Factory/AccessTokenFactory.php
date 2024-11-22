<?php

namespace App\Factory;

use App\Entity\AccessToken;
use App\Entity\User;
use App\Utils\Helpers;

class AccessTokenFactory
{
    public static function build(User $user): AccessToken
    {
        $accessToken = new AccessToken();
        $accessToken->setAccessToken(Helpers::generateRandomString(1024));
        $accessToken->setRefreshToken(Helpers::generateRandomString(1024));
        $accessToken->setCreatedAt(new \DateTimeImmutable());
        $accessToken->setRefreshTokenUsed(false);
        $accessToken->setUser($user);

        return $accessToken;
    }
}