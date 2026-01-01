<?php

/**
 * VCL for PHP - System Unit (Legacy Wrapper)
 *
 * This file provides backwards compatibility for existing VCL applications.
 * It loads the new namespaced classes and creates aliases for the old names.
 *
 * @deprecated Use Composer autoloading with namespaced classes instead:
 *             use VCL\Core\VCLObject;
 *             use VCL\Core\Input;
 */

declare(strict_types=1);

// Load Composer autoloader if available
$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    // Fallback: manually load required files
    require_once __DIR__ . '/src/VCL/Core/InputSource.php';
    require_once __DIR__ . '/src/VCL/Core/Exception/PropertyNotFoundException.php';
    require_once __DIR__ . '/src/VCL/Core/InputParam.php';
    require_once __DIR__ . '/src/VCL/Core/Input.php';
    require_once __DIR__ . '/src/VCL/Core/VCLObject.php';
}

// Load legacy constants and aliases
require_once __DIR__ . '/src/VCL/Core/LegacyConstants.php';
require_once __DIR__ . '/src/VCL/Core/LegacyAliases.php';

// Legacy InputFilter class (stub for compatibility)
if (!class_exists('InputFilter', false)) {
    /**
     * @deprecated Input filtering is now built into InputParam
     */
    class InputFilter
    {
        public function process(mixed $input): mixed
        {
            if (is_string($input)) {
                return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            return $input;
        }
    }
}

// Global filter function variable (legacy)
global $filter_func;
$filter_func = 'filter_var';

// Create global input instance if not exists
global $input;
if (!isset($input) || !$input instanceof \VCL\Core\Input) {
    $input = new \VCL\Core\Input();
}
