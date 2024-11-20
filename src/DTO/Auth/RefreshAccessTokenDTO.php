<?php

namespace App\DTO\Auth;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class RefreshAccessTokenDTO
{
    public function __construct(
        #[SerializedName('refresh_token')]
        #[Assert\NotBlank(message: 'No refresh token submitted')]
        public string $refreshToken
    ){}
}