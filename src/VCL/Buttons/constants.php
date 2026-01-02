<?php

declare(strict_types=1);

/**
 * VCL Buttons Constants
 *
 * Legacy constants for backward compatibility.
 * New code should use the corresponding Enum classes.
 */

// Button layout
if (!defined('blImageBottom')) {
    define('blImageBottom', 'blImageBottom');
    define('blImageLeft', 'blImageLeft');
    define('blImageRight', 'blImageRight');
    define('blImageTop', 'blImageTop');
}

// Button kinds
if (!defined('bkCustom')) {
    define('bkCustom', 'bkCustom');
    define('bkOK', 'bkOK');
    define('bkCancel', 'bkCancel');
    define('bkYes', 'bkYes');
    define('bkNo', 'bkNo');
    define('bkHelp', 'bkHelp');
    define('bkClose', 'bkClose');
    define('bkAbort', 'bkAbort');
    define('bkRetry', 'bkRetry');
    define('bkIgnore', 'bkIgnore');
    define('bkAll', 'bkAll');
}

// Button types
if (!defined('btSubmit')) {
    define('btSubmit', 'btSubmit');
    define('btReset', 'btReset');
    define('btNormal', 'btNormal');
}
