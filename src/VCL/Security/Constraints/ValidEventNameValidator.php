<?php

declare(strict_types=1);

namespace VCL\Security\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validator for ValidEventName constraint.
 */
class ValidEventNameValidator extends ConstraintValidator
{
    /**
     * Pattern for valid event names.
     * Must start with letter or underscore, can contain alphanumeric and underscore.
     */
    private const PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidEventName) {
            throw new UnexpectedTypeException($constraint, ValidEventName::class);
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

        // Check whitelist if strict mode
        if ($constraint->strict && !empty($constraint->allowedEvents)) {
            if (!in_array($value, $constraint->allowedEvents, true)) {
                $this->context->buildViolation($constraint->notAllowedMessage)
                    ->setParameter('{{ value }}', $value)
                    ->addViolation();
            }
        }
    }
}
