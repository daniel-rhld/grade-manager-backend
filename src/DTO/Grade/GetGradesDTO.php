<?php

namespace App\DTO\Grade;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class GetGradesDTO
{
    public function __construct(
        #[SerializedName('subject_id')]
        #[Assert\NotBlank(message: 'No subject ID submitted')]
        public int $subjectId,

        #[SerializedName('page')]
        #[Assert\NotBlank(message: 'No page submitted')]
        public int $page,

        #[SerializedName('per_page')]
        #[Assert\NotBlank(message: 'No page submitted')]
        public int $perPage,
    ) {}
}