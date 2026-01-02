<?php

declare(strict_types=1);

namespace VCL\Core\Exception;

/**
 * Exception thrown for Collection errors
 *
 * This exception is used by the Collection class when, for example,
 * you are trying to access an item specifying an index out of bounds.
 */
class CollectionException extends VCLException
{
    public function __construct(int|string $index, int $code = 0, ?\Throwable $previous = null)
    {
        $message = sprintf('List index out of bounds (%s)', $index);
        parent::__construct($message, $code, $previous);
    }
}

// Legacy alias
class_alias(CollectionException::class, 'ECollectionError');
