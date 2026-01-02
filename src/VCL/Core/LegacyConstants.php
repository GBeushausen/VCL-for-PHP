<?php

declare(strict_types=1);

/**
 * Legacy constants for backwards compatibility
 *
 * These constants map to the new Enum values and will be deprecated
 * in a future version. Use the Enum classes directly instead.
 *
 * @deprecated Use VCL\Core\InputSource enum instead
 */

// Define in global namespace for backwards compatibility
if (!defined('sGET')) {
    define('sGET', 0);
    define('sPOST', 1);
    define('sREQUEST', 2);
    define('sCOOKIES', 3);
    define('sSERVER', 4);
}
