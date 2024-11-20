<?php

namespace App\DTO\Grade;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class AddGradeDTO
{
    public function __construct(
        #[SerializedName('subject_id')]
        #[Assert\NotBlank(message: 'No subject ID submitted')]
        public int $subjectId,

        #[Assert\NotBlank(message: 'Bitte gib eine Note an')]
        #[Assert\Range(
            notInRangeMessage: 'Bitte gib eine Note zwischen 1,0 und 6,0 an',
            min: 1.0,
            max: 6.0
        )]
        public float $grade,

        #[Assert\NotBlank(message: 'Bitte gib eine Gewichtung an')]
        #[Assert\Range(
            notInRangeMessage: 'Bitte gib eine Gewichtung zwischen 0,25 und 2,0 an',
            min: 1.0,
            max: 6.0
        )]
        public float $weighting,

        #[SerializedName('received_at')]
        public ?int $receivedAt,

        public ?string $note
    ){}
}