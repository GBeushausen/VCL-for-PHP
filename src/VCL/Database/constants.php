<?php

declare(strict_types=1);

/**
 * VCL Database Constants
 *
 * Legacy constants for backward compatibility.
 * New code should use the corresponding Enum classes.
 */

// Data events
if (!defined('deFieldChange')) {
    define('deFieldChange', 1);
    define('deRecordChange', 2);
    define('deDataSetChange', 3);
    define('deDataSetScroll', 4);
    define('deLayoutChange', 5);
    define('deUpdateRecord', 6);
    define('deUpdateState', 7);
    define('deCheckBrowseMode', 8);
    define('dePropertyChange', 9);
    define('deFieldListChange', 10);
    define('deFocusControl', 11);
    define('deParentScroll', 12);
    define('deConnectChange', 13);
    define('deReconcileError', 14);
    define('deDisabledStateChange', 15);
}

// DataSet states
if (!defined('dsInactive')) {
    define('dsInactive', 1);
    define('dsBrowse', 2);
    define('dsEdit', 3);
    define('dsInsert', 4);
    define('dsSetKey', 5);
    define('dsCalcFields', 6);
    define('dsFilter', 7);
    define('dsNewValue', 8);
    define('dsOldValue', 9);
    define('dsCurValue', 10);
    define('dsBlockRead', 11);
    define('dsInternalCalc', 12);
    define('dsOpening', 13);
}
