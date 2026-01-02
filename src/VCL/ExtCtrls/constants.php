<?php

declare(strict_types=1);

/**
 * VCL ExtCtrls Constants
 *
 * Legacy constants for backward compatibility.
 * New code should use the corresponding Enum classes.
 *
 * @see \VCL\ExtCtrls\Enums\ShapeType
 * @see \VCL\ExtCtrls\Enums\BevelShape
 * @see \VCL\ExtCtrls\Enums\BevelStyle
 */

// Shape types
if (!defined('stRectangle')) {
    define('stRectangle', 'stRectangle');
    define('stSquare', 'stSquare');
    define('stRoundRect', 'stRoundRect');
    define('stRoundSquare', 'stRoundSquare');
    define('stEllipse', 'stEllipse');
    define('stCircle', 'stCircle');
}

// Bevel shapes
if (!defined('bsBox')) {
    define('bsBox', 'bsBox');
    define('bsFrame', 'bsFrame');
    define('bsTopLine', 'bsTopLine');
    define('bsBottomLine', 'bsBottomLine');
    define('bsLeftLine', 'bsLeftLine');
    define('bsRightLine', 'bsRightLine');
    define('bsSpacer', 'bsSpacer');
}

// Bevel styles
if (!defined('bsLowered')) {
    define('bsLowered', 'bsLowered');
    define('bsRaised', 'bsRaised');
}
