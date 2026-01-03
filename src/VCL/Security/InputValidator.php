<?php

declare(strict_types=1);

namespace VCL\Security;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use VCL\Security\Constraints\ValidControlName;
use VCL\Security\Constraints\ValidEventName;
use VCL\Security\Constraints\SafeUrl;
use VCL\Security\Exception\SecurityException;

/**
 * Input validation wrapper using Symfony Validator.
 *
 * Provides convenient methods for validating user input with
 * proper security constraints.
 *
 * Usage:
 *   $validator = new InputValidator();
 *
 *   // Validate with exceptions
 *   $controlName = $validator->validateControlName($input);
 *
 *   // Validate without exceptions
 *   if ($validator->isValidControlName($input)) { ... }
 *
 *   // Get validation errors
 *   $errors = $validator->getErrors($input, [new Assert\NotBlank()]);
 */
class InputValidator
{
    private static ?self $instance = null;

    private ValidatorInterface $validator;

    public function __construct()
    {
        $this->validator = Validation::createValidator();
    }

    // =========================================================================
    // GENERIC VALIDATION METHODS
    // =========================================================================

    /**
     * Validate a value against constraints.
     *
     * @param mixed $value The value to validate
     * @param array $constraints Array of constraints
     * @return ConstraintViolationListInterface
     */
    public function validate(mixed $value, array $constraints): ConstraintViolationListInterface
    {
        return $this->validator->validate($value, $constraints);
    }

    /**
     * Check if a value is valid against constraints.
     *
     * @param mixed $value The value to validate
     * @param array $constraints Array of constraints
     * @return bool True if valid
     */
    public function isValid(mixed $value, array $constraints): bool
    {
        return $this->validate($value, $constraints)->count() === 0;
    }

    /**
     * Get validation error messages.
     *
     * @param mixed $value The value to validate
     * @param array $constraints Array of constraints
     * @return array Array of error messages
     */
    public function getErrors(mixed $value, array $constraints): array
    {
        $violations = $this->validate($value, $constraints);
        $errors = [];

        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }

    /**
     * Validate and throw exception on failure.
     *
     * @param mixed $value The value to validate
     * @param array $constraints Array of constraints
     * @param string $fieldName Name of the field for error message
     * @return mixed The validated value
     * @throws SecurityException If validation fails
     */
    public function validateOrFail(mixed $value, array $constraints, string $fieldName = 'value'): mixed
    {
        $violations = $this->validate($value, $constraints);

        if ($violations->count() > 0) {
            $message = sprintf(
                'Validation failed for %s: %s',
                $fieldName,
                $violations->get(0)->getMessage()
            );
            throw new SecurityException($message);
        }

        return $value;
    }

    // =========================================================================
    // VCL-SPECIFIC VALIDATION METHODS
    // =========================================================================

    /**
     * Validate a control name.
     *
     * @param ?string $name The control name
     * @param array $allowedNames Optional whitelist of allowed names
     * @return string The validated name
     * @throws SecurityException If invalid
     */
    public function validateControlName(?string $name, array $allowedNames = []): string
    {
        if ($name === null || $name === '') {
            throw new SecurityException('Control name cannot be empty');
        }

        $constraints = [
            new Assert\NotBlank(),
            new ValidControlName(allowedNames: $allowedNames),
        ];

        return $this->validateOrFail($name, $constraints, 'control name');
    }

    /**
     * Check if a control name is valid.
     *
     * @param ?string $name The control name
     * @param array $allowedNames Optional whitelist
     * @return bool
     */
    public function isValidControlName(?string $name, array $allowedNames = []): bool
    {
        if ($name === null || $name === '') {
            return false;
        }

        return $this->isValid($name, [
            new Assert\NotBlank(),
            new ValidControlName(allowedNames: $allowedNames),
        ]);
    }

    /**
     * Validate an event name.
     *
     * @param ?string $name The event name
     * @param array $allowedEvents Optional whitelist
     * @return string The validated name
     * @throws SecurityException If invalid
     */
    public function validateEventName(?string $name, array $allowedEvents = []): string
    {
        if ($name === null || $name === '') {
            throw new SecurityException('Event name cannot be empty');
        }

        $constraints = [
            new Assert\NotBlank(),
            new ValidEventName(allowedEvents: $allowedEvents),
        ];

        return $this->validateOrFail($name, $constraints, 'event name');
    }

    /**
     * Check if an event name is valid.
     *
     * @param ?string $name The event name
     * @param array $allowedEvents Optional whitelist
     * @return bool
     */
    public function isValidEventName(?string $name, array $allowedEvents = []): bool
    {
        if ($name === null || $name === '') {
            return false;
        }

        return $this->isValid($name, [
            new Assert\NotBlank(),
            new ValidEventName(allowedEvents: $allowedEvents),
        ]);
    }

    /**
     * Validate a URL for safe usage.
     *
     * @param ?string $url The URL
     * @param array $allowedSchemes Allowed URL schemes
     * @param array $allowedHosts Optional whitelist of hosts
     * @return string The validated URL
     * @throws SecurityException If invalid
     */
    public function validateUrl(?string $url, array $allowedSchemes = ['http', 'https'], array $allowedHosts = []): string
    {
        if ($url === null || $url === '') {
            throw new SecurityException('URL cannot be empty');
        }

        $constraints = [
            new Assert\NotBlank(),
            new SafeUrl(allowedSchemes: $allowedSchemes, allowedHosts: $allowedHosts),
        ];

        return $this->validateOrFail($url, $constraints, 'URL');
    }

    /**
     * Check if a URL is safe.
     *
     * @param ?string $url The URL
     * @param array $allowedSchemes Allowed schemes
     * @param array $allowedHosts Optional host whitelist
     * @return bool
     */
    public function isValidUrl(?string $url, array $allowedSchemes = ['http', 'https'], array $allowedHosts = []): bool
    {
        if ($url === null || $url === '') {
            return false;
        }

        return $this->isValid($url, [
            new Assert\NotBlank(),
            new SafeUrl(allowedSchemes: $allowedSchemes, allowedHosts: $allowedHosts),
        ]);
    }

    /**
     * Validate an email address.
     *
     * @param ?string $email The email
     * @return string The validated email
     * @throws SecurityException If invalid
     */
    public function validateEmail(?string $email): string
    {
        if ($email === null || $email === '') {
            throw new SecurityException('Email cannot be empty');
        }

        return $this->validateOrFail($email, [
            new Assert\NotBlank(),
            new Assert\Email(mode: Assert\Email::VALIDATION_MODE_HTML5),
        ], 'email');
    }

    /**
     * Check if an email is valid.
     *
     * @param ?string $email The email
     * @return bool
     */
    public function isValidEmail(?string $email): bool
    {
        if ($email === null || $email === '') {
            return false;
        }

        return $this->isValid($email, [
            new Assert\NotBlank(),
            new Assert\Email(mode: Assert\Email::VALIDATION_MODE_HTML5),
        ]);
    }

    /**
     * Validate an integer within a range.
     *
     * @param mixed $value The value
     * @param int|null $min Minimum value
     * @param int|null $max Maximum value
     * @return int The validated integer
     * @throws SecurityException If invalid
     */
    public function validateInteger(mixed $value, ?int $min = null, ?int $max = null): int
    {
        $constraints = [
            new Assert\Type('numeric'),
        ];

        if ($min !== null) {
            $constraints[] = new Assert\GreaterThanOrEqual($min);
        }

        if ($max !== null) {
            $constraints[] = new Assert\LessThanOrEqual($max);
        }

        $this->validateOrFail($value, $constraints, 'integer');

        return (int) $value;
    }

    /**
     * Validate a string with length constraints.
     *
     * @param ?string $value The string
     * @param int $minLength Minimum length
     * @param int $maxLength Maximum length
     * @return string The validated string
     * @throws SecurityException If invalid
     */
    public function validateString(?string $value, int $minLength = 0, int $maxLength = 65535): string
    {
        if ($value === null) {
            $value = '';
        }

        $constraints = [
            new Assert\Length(min: $minLength, max: $maxLength),
        ];

        if ($minLength > 0) {
            $constraints[] = new Assert\NotBlank();
        }

        return $this->validateOrFail($value, $constraints, 'string');
    }

    /**
     * Validate a value matches a pattern.
     *
     * @param ?string $value The value
     * @param string $pattern The regex pattern
     * @param string $message Error message
     * @return string The validated value
     * @throws SecurityException If invalid
     */
    public function validatePattern(?string $value, string $pattern, string $message = 'Invalid format'): string
    {
        if ($value === null || $value === '') {
            throw new SecurityException('Value cannot be empty');
        }

        return $this->validateOrFail($value, [
            new Assert\NotBlank(),
            new Assert\Regex(pattern: $pattern, message: $message),
        ], 'value');
    }

    // =========================================================================
    // STATIC FACADE
    // =========================================================================

    /**
     * Get the singleton instance.
     */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Set a custom instance (for testing).
     */
    public static function setInstance(?self $instance): void
    {
        self::$instance = $instance;
    }

    /**
     * Reset to default instance.
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    /**
     * Get the underlying Symfony validator.
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }
}
