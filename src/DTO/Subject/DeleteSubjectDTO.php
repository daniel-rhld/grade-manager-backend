<?php

namespace App\DTO\Subject;

use Symfony\Component\Validator\Constraints as Assert;

readonly class DeleteSubjectDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'No subject ID submitted')]
        public int $id
    ){}
}