<?php

namespace App\DTO\Subject;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateSubjectDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Bitte gib einen Namen für das Fach an')]
        public string $name
    ){}
}