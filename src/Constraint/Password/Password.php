<?php

namespace App\Constraint\Password;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Password extends Constraint
{
    public string $message = 'Dein Passwort ist zu schwach. Es muss mindestens {{ minLength }} Zeichen lang sein und muss mindestens {{ minUppercase }} {{ uppercaseLetterLabel }}, {{ minLowercase }} {{ lowercaseLetterLabel }}, {{ minNumbers }} {{ numbersLabel }} und {{ minSpecial }} {{ specialLabel }} enthalten';

    public function __construct(
        public int $minUppercase = 1,
        public int $minLowercase = 1,
        public int $minNumbers = 1,
        public int $minSpecial = 1,
        public int $minLength = 8,

        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null,

        ?string $message = null
    )
    {
        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
    }



}