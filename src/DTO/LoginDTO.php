<?php

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class LoginDTO
{
    public function __construct(
        #[SerializedName('email_address')]
        #[Assert\NotBlank(message: 'Bitte gib deine Email-Adresse ein')]
        public string $emailAddress,

        #[SerializedName('password')]
        #[Assert\NotBlank(message: 'Bitte gib dein Passwort ein')]
        public string $password
    ){}
}