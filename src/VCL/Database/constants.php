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

// Driver type constants (legacy compatibility with ADOdb driver names)
if (!defined('DB_DRIVER_MYSQL')) {
    define('DB_DRIVER_MYSQL', 'mysql');
    define('DB_DRIVER_MYSQLI', 'mysqli');
    define('DB_DRIVER_PGSQL', 'pgsql');
    define('DB_DRIVER_POSTGRES', 'postgres');
    define('DB_DRIVER_SQLITE', 'sqlite');
    define('DB_DRIVER_SQLITE3', 'sqlite3');
    define('DB_DRIVER_SQLSRV', 'sqlsrv');
    define('DB_DRIVER_MSSQL', 'mssql');
    define('DB_DRIVER_OCI8', 'oci8');
    define('DB_DRIVER_ORACLE', 'oracle');
    define('DB_DRIVER_MARIADB', 'mariadb');
}

// Fetch mode constants (legacy compatibility)
if (!defined('DB_FETCHMODE_ASSOC')) {
    define('DB_FETCHMODE_DEFAULT', 0);
    define('DB_FETCHMODE_ORDERED', 1);
    define('DB_FETCHMODE_ASSOC', 2);
    define('DB_FETCHMODE_BOTH', 3);
}

// Auto-query constants (for insert/update operations)
if (!defined('DB_AUTOQUERY_INSERT')) {
    define('DB_AUTOQUERY_INSERT', 1);
    define('DB_AUTOQUERY_UPDATE', 2);
}

// Database error constants
if (!defined('DB_ERROR')) {
    define('DB_OK', 1);
    define('DB_ERROR', -1);
    define('DB_ERROR_SYNTAX', -2);
    define('DB_ERROR_CONSTRAINT', -3);
    define('DB_ERROR_NOT_FOUND', -4);
    define('DB_ERROR_ALREADY_EXISTS', -5);
    define('DB_ERROR_UNSUPPORTED', -6);
    define('DB_ERROR_MISMATCH', -7);
    define('DB_ERROR_INVALID', -8);
    define('DB_ERROR_NOT_CAPABLE', -9);
    define('DB_ERROR_TRUNCATED', -10);
    define('DB_ERROR_INVALID_NUMBER', -11);
    define('DB_ERROR_INVALID_DATE', -12);
    define('DB_ERROR_DIVZERO', -13);
    define('DB_ERROR_NODBSELECTED', -14);
    define('DB_ERROR_CANNOT_CREATE', -15);
    define('DB_ERROR_CANNOT_DELETE', -16);
    define('DB_ERROR_CANNOT_DROP', -17);
    define('DB_ERROR_NOSUCHTABLE', -18);
    define('DB_ERROR_NOSUCHFIELD', -19);
    define('DB_ERROR_NEED_MORE_DATA', -20);
    define('DB_ERROR_NOT_LOCKED', -21);
    define('DB_ERROR_VALUE_COUNT_ON_ROW', -22);
    define('DB_ERROR_INVALID_DSN', -23);
    define('DB_ERROR_CONNECT_FAILED', -24);
    define('DB_ERROR_EXTENSION_NOT_FOUND', -25);
    define('DB_ERROR_NOSUCHDB', -26);
    define('DB_ERROR_ACCESS_VIOLATION', -27);
    define('DB_ERROR_DEADLOCK', -28);
}

// Table info constants
if (!defined('DB_TABLEINFO_ORDER')) {
    define('DB_TABLEINFO_ORDER', 1);
    define('DB_TABLEINFO_ORDERTABLE', 2);
    define('DB_TABLEINFO_FULL', 3);
}

// Transaction isolation levels
if (!defined('DB_ISOLATION_READ_UNCOMMITTED')) {
    define('DB_ISOLATION_READ_UNCOMMITTED', 1);
    define('DB_ISOLATION_READ_COMMITTED', 2);
    define('DB_ISOLATION_REPEATABLE_READ', 3);
    define('DB_ISOLATION_SERIALIZABLE', 4);
}
