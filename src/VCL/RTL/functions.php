<?php

/**
 * VCL RTL Global Functions
 *
 * This file provides global function aliases for backwards compatibility.
 * All functions delegate to the VCL\RTL\Helpers static methods.
 */

use VCL\RTL\Helpers;
use VCL\RTL\AbortException;

if (!function_exists('boolToStr')) {
    /**
     * Converts PHP boolean into a JavaScript compatible boolean string.
     */
    function boolToStr($value): string
    {
        return Helpers::boolToStr((bool)$value);
    }
}

if (!function_exists('textToHtml')) {
    /**
     * Converts plain text to HTML.
     */
    function textToHtml($text, $charset = null): string
    {
        return Helpers::textToHtml((string)$text, $charset);
    }
}

if (!function_exists('htmlToText')) {
    /**
     * Converts HTML to plain text.
     */
    function htmlToText($text): string
    {
        return Helpers::htmlToText((string)$text);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirects the browser to a project file.
     */
    function redirect($file): never
    {
        Helpers::redirect((string)$file);
    }
}

if (!function_exists('assigned')) {
    /**
     * Check if an object/variable is not null.
     */
    function assigned($var): bool
    {
        return Helpers::assigned($var);
    }
}

if (!function_exists('extractjscript')) {
    /**
     * Extracts JavaScript code from an HTML document.
     */
    function extractjscript($html): array
    {
        return Helpers::extractJScript((string)$html);
    }
}

if (!function_exists('safeunserialize')) {
    /**
     * Safe unserialize with DBCS fallback.
     */
    function safeunserialize($input): mixed
    {
        return Helpers::safeUnserialize((string)$input);
    }
}

if (!function_exists('__unserialize')) {
    /**
     * DBCS-safe unserialize.
     */
    function __unserialize($sObject): mixed
    {
        return Helpers::dbcsUnserialize((string)$sObject);
    }
}

if (!function_exists('Abort')) {
    /**
     * Throws a silent exception.
     */
    function Abort(): never
    {
        throw new AbortException();
    }
}
