<?php

declare(strict_types=1);

namespace VCL\Security\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint for validating VCL control names.
 *
 * Control names must:
 * - Start with a letter or underscore
 * - Contain only alphanumeric characters, underscores, and hyphens
 * - Optionally be in a whitelist of allowed names
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ValidControlName extends Constraint
{
    public string $message = 'The control name "{{ value }}" is invalid.';
    public string $patternMessage = 'The control name "{{ value }}" contains invalid characters. Only letters, numbers, underscores, and hyphens are allowed.';
    public string $notAllowedMessage = 'The control name "{{ value }}" is not in the list of allowed controls.';

    /**
     * @param array $allowedNames Optional whitelist of allowed control names
     * @param bool $strict If true, the name must be in allowedNames
     */
    public function __construct(
        public array $allowedNames = [],
        public bool $strict = false,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);

        if ($message !== null) {
            $this->message = $message;
        }
    }
}
