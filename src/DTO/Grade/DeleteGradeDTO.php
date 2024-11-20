<?php

namespace App\DTO\Grade;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class DeleteGradeDTO
{
    public function __construct(
        #[SerializedName('grade_id')]
        #[Assert\NotBlank(message: 'No grade ID submitted')]
        public int $gradeId
    ){}
}