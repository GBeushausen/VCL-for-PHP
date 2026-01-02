<?php

declare(strict_types=1);

namespace VCL\Core\Exception;

/**
 * Exception thrown when trying to assign an object to another
 *
 * This exception is thrown by the assign method when it's impossible
 * to assign the objects you are trying to assign.
 */
class AssignException extends VCLException
{
    public function __construct(string $sourceName, string $targetClass, ?\Throwable $previous = null)
    {
        $message = sprintf('Cannot assign a %s to a %s', $sourceName, $targetClass);
        parent::__construct($message, 0, $previous);
    }
}

// Legacy alias
class_alias(AssignException::class, 'EAssignError');
