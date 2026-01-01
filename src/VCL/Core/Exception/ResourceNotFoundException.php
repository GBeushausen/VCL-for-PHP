<?php

declare(strict_types=1);

namespace VCL\Core\Exception;

/**
 * Exception thrown when a resource is not found on an xml stream
 *
 * This exception is thrown by the streaming system when loading an XML resource
 * and the file doesn't exist or cannot be found.
 */
class ResourceNotFoundException extends VCLException
{
    public function __construct(string $resource, int $code = 0, ?\Throwable $previous = null)
    {
        $message = sprintf('Resource not found [%s]', $resource);
        parent::__construct($message, $code, $previous);
    }
}

// Legacy alias
class_alias(ResourceNotFoundException::class, 'EResNotFound');
