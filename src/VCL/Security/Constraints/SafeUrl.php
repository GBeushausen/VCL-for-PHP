<?php

declare(strict_types=1);

namespace VCL\Security\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint for validating safe URLs.
 *
 * Validates that URLs:
 * - Don't use dangerous schemes (javascript:, data:, vbscript:, etc.)
 * - Optionally use only allowed schemes
 * - Optionally are from allowed hosts
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class SafeUrl extends Constraint
{
    public string $message = 'The URL "{{ value }}" is not safe.';
    public string $schemeMessage = 'The URL scheme "{{ scheme }}" is not allowed. Allowed schemes: {{ schemes }}';
    public string $hostMessage = 'The host "{{ host }}" is not allowed.';
    public string $dangerousMessage = 'The URL "{{ value }}" uses a dangerous scheme.';

    /**
     * Dangerous URL schemes that are always blocked.
     */
    public const DANGEROUS_SCHEMES = [
        'javascript',
        'data',
        'vbscript',
        'file',
    ];

    /**
     * @param array $allowedSchemes Allowed URL schemes (e.g., ['http', 'https', 'mailto'])
     * @param array $allowedHosts Allowed hostnames (empty = all hosts allowed)
     * @param bool $allowRelative Allow relative URLs
     */
    public function __construct(
        public array $allowedSchemes = ['http', 'https'],
        public array $allowedHosts = [],
        public bool $allowRelative = true,
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
