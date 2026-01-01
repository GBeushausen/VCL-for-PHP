<?php

declare(strict_types=1);

namespace VCL\RTL;

/**
 * EAbort is the exception class for errors that should not display an error message.
 *
 * Use AbortException to raise an exception without displaying an error message.
 * If applications do not trap such "silent" exceptions, the exception is passed
 * to the standard exception handler.
 *
 * The Abort() function provides a simple, standard way to raise AbortException.
 */
class AbortException extends \Exception
{
    public function __construct(string $message = 'Operation aborted', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

// Legacy alias
class_alias(AbortException::class, 'EAbort');
