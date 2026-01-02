<?php

declare(strict_types=1);

namespace VCL\Database;

use Exception;
use VCL\Core\Component;

/**
 * Exception for database errors.
 *
 * This exception is raised whenever a data access component generates an error.
 */
class EDatabaseError extends Exception
{
    /**
     * Create a database error with component context.
     */
    public static function raise(string $message, ?Component $component = null): never
    {
        if ($component !== null && $component->Name !== '') {
            throw new self(sprintf('%s: %s', $component->Name, $message));
        }
        throw new self($message);
    }
}

/**
 * Function to raise a Database Error.
 *
 * @param string $message Message of the exception to show
 * @param Component|null $component Component raising the exception
 */
function databaseError(string $message, ?Component $component = null): never
{
    EDatabaseError::raise($message, $component);
}
