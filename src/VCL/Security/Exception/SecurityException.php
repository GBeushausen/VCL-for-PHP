<?php

declare(strict_types=1);

namespace VCL\Security\Exception;

/**
 * Exception thrown when security validation fails.
 */
class SecurityException extends \RuntimeException
{
    public function __construct(
        string $message = 'Security validation failed',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
