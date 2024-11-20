<?php

namespace App\DTO\Auth;

use App\Constraint as CustomAssert;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(
    fields: ['emailAddress' => 'emailAddress'],
    message: 'Diese Email-Adresse wird bereits verwendet',
    entityClass: User::class,
    repositoryMethod: 'checkIfEmailAddressAlreadyExists'
)]
readonly class RegisterDTO
{
    public function __construct(
        #[SerializedName('first_name')]
        #[Assert\NotBlank(message: 'Bitte gib deinen Vornamen an')]
        public string $firstName,

        #[SerializedName('last_name')]
        #[Assert\NotBlank(message: 'Bitte gib deinen Nachnamen an')]
        public string $lastName,

        #[SerializedName('email_address')]
        #[Assert\NotBlank(message: 'Bitte gib deine Email-Adresse an')]
        #[Assert\Email(message: 'Bitte gib eine gültige Email-Adresse an')]
        public string $emailAddress,

        #[SerializedName('password')]
        #[Assert\NotBlank(message: 'Bitte gib ein Passwort an')]
        #[CustomAssert\Password\Password(
            minUppercase: 1,
            minLowercase: 1,
            minNumbers: 1,
            minSpecial: 1,
            minLength: 8
        )]
        public string $password,

        #[SerializedName('password_confirmation')]
        #[Assert\NotBlank(message: 'Bitte bestätige dein Passwort')]
        #[Assert\EqualTo(
            propertyPath: 'password',
            message: 'Die Passwortbestätigung stimmt nicht mit dem Passwort überein'
        )]
        public string $passwordConfirmation,
    ){}
}