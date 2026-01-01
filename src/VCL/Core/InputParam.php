<?php

declare(strict_types=1);

namespace VCL\Core;

/**
 * Represents an input parameter from user request
 *
 * Provides type-safe access to user input with built-in sanitization.
 * Use the as*() methods to retrieve values in the appropriate type.
 */
class InputParam
{
    private array $data;

    public function __construct(
        public string $name,
        public InputSource $source = InputSource::GET
    ) {
        $this->data = $this->source->getArray();
    }

    /**
     * Check if the parameter exists in the source
     */
    public function exists(): bool
    {
        return array_key_exists($this->name, $this->data);
    }

    /**
     * Get the raw value without any filtering
     *
     * WARNING: Only use when you're certain the input is safe
     */
    public function asRaw(): mixed
    {
        return $this->data[$this->name] ?? null;
    }

    /**
     * Get as sanitized string (HTML special chars escaped)
     */
    public function asString(): string
    {
        $value = $this->data[$this->name] ?? '';
        if (!is_string($value)) {
            return '';
        }
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Get as string array with sanitized values
     *
     * @return array<string, string>
     */
    public function asStringArray(): array
    {
        $value = $this->data[$this->name] ?? [];
        if (!is_array($value)) {
            return [];
        }

        $result = [];
        foreach ($value as $k => $v) {
            if (is_string($k) && is_string($v)) {
                $result[htmlspecialchars($k, ENT_QUOTES | ENT_HTML5, 'UTF-8')] =
                    htmlspecialchars($v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
        return $result;
    }

    /**
     * Get as integer
     */
    public function asInteger(): int
    {
        $value = $this->data[$this->name] ?? 0;
        return filter_var($value, FILTER_VALIDATE_INT) ?: 0;
    }

    /**
     * Get as boolean
     */
    public function asBoolean(): bool
    {
        $value = $this->data[$this->name] ?? false;
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get as float
     */
    public function asFloat(int $flags = 0): float
    {
        $value = $this->data[$this->name] ?? 0.0;
        $filtered = filter_var($value, FILTER_VALIDATE_FLOAT, $flags);
        return $filtered !== false ? $filtered : 0.0;
    }

    /**
     * Get as validated URL
     */
    public function asURL(): ?string
    {
        $value = $this->data[$this->name] ?? '';
        $filtered = filter_var($value, FILTER_VALIDATE_URL);
        return $filtered !== false ? $filtered : null;
    }

    /**
     * Get as sanitized URL
     */
    public function asSanitizedURL(): string
    {
        $value = $this->data[$this->name] ?? '';
        return filter_var($value, FILTER_SANITIZE_URL) ?: '';
    }

    /**
     * Get as validated email
     */
    public function asEmail(): ?string
    {
        $value = $this->data[$this->name] ?? '';
        $filtered = filter_var($value, FILTER_VALIDATE_EMAIL);
        return $filtered !== false ? $filtered : null;
    }

    /**
     * Get as sanitized email
     */
    public function asSanitizedEmail(): string
    {
        $value = $this->data[$this->name] ?? '';
        return filter_var($value, FILTER_SANITIZE_EMAIL) ?: '';
    }

    /**
     * Get as validated IP address
     */
    public function asIP(int $flags = 0): ?string
    {
        $value = $this->data[$this->name] ?? '';
        $filtered = filter_var($value, FILTER_VALIDATE_IP, $flags);
        return $filtered !== false ? $filtered : null;
    }

    /**
     * Get as URL-encoded string
     */
    public function asEncoded(): string
    {
        $value = $this->data[$this->name] ?? '';
        if (!is_string($value)) {
            return '';
        }
        return filter_var($value, FILTER_SANITIZE_ENCODED) ?: '';
    }

    /**
     * Get with special chars escaped for HTML
     */
    public function asSpecialChars(): string
    {
        $value = $this->data[$this->name] ?? '';
        if (!is_string($value)) {
            return '';
        }
        return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
    }

    /**
     * Validate against a regular expression
     */
    public function matchesPattern(string $pattern): bool
    {
        $value = $this->data[$this->name] ?? '';
        if (!is_string($value)) {
            return false;
        }
        return (bool) preg_match($pattern, $value);
    }

    /**
     * Get value if it matches a pattern, null otherwise
     */
    public function asPattern(string $pattern): ?string
    {
        $value = $this->data[$this->name] ?? '';
        if (!is_string($value)) {
            return null;
        }
        return preg_match($pattern, $value) ? $value : null;
    }
}
