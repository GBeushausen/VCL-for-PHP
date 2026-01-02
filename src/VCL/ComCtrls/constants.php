<?php

declare(strict_types=1);

/**
 * VCL ComCtrls Constants
 *
 * Legacy constants for backward compatibility.
 * New code should use the corresponding Enum classes.
 */

// ProgressBar orientation
if (!defined('pbHorizontal')) {
    define('pbHorizontal', 'pbHorizontal');
    define('pbVertical', 'pbVertical');
}

// TrackBar orientation
if (!defined('tbHorizontal')) {
    define('tbHorizontal', 'tbHorizontal');
    define('tbVertical', 'tbVertical');
}

// PageControl tab position
if (!defined('tpTop')) {
    define('tpTop', 'tpTop');
    define('tpBottom', 'tpBottom');
}

// LabeledEdit label position
if (!defined('lpAbove')) {
    define('lpAbove', 'lpAbove');
    define('lpBelow', 'lpBelow');
}

// ListColumn cell render types
if (!defined('creEdit')) {
    define('creEdit', 'creEdit');
    define('creBoolean', 'creBoolean');
}
