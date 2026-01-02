<?php

declare(strict_types=1);

namespace VCL\Core;

/**
 * Defines the source of user input
 */
enum InputSource: int
{
    case GET = 0;
    case POST = 1;
    case REQUEST = 2;
    case COOKIES = 3;
    case SERVER = 4;

    /**
     * Get the superglobal array for this source
     */
    public function getArray(): array
    {
        return match ($this) {
            self::GET => $_GET,
            self::POST => $_POST,
            self::REQUEST => $_REQUEST,
            self::COOKIES => $_COOKIE,
            self::SERVER => $_SERVER,
        };
    }
}
