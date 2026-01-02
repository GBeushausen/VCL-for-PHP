<?php

/**
 * VCL Forms Constants
 *
 * Legacy constants for backwards compatibility with old code.
 * New code should use the DocType, Directionality, and FrameBorder enums.
 */

// Document types
if (!defined('dtNone')) {
    define('dtNone', '(none)');
}
if (!defined('dtXHTML_1_0_Strict')) {
    define('dtXHTML_1_0_Strict', 'XHTML 1.0 Strict');
}
if (!defined('dtXHTML_1_0_Transitional')) {
    define('dtXHTML_1_0_Transitional', 'XHTML 1.0 Transitional');
}
if (!defined('dtXHTML_1_0_Frameset')) {
    define('dtXHTML_1_0_Frameset', 'XHTML 1.0 Frameset');
}
if (!defined('dtHTML_4_01_Strict')) {
    define('dtHTML_4_01_Strict', 'HTML 4.01 Strict');
}
if (!defined('dtHTML_4_01_Transitional')) {
    define('dtHTML_4_01_Transitional', 'HTML 4.01 Transitional');
}
if (!defined('dtHTML_4_01_Frameset')) {
    define('dtHTML_4_01_Frameset', 'HTML 4.01 Frameset');
}
if (!defined('dtXHTML_1_1')) {
    define('dtXHTML_1_1', 'XHTML 1.1');
}
if (!defined('dtHTML5')) {
    define('dtHTML5', 'HTML5');
}

// Directionality
if (!defined('ddLeftToRight')) {
    define('ddLeftToRight', 'ddLeftToRight');
}
if (!defined('ddRightToLeft')) {
    define('ddRightToLeft', 'ddRightToLeft');
}

// Frame borders
if (!defined('fbNo')) {
    define('fbNo', 'fbNo');
}
if (!defined('fbYes')) {
    define('fbYes', 'fbYes');
}
if (!defined('fbDefault')) {
    define('fbDefault', 'fbDefault');
}
