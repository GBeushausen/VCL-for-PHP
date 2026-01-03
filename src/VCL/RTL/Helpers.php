<?php

declare(strict_types=1);

namespace VCL\RTL;

/**
 * Runtime Library helper functions.
 *
 * Provides utility functions commonly used throughout VCL applications.
 * All methods are static for easy access.
 */
class Helpers
{
    /**
     * Converts PHP boolean into a JavaScript compatible boolean string.
     *
     * @param bool $value PHP boolean value to convert
     * @return string 'true' or 'false'
     */
    public static function boolToStr(bool $value): string
    {
        return $value ? 'true' : 'false';
    }

    /**
     * Converts plain text to HTML.
     *
     * Converts carriage returns to <br> and non-HTML chars to entities.
     *
     * @param string $text Plain text to convert
     * @param string|null $charset Character set for conversion
     * @return string HTML output
     */
    public static function textToHtml(string $text, ?string $charset = null): string
    {
        if ($charset !== null) {
            return nl2br(htmlentities($text, ENT_QUOTES, $charset));
        }
        return nl2br(htmlentities($text));
    }

    /**
     * Converts HTML to plain text.
     *
     * Converts <br> to carriage returns and entities to characters.
     *
     * @param string $text HTML to convert
     * @return string Plain text output
     */
    public static function htmlToText(string $text): string
    {
        // Handle all variants of <br>
        $text = preg_replace('/<br\s*\/?>/i', "\r\n", $text);
        return html_entity_decode($text);
    }

    /**
     * Allowed hosts for redirects (can be configured in application).
     */
    protected static array $allowedHosts = [];

    /**
     * Set allowed hosts for redirects.
     *
     * @param array $hosts List of allowed hosts (e.g., ['example.com', 'www.example.com'])
     */
    public static function setAllowedHosts(array $hosts): void
    {
        self::$allowedHosts = $hosts;
    }

    /**
     * Redirects the browser to a project file.
     *
     * SECURITY: This method now validates the host to prevent Host Header Injection.
     * For full URLs, use redirectToUrl() with explicit URL validation.
     *
     * @param string $file File to redirect to (relative path)
     * @param bool $https Use HTTPS if true
     * @return never
     */
    public static function redirect(string $file, bool $https = false): never
    {
        // Validate file path - only allow relative paths for security
        if (preg_match('#^[a-zA-Z]+://#', $file) || str_starts_with($file, '//')) {
            throw new \InvalidArgumentException(
                'redirect() only accepts relative paths. Use redirectToUrl() for full URLs.'
            );
        }

        // Use SERVER_NAME if available (more reliable), fallback to HTTP_HOST with validation
        $host = $_SERVER['SERVER_NAME'] ?? null;

        if ($host === null) {
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            // Strip port from HTTP_HOST
            $host = preg_replace('/:\d+$/', '', $host);
        }

        // Validate host against whitelist if configured
        if (!empty(self::$allowedHosts)) {
            if (!in_array(strtolower($host), array_map('strtolower', self::$allowedHosts), true)) {
                throw new \RuntimeException(
                    "Host '{$host}' is not in the allowed hosts list. " .
                    "Configure with Helpers::setAllowedHosts()."
                );
            }
        }

        // Validate host format (basic check)
        if (!preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9-]*[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9-]*[a-zA-Z0-9])?)*$/', $host)) {
            $host = 'localhost';
        }

        $protocol = $https ? 'https' : 'http';
        $uri = rtrim(dirname($_SERVER['PHP_SELF'] ?? ''), '/\\');

        // Build safe location header
        $location = "{$protocol}://{$host}{$uri}/{$file}";

        header("Location: {$location}");
        exit();
    }

    /**
     * Redirect to an absolute URL with validation.
     *
     * @param string $url Full URL to redirect to
     * @param array $allowedSchemes Allowed URL schemes (default: http, https)
     * @return never
     */
    public static function redirectToUrl(string $url, array $allowedSchemes = ['http', 'https']): never
    {
        $parsed = parse_url($url);

        if ($parsed === false || !isset($parsed['scheme']) || !isset($parsed['host'])) {
            throw new \InvalidArgumentException('Invalid URL format');
        }

        $scheme = strtolower($parsed['scheme']);
        if (!in_array($scheme, $allowedSchemes, true)) {
            throw new \InvalidArgumentException(
                "URL scheme '{$scheme}' is not allowed. Allowed: " . implode(', ', $allowedSchemes)
            );
        }

        // Validate host against whitelist if configured
        $host = strtolower($parsed['host']);
        if (!empty(self::$allowedHosts)) {
            if (!in_array($host, array_map('strtolower', self::$allowedHosts), true)) {
                throw new \RuntimeException(
                    "Host '{$host}' is not in the allowed hosts list."
                );
            }
        }

        header("Location: {$url}");
        exit();
    }

    /**
     * Check if an object/variable is not null.
     *
     * Delphi-compatible function to check if a variable is assigned.
     *
     * @param mixed $var Variable to check
     * @return bool True if not null
     */
    public static function assigned(mixed $var): bool
    {
        return $var !== null;
    }

    /**
     * Extracts JavaScript code from an HTML document.
     *
     * @param string $html HTML document
     * @return array{0: string, 1: string} [JavaScript code, HTML without scripts]
     */
    public static function extractJScript(string $html): array
    {
        $result = '';
        $pattern = '/<script[^>]*?>.*?<\/script>/si';

        preg_match_all($pattern, $html, $out);
        $onlyHtml = preg_replace($pattern, '', $html);

        $scriptPattern = '/^<script[^>]*?>(.*?)<\/script>$/si';

        foreach ($out[0] as $script) {
            if (preg_match($scriptPattern, $script, $arr)) {
                $result .= trim($arr[1]);
            }
        }

        return [$result, $onlyHtml];
    }

    /**
     * DBCS-safe unserialize with string length correction.
     *
     * @deprecated Use JSON encoding/decoding for new code. This method is kept
     *             for backwards compatibility with legacy serialized data only.
     *
     * @param string $data Serialized string
     * @return mixed Unserialized data (only arrays and scalar types)
     */
    public static function dbcsUnserialize(string $data): mixed
    {
        @trigger_error(
            'dbcsUnserialize() is deprecated. Use JSON encoding for new code.',
            E_USER_DEPRECATED
        );

        // Fix string lengths for DBCS
        $fixed = preg_replace_callback(
            '/s:(\d+):"(.*?)";/s',
            fn($m) => 's:' . strlen($m[2]) . ':"' . $m[2] . '";',
            $data
        );

        // SECURITY: Only allow arrays and scalar types, no object instantiation
        return unserialize($fixed, ['allowed_classes' => false]);
    }

    /**
     * Safe unserialize with DBCS fallback.
     *
     * First tries normal unserialize, then DBCS-safe if that fails.
     *
     * @deprecated Use JSON encoding/decoding for new code. This method is kept
     *             for backwards compatibility with legacy serialized data only.
     *
     * @param string $data Serialized string
     * @return mixed Unserialized data (only arrays and scalar types)
     */
    public static function safeUnserialize(string $data): mixed
    {
        @trigger_error(
            'safeUnserialize() is deprecated. Use JSON encoding for new code.',
            E_USER_DEPRECATED
        );

        // SECURITY: Only allow arrays and scalar types, no object instantiation
        $result = @unserialize($data, ['allowed_classes' => false]);
        if ($result === false && $data !== 'b:0;') {
            // dbcsUnserialize already has its own deprecation warning, suppress it here
            @$result = unserialize(
                preg_replace_callback(
                    '/s:(\d+):"(.*?)";/s',
                    fn($m) => 's:' . strlen($m[2]) . ':"' . $m[2] . '";',
                    $data
                ),
                ['allowed_classes' => false]
            );
        }
        return $result;
    }

    /**
     * Generates a GUID/UUID v4.
     *
     * @return string GUID in format {xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx}
     */
    public static function generateGUID(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return sprintf(
            '{%08s-%04s-%04s-%04s-%12s}',
            bin2hex(substr($data, 0, 4)),
            bin2hex(substr($data, 4, 2)),
            bin2hex(substr($data, 6, 2)),
            bin2hex(substr($data, 8, 2)),
            bin2hex(substr($data, 10, 6))
        );
    }

    /**
     * Escape a string for use in JavaScript.
     *
     * @param string $str String to escape
     * @return string Escaped string
     */
    public static function escapeJS(string $str): string
    {
        return addslashes(str_replace(["\r\n", "\r", "\n"], '\n', $str));
    }

    /**
     * Convert a color name to hex value.
     *
     * @param string $color Color name (e.g., 'red', 'blue')
     * @return string Hex color code (e.g., '#FF0000')
     */
    public static function colorToHex(string $color): string
    {
        $colors = [
            'black' => '#000000',
            'white' => '#FFFFFF',
            'red' => '#FF0000',
            'green' => '#00FF00',
            'blue' => '#0000FF',
            'yellow' => '#FFFF00',
            'cyan' => '#00FFFF',
            'magenta' => '#FF00FF',
            'silver' => '#C0C0C0',
            'gray' => '#808080',
            'maroon' => '#800000',
            'olive' => '#808000',
            'navy' => '#000080',
            'purple' => '#800080',
            'teal' => '#008080',
            'orange' => '#FFA500',
        ];

        $lower = strtolower($color);
        return $colors[$lower] ?? $color;
    }
}

