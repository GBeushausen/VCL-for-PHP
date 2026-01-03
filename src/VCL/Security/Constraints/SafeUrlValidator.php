<?php

declare(strict_types=1);

namespace VCL\Security\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validator for SafeUrl constraint.
 */
class SafeUrlValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof SafeUrl) {
            throw new UnexpectedTypeException($constraint, SafeUrl::class);
        }

        // Null and empty string are handled by NotBlank constraint
        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $url = trim($value);

        // Check for dangerous schemes first
        if ($this->isDangerousScheme($url)) {
            $this->context->buildViolation($constraint->dangerousMessage)
                ->setParameter('{{ value }}', $this->truncateUrl($url))
                ->addViolation();
            return;
        }

        // Handle relative URLs
        if ($this->isRelativeUrl($url)) {
            if (!$constraint->allowRelative) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->truncateUrl($url))
                    ->addViolation();
            }
            // Relative URLs are safe if allowed
            return;
        }

        // Parse absolute URL
        $parsed = parse_url($url);

        if ($parsed === false) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->truncateUrl($url))
                ->addViolation();
            return;
        }

        // Check scheme
        $scheme = strtolower($parsed['scheme'] ?? '');
        if ($scheme !== '' && !in_array($scheme, $constraint->allowedSchemes, true)) {
            $this->context->buildViolation($constraint->schemeMessage)
                ->setParameter('{{ scheme }}', $scheme)
                ->setParameter('{{ schemes }}', implode(', ', $constraint->allowedSchemes))
                ->addViolation();
            return;
        }

        // Check host if whitelist is provided
        if (!empty($constraint->allowedHosts)) {
            $host = strtolower($parsed['host'] ?? '');
            if ($host !== '' && !$this->isHostAllowed($host, $constraint->allowedHosts)) {
                $this->context->buildViolation($constraint->hostMessage)
                    ->setParameter('{{ host }}', $host)
                    ->addViolation();
            }
        }
    }

    /**
     * Check if URL uses a dangerous scheme.
     */
    private function isDangerousScheme(string $url): bool
    {
        $lowercaseUrl = strtolower(ltrim($url));

        foreach (SafeUrl::DANGEROUS_SCHEMES as $scheme) {
            if (str_starts_with($lowercaseUrl, $scheme . ':')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if URL is relative.
     */
    private function isRelativeUrl(string $url): bool
    {
        // Starts with /, ./, ../, or #
        return preg_match('/^(?:\/|\.{1,2}\/|#)/', $url) === 1;
    }

    /**
     * Check if host is in allowed list.
     */
    private function isHostAllowed(string $host, array $allowedHosts): bool
    {
        foreach ($allowedHosts as $allowedHost) {
            $allowedHost = strtolower($allowedHost);

            // Exact match
            if ($host === $allowedHost) {
                return true;
            }

            // Wildcard subdomain match (e.g., *.example.com)
            if (str_starts_with($allowedHost, '*.')) {
                $domain = substr($allowedHost, 2);
                if ($host === $domain || str_ends_with($host, '.' . $domain)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Truncate long URLs for display in error messages.
     */
    private function truncateUrl(string $value): string
    {
        if (strlen($value) > 65) {
            return substr($value, 0, 65) . '...';
        }
        return $value;
    }
}
