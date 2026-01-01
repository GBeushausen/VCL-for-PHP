<?php

/**
 * VCL Core Constants
 *
 * This file defines constants used throughout the VCL framework.
 */

// Component state constants
if (!defined('CS_LOADING')) {
    define('CS_LOADING', 1);
    define('CS_DESIGNING', 2);

    // Legacy aliases (lowercase)
    define('csLoading', CS_LOADING);
    define('csDesigning', CS_DESIGNING);
}

// Global settings
if (!defined('VCL_EXCEPTIONS_ENABLED')) {
    define('VCL_EXCEPTIONS_ENABLED', true);
}

// Initialize global settings if not already set
global $exceptions_enabled, $use_html_entity_decode, $output_enabled, $checkduplicatenames;

if (!isset($exceptions_enabled)) {
    $exceptions_enabled = true;
}
if (!isset($use_html_entity_decode)) {
    $use_html_entity_decode = true;
}
if (!isset($output_enabled)) {
    $output_enabled = true;
}
if (!isset($checkduplicatenames)) {
    $checkduplicatenames = true;
}
