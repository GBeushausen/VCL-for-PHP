<?php

/**
 * VCL Graphics Constants
 *
 * This file defines constants used by the Graphics classes for backwards compatibility.
 */

// Text alignment
if (!defined('taNone')) {
    define('taNone', 'taNone');
    define('taLeft', 'taLeft');
    define('taCenter', 'taCenter');
    define('taRight', 'taRight');
    define('taJustify', 'taJustify');
}

// Font style
if (!defined('fsNormal')) {
    define('fsNormal', 'fsNormal');
    define('fsItalic', 'fsItalic');
    define('fsOblique', 'fsOblique');
}

// Text case
if (!defined('caCapitalize')) {
    define('caCapitalize', 'caCapitalize');
    define('caUpperCase', 'caUpperCase');
    define('caLowerCase', 'caLowerCase');
    define('caNone', 'caNone');
}

// Font variant
if (!defined('vaNormal')) {
    define('vaNormal', 'vaNormal');
    define('vaSmallCaps', 'vaSmallCaps');
}

// Pen style
if (!defined('psDash')) {
    define('psDash', 'psDash');
    define('psDashDot', 'psDashDot');
    define('psDashDotDot', 'psDashDotDot');
    define('psDot', 'psDot');
    define('psSolid', 'psSolid');
}

// Layout types
if (!defined('FLOW_LAYOUT')) {
    define('FLOW_LAYOUT', 'FLOW_LAYOUT');
    define('XY_LAYOUT', 'XY_LAYOUT');
    define('ABS_XY_LAYOUT', 'ABS_XY_LAYOUT');
    define('REL_XY_LAYOUT', 'REL_XY_LAYOUT');
    define('GRIDBAG_LAYOUT', 'GRIDBAG_LAYOUT');
    define('ROW_LAYOUT', 'ROW_LAYOUT');
    define('COL_LAYOUT', 'COL_LAYOUT');
}
