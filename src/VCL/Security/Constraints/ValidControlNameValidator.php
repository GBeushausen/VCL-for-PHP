<?php

declare(strict_types=1);

namespace VCL\Security\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validator for ValidControlName constraint.
 */
class ValidControlNameValidator extends ConstraintValidator
{
    /**
     * Pattern for valid control names.
     * Must start with letter or underscore, can contain alphanumeric, underscore, hyphen.
     */
    private const PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_-]*$/';

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidControlName) {
            throw new UnexpectedTypeException($constraint, ValidControlName::class);
        }

        // Null and empty string are handled by NotBlank constraint
        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        // Check pattern
        if (!preg_match(self::PATTERN, $value)) {
            $this->context->buildViolation($constraint->patternMessage)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
            return;
        }

        // Check whitelist if strict mode or if allowedNames is provided
        if ($constraint->strict && !empty($constraint->allowedNames)) {
            if (!in_array($value, $constraint->allowedNames, true)) {
                $this->context->buildViolation($constraint->notAllowedMessage)
                    ->setParameter('{{ value }}', $value)
                    ->addViolation();
            }
        }
    }
}
