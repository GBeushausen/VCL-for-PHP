<?php

/**
 * VCL for PHP - Main Entry Point (Legacy Wrapper)
 *
 * This file provides backwards compatibility for existing VCL applications.
 * It sets up paths, loads the Composer autoloader, and provides the use_unit() function.
 *
 * For new projects, use Composer autoloading directly:
 *     require_once 'vendor/autoload.php';
 *     use VCL\Core\VCLObject;
 *     use VCL\UI\Page;
 */

declare(strict_types=1);

// VCL Version
if (!defined('VCL_VERSION_MAJOR')) {
    define('VCL_VERSION_MAJOR', 3);
    define('VCL_VERSION_MINOR', 0);
    define('VCL_VERSION', VCL_VERSION_MAJOR . '.' . VCL_VERSION_MINOR);
}

// Load Composer autoloader
$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

// Calculate paths
$scriptFilename = $_SERVER['SCRIPT_FILENAME'] ?? $_SERVER['SCRIPT_NAME'] ?? '';

$fsPath = '';
$httpPath = '';

if ($scriptFilename !== '') {
    $fsPath = relativePath(realpath(__DIR__), dirname(realpath($scriptFilename)));
    $httpPath = $fsPath;

    // If VCL folder is not a subfolder, use vcl-bin alias
    if (str_starts_with($fsPath, '..') && !isset($_SERVER['FOR_PREVIEW'])) {
        $httpPath = '/vcl-bin';
    }
}

/**
 * Filesystem path to the VCL
 */
if (!defined('VCL_FS_PATH')) {
    define('VCL_FS_PATH', $fsPath);
}

/**
 * Webserver path to the VCL
 */
if (!defined('VCL_HTTP_PATH')) {
    define('VCL_HTTP_PATH', $httpPath);
}

/**
 * Returns a relative path between two directories
 */
function relativePath(string $path, string $root, string $separator = '/'): string
{
    $path = str_replace('\\', '/', $path);
    $root = str_replace('\\', '/', $root);

    $dirs = explode($separator, $path);
    $comp = explode($separator, $root);

    foreach ($comp as $i => $part) {
        if (isset($dirs[$i]) && strtolower($part) === strtolower($dirs[$i])) {
            unset($dirs[$i], $comp[$i]);
        } else {
            break;
        }
    }

    return str_repeat('..' . $separator, count($comp)) . implode($separator, $dirs);
}

/**
 * Unit mapping from legacy .inc.php files to new namespaced classes
 */
function getUnitMapping(): array
{
    return [
        'system.inc.php' => ['VCL\\Core\\VCLObject', 'VCL\\Core\\Input'],
        'classes.inc.php' => ['VCL\\Core\\Component', 'VCL\\Core\\Persistent'],
        'rtl.inc.php' => ['VCL\\Core\\RTL'],
        'controls.inc.php' => ['VCL\\UI\\Control'],
        'forms.inc.php' => ['VCL\\UI\\Page', 'VCL\\UI\\Application'],
        'stdctrls.inc.php' => ['VCL\\UI\\StdCtrls\\Button', 'VCL\\UI\\StdCtrls\\Edit'],
        'extctrls.inc.php' => ['VCL\\UI\\ExtCtrls\\Panel', 'VCL\\UI\\ExtCtrls\\Image'],
        'comctrls.inc.php' => ['VCL\\UI\\ComCtrls\\PageControl', 'VCL\\UI\\ComCtrls\\TreeView'],
        'buttons.inc.php' => ['VCL\\UI\\Buttons\\BitBtn', 'VCL\\UI\\Buttons\\SpeedButton'],
        'menus.inc.php' => ['VCL\\UI\\Menus\\MainMenu', 'VCL\\UI\\Menus\\PopupMenu'],
        'graphics.inc.php' => ['VCL\\Graphics\\Canvas', 'VCL\\Graphics\\Font'],
        'db.inc.php' => ['VCL\\Database\\DataSet', 'VCL\\Database\\DataSource'],
        'mysql.inc.php' => ['VCL\\Database\\MySQL\\MySQLDatabase'],
        'oracle.inc.php' => ['VCL\\Database\\Oracle\\OracleDatabase'],
        'interbase.inc.php' => ['VCL\\Database\\InterBase\\InterBaseDatabase'],
    ];
}

/**
 * Includes a VCL unit
 *
 * This function loads VCL modules. For new namespaced classes, it triggers
 * the Composer autoloader. For legacy units, it loads the original file.
 *
 * @deprecated Use Composer autoloading with 'use' statements instead
 */
function use_unit(string $path): void
{
    static $loadedUnits = [];
    static $legacyAliasesLoaded = false;

    $basename = basename($path);

    // Skip if already loaded
    if (isset($loadedUnits[$basename])) {
        return;
    }
    $loadedUnits[$basename] = true;

    $mapping = getUnitMapping();

    // Check if we have new namespaced classes for this unit
    if (isset($mapping[$basename])) {
        // Load legacy aliases once (for VCLObject, Input, etc.)
        if (!$legacyAliasesLoaded) {
            $aliasesPath = __DIR__ . '/src/VCL/Core/LegacyConstants.php';
            if (file_exists($aliasesPath)) {
                require_once $aliasesPath;
            }
            $aliasesPath = __DIR__ . '/src/VCL/Core/LegacyAliases.php';
            if (file_exists($aliasesPath)) {
                require_once $aliasesPath;
            }
            $legacyAliasesLoaded = true;
        }

        // Trigger autoloader for new classes
        foreach ($mapping[$basename] as $className) {
            class_exists($className, true);
        }

        // If at least one class was loaded, we're done
        if (class_exists($mapping[$basename][0] ?? '', false)) {
            return;
        }
    }

    // Fallback: load legacy file
    $basePath = defined('VCL_FS_PATH') && VCL_FS_PATH !== ''
        ? VCL_FS_PATH . '/'
        : __DIR__ . '/';

    // Try legacy folder first
    $legacyPath = __DIR__ . '/legacy/' . $path;
    if (file_exists($legacyPath)) {
        require_once $legacyPath;
        return;
    }

    // Try original location
    $originalPath = $basePath . $path;
    if (file_exists($originalPath)) {
        require_once $originalPath;
        return;
    }

    // Try absolute path
    if (file_exists($path)) {
        require_once $path;
        return;
    }

    throw new \RuntimeException("VCL unit not found: {$path}");
}

// Global startup functions (legacy)
global $startup_functions;
$startup_functions = [];

/**
 * Registers a function to be called before session start
 *
 * @deprecated Configure session handling through Application settings instead
 */
function register_startup_function(callable|string $function): void
{
    global $startup_functions;
    $startup_functions[] = $function;
}

// Load ACL if exists (for compatibility)
$aclPath = __DIR__ . '/acl.inc.php';
if (file_exists($aclPath)) {
    require_once $aclPath;
}
