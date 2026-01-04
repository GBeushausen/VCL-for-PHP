<?php

declare(strict_types=1);

/**
 * VCL for PHP Bootstrap File
 *
 * This file bridges the legacy VCL system with modern Composer autoloading.
 * It provides backwards compatibility for existing applications while enabling
 * the new namespace-based architecture.
 */

// Define relativePath first (may already be defined by vcl.inc.php)
if (!function_exists('relativePath')) {
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
}

// Define use_unit if not already defined by vcl.inc.php
if (!function_exists('use_unit')) {
    /**
     * Legacy unit loader - maps old .inc.php files to new namespaced classes
     *
     * @deprecated Use Composer autoloading with namespaced classes instead
     */
    function use_unit(string $path): void
    {
        static $unitMap = [
            'system.inc.php' => \VCL\Core\VCLObject::class,
            'classes.inc.php' => \VCL\Core\Component::class,
            'rtl.inc.php' => \VCL\RTL\Functions::class,
            'controls.inc.php' => \VCL\UI\Control::class,
            'forms.inc.php' => \VCL\Forms\Page::class,
            'stdctrls.inc.php' => \VCL\StdCtrls\Button::class,
            'extctrls.inc.php' => \VCL\ExtCtrls\Panel::class,
            'comctrls.inc.php' => \VCL\ComCtrls\PageControl::class,
            'buttons.inc.php' => \VCL\Buttons\BitBtn::class,
            'menus.inc.php' => \VCL\Menus\MainMenu::class,
            'graphics.inc.php' => \VCL\Graphics\Canvas::class,
            'db.inc.php' => \VCL\Database\DataSet::class,
            'dbctrls.inc.php' => \VCL\DBCtrls\DBPaginator::class,
            'dbgrids.inc.php' => \VCL\DBGrids\DBGrid::class,
            'auth.inc.php' => \VCL\Auth\BasicAuthentication::class,
            'styles.inc.php' => \VCL\Styles\StyleSheet::class,
            'mysql.inc.php' => \VCL\Database\MySQL\MySQLDatabase::class,
            'oracle.inc.php' => \VCL\Database\Oracle\OracleDatabase::class,
            'interbase.inc.php' => \VCL\Database\InterBase\InterBaseDatabase::class,
        ];

        $basename = basename($path);

        // If mapped to new class, trigger autoloader by referencing the class
        if (isset($unitMap[$basename]) && class_exists($unitMap[$basename])) {
            return;
        }

        // Fallback: load legacy file if it exists
        $vclPath = defined('VCL_FS_PATH') ? VCL_FS_PATH : dirname(__DIR__);
        $legacyPath = $vclPath . '/legacy/' . $path;
        if (file_exists($legacyPath)) {
            require_once $legacyPath;
            return;
        }

        // Ultimate fallback: try original location (during migration)
        $originalPath = $vclPath . '/' . $path;
        if (file_exists($originalPath)) {
            require_once $originalPath;
            return;
        }

        throw new \RuntimeException("VCL unit not found: {$path}");
    }
}

// Define register_startup_function if not already defined
if (!function_exists('register_startup_function')) {
    /**
     * Startup functions registry (legacy compatibility)
     */
    $GLOBALS['startup_functions'] = $GLOBALS['startup_functions'] ?? [];

    /**
     * Register a function to be called before session start
     *
     * @deprecated Configure session handling through Application settings instead
     */
    function register_startup_function(callable|string $function): void
    {
        $GLOBALS['startup_functions'][] = $function;
    }
}

// Define VCL constants if not already defined (vcl.inc.php defines these)
if (!defined('VCL_VERSION_MAJOR')) {
    define('VCL_VERSION_MAJOR', 3);
    define('VCL_VERSION_MINOR', 0);
    define('VCL_VERSION', VCL_VERSION_MAJOR . '.' . VCL_VERSION_MINOR);
}

if (!defined('VCL_FS_PATH')) {
    define('VCL_FS_PATH', dirname(__DIR__));
}

if (!defined('VCL_HTTP_PATH')) {
    $httpPath = '';
    if (PHP_SAPI !== 'cli') {
        $scriptFilename = $_SERVER['SCRIPT_FILENAME'] ?? $_SERVER['SCRIPT_NAME'] ?? '';
        if ($scriptFilename !== '' && function_exists('relativePath')) {
            $vclRoot = realpath(dirname(__DIR__));
            $scriptDir = dirname(realpath($scriptFilename));
            if ($vclRoot !== false && $scriptDir !== false) {
                $httpPath = relativePath($vclRoot, $scriptDir);
            }
        }
    }
    define('VCL_HTTP_PATH', $httpPath);
}

// Create global input instance
global $input;
if (!isset($input) || !$input instanceof \VCL\Core\Input) {
    $input = new \VCL\Core\Input();
}
