<?php
/**
 * PHPUnit Bootstrap
 *
 * VCL for PHP 3.0
 */

declare(strict_types=1);

// Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Define VCL paths for testing
if (!defined('VCL_PATH')) {
    define('VCL_PATH', dirname(__DIR__));
}

if (!defined('VCL_HTTP_PATH')) {
    define('VCL_HTTP_PATH', '/vcl');
}
