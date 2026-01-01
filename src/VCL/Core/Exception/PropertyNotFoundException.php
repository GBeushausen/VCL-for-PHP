<?php

declare(strict_types=1);

namespace VCL\Core\Exception;

use Exception;

/**
 * Exception thrown when trying to access a property not defined
 */
class PropertyNotFoundException extends Exception
{
    public function __construct(
        public readonly string $className,
        public readonly string $propertyName,
        int $code = 0,
        ?Exception $previous = null
    ) {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $call = $backtrace[2] ?? $backtrace[1] ?? $backtrace[0];
        $file = basename($call['file'] ?? 'unknown');
        $line = $call['line'] ?? 0;

        $message = sprintf(
            'Trying to access non-existent property %s->%s in %s, line %d.',
            $this->className,
            $this->propertyName,
            $file,
            $line
        );

        parent::__construct($message, $code, $previous);
    }
}
