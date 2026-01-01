<?php

declare(strict_types=1);

/**
 * Legacy class aliases for backwards compatibility
 *
 * Maps old class names to new namespaced classes.
 * Existing code using the old names will continue to work.
 */

namespace VCL\Core;

// Core aliases - register only if not already defined
if (!class_exists('VCLObject', false)) {
    class_alias(VCLObject::class, 'VCLObject');
}
if (!class_exists('Input', false)) {
    class_alias(Input::class, 'Input');
}
if (!class_exists('EPropertyNotFound', false)) {
    class_alias(Exception\PropertyNotFoundException::class, 'EPropertyNotFound');
}

/**
 * Legacy InputParam that accepts old-style integer constants
 *
 * @deprecated Use VCL\Core\InputParam with InputSource enum instead
 */
class LegacyInputParam extends InputParam
{
    public function __construct(string $name, int|InputSource $source = InputSource::GET)
    {
        if (is_int($source)) {
            $source = match ($source) {
                0 => InputSource::GET,
                1 => InputSource::POST,
                2 => InputSource::REQUEST,
                3 => InputSource::COOKIES,
                4 => InputSource::SERVER,
                default => InputSource::GET,
            };
        }
        parent::__construct($name, $source);
    }

    // Legacy method aliases
    public function asStripped(): string
    {
        return $this->asString();
    }

    public function asUnsafeRaw(): mixed
    {
        return $this->asRaw();
    }

    public function asRegExp(): string
    {
        return (string) $this->asRaw();
    }
}

/**
 * Legacy RawInputParam for unfiltered input
 *
 * @deprecated Input filtering is always recommended
 */
class RawInputParam extends InputParam
{
    public function asString(): string
    {
        return (string) $this->asRaw();
    }

    public function asStringArray(): array
    {
        $raw = $this->asRaw();
        return is_array($raw) ? $raw : [];
    }

    public function asInteger(): int
    {
        return (int) $this->asRaw();
    }

    public function asBoolean(): bool
    {
        return (bool) $this->asRaw();
    }

    public function asFloat(int $flags = 0): float
    {
        return (float) $this->asRaw();
    }

    public function asURL(): ?string
    {
        $raw = $this->asRaw();
        return is_string($raw) ? $raw : null;
    }

    public function asEmail(): ?string
    {
        $raw = $this->asRaw();
        return is_string($raw) ? $raw : null;
    }

    public function asIP(int $flags = 0): ?string
    {
        $raw = $this->asRaw();
        return is_string($raw) ? $raw : null;
    }

    public function asEncoded(): string
    {
        return (string) $this->asRaw();
    }

    public function asSpecialChars(): string
    {
        return (string) $this->asRaw();
    }

    public function asStripped(): string
    {
        return $this->asString();
    }

    public function asUnsafeRaw(): mixed
    {
        return $this->asRaw();
    }

    public function asRegExp(): string
    {
        return (string) $this->asRaw();
    }
}

// Register InputParam alias to use legacy version with int support
if (!class_exists('InputParam', false)) {
    class_alias(LegacyInputParam::class, 'InputParam');
}
if (!class_exists('RawInputParam', false)) {
    class_alias(RawInputParam::class, 'RawInputParam');
}
