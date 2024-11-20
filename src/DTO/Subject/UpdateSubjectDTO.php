<?php

namespace App\DTO\Subject;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateSubjectDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'No subject ID submitted')]
        public int $id,

        #[Assert\NotBlank(message: 'Bitte gib einen Namen für das Fach an')]
        public string $name
    ){}
}