<?php

namespace App\Factory;

use App\Entity\AccessToken;
use App\Entity\User;

class AccessTokenFactory
{
    private static function generateRandomString(int $length): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function build(User $user): AccessToken
    {
        $accessToken = new AccessToken();
        $accessToken->setAccessToken(self::generateRandomString(1024));
        $accessToken->setRefreshToken(self::generateRandomString(1024));
        $accessToken->setCreatedAt(new \DateTimeImmutable());
        $accessToken->setUser($user);

        return $accessToken;
    }
}