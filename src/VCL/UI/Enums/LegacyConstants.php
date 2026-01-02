<?php

declare(strict_types=1);

/**
 * Legacy constants for UI enums
 *
 * These constants provide backwards compatibility with existing code.
 * For new code, use the Enum classes directly.
 *
 * @deprecated Use VCL\UI\Enums\* enums instead
 */

// Alignment constants
if (!defined('alNone')) {
    define('alNone', 'alNone');
    define('alTop', 'alTop');
    define('alBottom', 'alBottom');
    define('alLeft', 'alLeft');
    define('alRight', 'alRight');
    define('alClient', 'alClient');
    define('alCustom', 'alCustom');
}

// Anchor group constants
if (!defined('agNone')) {
    define('agNone', 'agNone');
    define('agLeft', 'agLeft');
    define('agCenter', 'agCenter');
    define('agRight', 'agRight');
}

// Cursor constants
if (!defined('crPointer')) {
    define('crPointer', 'crPointer');
    define('crCrossHair', 'crCrossHair');
    define('crText', 'crText');
    define('crWait', 'crWait');
    define('crDefault', 'crDefault');
    define('crHelp', 'crHelp');
    define('crEResize', 'crE-Resize');
    define('crNEResize', 'crNE-Resize');
    define('crNResize', 'crN-Resize');
    define('crNWResize', 'crNW-Resize');
    define('crWResize', 'crW-Resize');
    define('crSWResize', 'crSW-Resize');
    define('crSResize', 'crS-Resize');
    define('crSEResize', 'crSE-Resize');
    define('crAuto', 'crAuto');
}

// Border style constants
if (!defined('bsNone')) {
    define('bsNone', 'bsNone');
    define('bsSingle', 'bsSingle');
    define('bsBox', 'bsBox');
    define('bsFrame', 'bsFrame');
    define('bsTopLine', 'bsTopLine');
    define('bsBottomLine', 'bsBottomLine');
    define('bsLeftLine', 'bsLeftLine');
    define('bsRightLine', 'bsRightLine');
    define('bsSpacer', 'bsSpacer');
    define('bsLowered', 'bsLowered');
    define('bsRaised', 'bsRaised');
}

// Edit char case constants
if (!defined('ecNormal')) {
    define('ecNormal', 'ecNormal');
    define('ecLowerCase', 'ecLowerCase');
    define('ecUpperCase', 'ecUpperCase');
}

// Button type constants
if (!defined('btSubmit')) {
    define('btSubmit', 'btSubmit');
    define('btReset', 'btReset');
    define('btNormal', 'btNormal');
}
