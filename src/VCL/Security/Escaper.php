<?php

declare(strict_types=1);

namespace VCL\Security;

/**
 * Context-aware escaping utility for preventing XSS and injection attacks.
 *
 * This class provides both static methods (for convenience) and instance methods
 * (for dependency injection and testing).
 *
 * Usage:
 *   // Static facade (simple usage)
 *   echo Escaper::html($userInput);
 *   echo Escaper::js($value);
 *
 *   // Instance methods (for DI/testing)
 *   $escaper = new Escaper();
 *   echo $escaper->escapeHtml($userInput);
 *
 *   // Replace instance for testing
 *   Escaper::setInstance($mockEscaper);
 */
class Escaper
{
    private static ?self $instance = null;

    /**
     * JSON encoding flags for JavaScript escaping.
     * These flags ensure safe embedding in HTML script tags and attributes.
     */
    private const JSON_FLAGS = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_THROW_ON_ERROR;

    /**
     * Allowed CSS color patterns for validation.
     */
    private const CSS_COLOR_PATTERN = '/^(#[0-9a-fA-F]{3,8}|rgba?\([^)]+\)|hsla?\([^)]+\)|[a-zA-Z]+|transparent|inherit|initial|unset)$/';

    /**
     * Allowed CSS length units.
     */
    private const CSS_LENGTH_PATTERN = '/^-?\d+(\.\d+)?(px|em|rem|%|vh|vw|pt|cm|mm|in|auto)?$/';

    /**
     * Dangerous URL schemes that should be blocked.
     */
    private const DANGEROUS_SCHEMES = ['javascript:', 'data:', 'vbscript:', 'file:'];

    public function __construct()
    {
        // Empty constructor for DI compatibility
    }

    // =========================================================================
    // INSTANCE METHODS (for Dependency Injection)
    // =========================================================================

    /**
     * Escape string for HTML text content.
     *
     * @param string $string The string to escape
     * @return string Escaped string safe for HTML text nodes
     */
    public function escapeHtml(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    /**
     * Escape string for use in HTML attributes.
     * Same as escapeHtml but semantically distinct for clarity.
     *
     * @param string $string The string to escape
     * @return string Escaped string safe for HTML attributes
     */
    public function escapeAttr(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    /**
     * Escape value for embedding in JavaScript code.
     * Uses JSON encoding with safe flags for HTML context.
     *
     * @param mixed $value The value to escape (string, int, array, etc.)
     * @return string JSON-encoded string safe for JavaScript context
     */
    public function escapeJs(mixed $value): string
    {
        return json_encode($value, self::JSON_FLAGS);
    }

    /**
     * Escape string for use in JavaScript string literals.
     * Returns the value without surrounding quotes.
     *
     * @param string $string The string to escape
     * @return string Escaped string for JS string literal (without quotes)
     */
    public function escapeJsString(string $string): string
    {
        $encoded = json_encode($string, self::JSON_FLAGS);
        // Remove surrounding quotes from JSON string
        return substr($encoded, 1, -1);
    }

    /**
     * Escape and validate CSS value.
     * Only allows safe CSS values (colors, lengths, keywords).
     *
     * @param string $value The CSS value to validate/escape
     * @param string $default Default value if validation fails
     * @return string Safe CSS value
     */
    public function escapeCss(string $value, string $default = ''): string
    {
        $value = trim($value);

        // Check for dangerous content
        if ($this->containsDangerousCss($value)) {
            return $default;
        }

        // Allow valid colors
        if (preg_match(self::CSS_COLOR_PATTERN, $value)) {
            return $value;
        }

        // Allow valid lengths
        if (preg_match(self::CSS_LENGTH_PATTERN, $value)) {
            return $value;
        }

        // Allow safe CSS keywords
        $safeKeywords = ['none', 'auto', 'inherit', 'initial', 'unset', 'normal', 'bold', 'italic'];
        if (in_array(strtolower($value), $safeKeywords, true)) {
            return $value;
        }

        // Allow safe url() values
        if (preg_match('/^url\s*\(/i', $value) && $this->isValidCssUrl($value)) {
            return $value;
        }

        return $default;
    }

    /**
     * Escape and validate CSS color value.
     *
     * @param string $color The color value
     * @param string $default Default if invalid
     * @return string Safe color value
     */
    public function escapeCssColor(string $color, string $default = ''): string
    {
        $color = trim($color);

        if (preg_match(self::CSS_COLOR_PATTERN, $color)) {
            return $color;
        }

        return $default;
    }

    /**
     * Escape string for URL encoding.
     *
     * @param string $string The string to encode
     * @return string URL-encoded string
     */
    public function escapeUrl(string $string): string
    {
        return rawurlencode($string);
    }

    /**
     * Validate and sanitize a URL for use in href/src attributes.
     * Blocks dangerous schemes like javascript:, data:, etc.
     *
     * @param string $url The URL to validate
     * @param string $default Default value if URL is invalid/dangerous
     * @return string Safe URL or default
     */
    public function escapeUrlAttr(string $url, string $default = '#'): string
    {
        $url = trim($url);

        // Empty URL
        if ($url === '') {
            return $default;
        }

        // Check for dangerous schemes
        $lowercaseUrl = strtolower($url);
        foreach (self::DANGEROUS_SCHEMES as $scheme) {
            if (str_starts_with($lowercaseUrl, $scheme)) {
                return $default;
            }
        }

        // Relative URLs are safe
        if (str_starts_with($url, '/') || str_starts_with($url, './') || str_starts_with($url, '../')) {
            return $this->escapeAttr($url);
        }

        // Validate absolute URLs
        if (str_starts_with($lowercaseUrl, 'http://') || str_starts_with($lowercaseUrl, 'https://') || str_starts_with($lowercaseUrl, 'mailto:')) {
            return $this->escapeAttr($url);
        }

        // Fragment-only URLs
        if (str_starts_with($url, '#')) {
            return $this->escapeAttr($url);
        }

        // Treat other URLs as potentially dangerous
        return $default;
    }

    /**
     * Escape for HTML id attribute (alphanumeric and hyphens/underscores only).
     *
     * @param string $id The ID value
     * @return string Safe ID value
     */
    public function escapeId(string $id): string
    {
        // Remove or replace invalid characters
        $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $id);

        // Ensure it doesn't start with a number
        if (preg_match('/^[0-9]/', $safe)) {
            $safe = '_' . $safe;
        }

        return $safe;
    }

    /**
     * Check if CSS value contains dangerous content.
     *
     * @param string $value The CSS value to check
     * @return bool True if dangerous
     */
    private function containsDangerousCss(string $value): bool
    {
        $dangerous = [
            'expression(',
            'javascript:',
            'behavior:',
            'binding:',
            '-moz-binding:',
            '@import',
            '@charset',
        ];

        $lowercase = strtolower($value);
        foreach ($dangerous as $pattern) {
            if (str_contains($lowercase, $pattern)) {
                return true;
            }
        }

        // Check for url() - allow only safe schemes
        if (preg_match('/url\s*\(/i', $lowercase)) {
            return !$this->isValidCssUrl($value);
        }

        return false;
    }

    /**
     * Check if a CSS url() value is safe.
     * Allows http://, https://, and relative paths.
     *
     * @param string $value The CSS value containing url()
     * @return bool True if safe
     */
    private function isValidCssUrl(string $value): bool
    {
        // Extract the URL from url(...)
        if (!preg_match('/url\s*\(\s*["\']?([^"\')\s]+)["\']?\s*\)/i', $value, $matches)) {
            return false;
        }

        $url = trim($matches[1]);
        $lowercaseUrl = strtolower($url);

        // Block dangerous schemes
        foreach (self::DANGEROUS_SCHEMES as $scheme) {
            if (str_starts_with($lowercaseUrl, $scheme)) {
                return false;
            }
        }

        // Allow http and https URLs
        if (str_starts_with($lowercaseUrl, 'http://') || str_starts_with($lowercaseUrl, 'https://')) {
            return true;
        }

        // Allow relative paths
        if (str_starts_with($url, '/') || str_starts_with($url, './') || str_starts_with($url, '../')) {
            return true;
        }

        // Allow simple filenames (no scheme)
        if (preg_match('/^[a-zA-Z0-9_\-\.\/]+$/', $url)) {
            return true;
        }

        return false;
    }

    // =========================================================================
    // STATIC FACADE METHODS (for simple usage)
    // =========================================================================

    /**
     * Escape string for HTML text content (static).
     *
     * @param string $string The string to escape
     * @return string Escaped string
     */
    public static function html(string $string): string
    {
        return self::getInstance()->escapeHtml($string);
    }

    /**
     * Escape string for HTML attributes (static).
     *
     * @param string $string The string to escape
     * @return string Escaped string
     */
    public static function attr(string $string): string
    {
        return self::getInstance()->escapeAttr($string);
    }

    /**
     * Escape value for JavaScript (static).
     *
     * @param mixed $value The value to escape
     * @return string JSON-encoded value
     */
    public static function js(mixed $value): string
    {
        return self::getInstance()->escapeJs($value);
    }

    /**
     * Escape string for JavaScript string literal (static).
     *
     * @param string $string The string to escape
     * @return string Escaped string (without quotes)
     */
    public static function jsString(string $string): string
    {
        return self::getInstance()->escapeJsString($string);
    }

    /**
     * Escape and validate CSS value (static).
     *
     * @param string $value The CSS value
     * @param string $default Default if invalid
     * @return string Safe CSS value
     */
    public static function css(string $value, string $default = ''): string
    {
        return self::getInstance()->escapeCss($value, $default);
    }

    /**
     * Escape and validate CSS color (static).
     *
     * @param string $color The color value
     * @param string $default Default if invalid
     * @return string Safe color value
     */
    public static function cssColor(string $color, string $default = ''): string
    {
        return self::getInstance()->escapeCssColor($color, $default);
    }

    /**
     * URL-encode a string (static).
     *
     * @param string $string The string to encode
     * @return string URL-encoded string
     */
    public static function url(string $string): string
    {
        return self::getInstance()->escapeUrl($string);
    }

    /**
     * Validate and escape URL for href/src attributes (static).
     *
     * @param string $url The URL to validate
     * @param string $default Default if invalid
     * @return string Safe URL
     */
    public static function urlAttr(string $url, string $default = '#'): string
    {
        return self::getInstance()->escapeUrlAttr($url, $default);
    }

    /**
     * Escape for HTML id attribute (static).
     *
     * @param string $id The ID value
     * @return string Safe ID
     */
    public static function id(string $id): string
    {
        return self::getInstance()->escapeId($id);
    }

    // =========================================================================
    // INSTANCE MANAGEMENT
    // =========================================================================

    /**
     * Get the singleton instance.
     *
     * @return self
     */
    private static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Set a custom instance (for testing).
     *
     * @param self|null $instance The instance to use, or null to reset
     */
    public static function setInstance(?self $instance): void
    {
        self::$instance = $instance;
    }

    /**
     * Reset to default instance.
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }
}
