<?php

/**
 * VCL StdCtrls Constants
 *
 * Legacy constants for backwards compatibility with old code.
 * New code should use the BorderStyle and CharCase enums.
 */

// Border styles
if (!defined('bsNone')) {
    define('bsNone', 'bsNone');
}
if (!defined('bsSingle')) {
    define('bsSingle', 'bsSingle');
}

// Char case
if (!defined('ecNormal')) {
    define('ecNormal', 'ecNormal');
}
if (!defined('ecLowerCase')) {
    define('ecLowerCase', 'ecLowerCase');
}
if (!defined('ecUpperCase')) {
    define('ecUpperCase', 'ecUpperCase');
}
