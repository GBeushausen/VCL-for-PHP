<?php

/**
 * VCL StdCtrls Constants
 *
 * Legacy constants for backwards compatibility with old code.
 * New code should use the BorderStyle and CharCase enums.
 */

// Border styles (string constants for legacy compatibility)
if (!defined('bsNone')) {
    define('bsNone', 'bsNone');
}
if (!defined('bsSingle')) {
    define('bsSingle', 'bsSingle');
}

// Border styles (integer constants for new code)
if (!defined('BS_NONE')) {
    define('BS_NONE', 0);
}
if (!defined('BS_SINGLE')) {
    define('BS_SINGLE', 1);
}

// Alignment constants for CheckListBox
if (!defined('AG_NONE')) {
    define('AG_NONE', 0);
}
if (!defined('AG_LEFT')) {
    define('AG_LEFT', 1);
}
if (!defined('AG_CENTER')) {
    define('AG_CENTER', 2);
}
if (!defined('AG_RIGHT')) {
    define('AG_RIGHT', 3);
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
