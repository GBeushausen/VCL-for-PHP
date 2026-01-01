<?php

declare(strict_types=1);

namespace VCL\Core\Exception;

/**
 * Exception thrown when a component has the same name on the same owner
 *
 * This exception is usually thrown by the Name property when it detects
 * there are two objects that have the same Name.
 */
class DuplicateNameException extends VCLException
{
    public function __construct(string $name, int $code = 0, ?\Throwable $previous = null)
    {
        $message = sprintf('A component named %s already exists', $name);
        parent::__construct($message, $code, $previous);
    }
}

// Legacy alias
class_alias(DuplicateNameException::class, 'ENameDuplicated');
