<?php

namespace App\Constraint\Password;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PasswordValidator extends ConstraintValidator
{

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof Password)) {
            throw new UnexpectedTypeException($constraint, Password::class);
        }

        if ($value == null || $value == '') {
            return;
        }

        if (!\is_scalar($value) && !$value instanceof \Stringable) {
            throw new UnexpectedValueException($value, 'string');
        }

        $arrayValue = str_split($value);

        $upper = count(array_filter($arrayValue, fn($l) => \IntlChar::isupper($l)));
        $lower = count(array_filter($arrayValue, fn($l) => \IntlChar::islower($l)));
        $isDigit = count(array_filter($arrayValue, fn($l) => \IntlChar::isdigit($l)));
        $isSymbol = count(array_filter($arrayValue, fn($l) => !\IntlChar::isalpha($l) && !\IntlChar::isdigit($l)));

        if ($constraint->minUppercase > $upper
            || $constraint->minLowercase > $lower
            || $constraint->minNumbers > $isDigit
            || $constraint->minSpecial > $isSymbol
        ) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ minLength }}', $constraint->minLength)
                ->setParameter('{{ minUppercase }}', $constraint->minUppercase)
                ->setParameter('{{ uppercaseLetterLabel }}', $constraint->minUppercase == 1 ? 'Großbuchstabe' : 'Großbuchstaben')
                ->setParameter('{{ minLowercase }}', $constraint->minLowercase)
                ->setParameter('{{ lowercaseLetterLabel }}', $constraint->minLowercase == 1 ? 'Kleinbuchstabe' : 'Kleinbuchstaben')
                ->setParameter('{{ minNumbers }}', $constraint->minNumbers)
                ->setParameter('{{ numbersLabel }}', $constraint->minNumbers == 1 ? 'Zahl' : 'Zahlen')
                ->setParameter('{{ minSpecial }}', $constraint->minSpecial)
                ->setParameter('{{ specialLabel }}', 'Sonderzeichen')
                ->addViolation();
        }


    }
}