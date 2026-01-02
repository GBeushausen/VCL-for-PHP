<?php

declare(strict_types=1);

namespace VCL\Core;

/**
 * Input class - provides easy access to filtered user input
 *
 * Usage:
 * ```php
 * $input = new Input();
 * $action = $input->action;
 * if ($action !== null) {
 *     $value = $action->asString();
 * }
 * ```
 *
 * Or with null-safe operator:
 * ```php
 * $value = $input->action?->asString() ?? 'default';
 * ```
 */
class Input
{
    /**
     * Magic getter to search for input parameters
     *
     * Searches in order: GET, POST, REQUEST, COOKIES, SERVER
     *
     * @return InputParam|null Returns null if parameter not found
     */
    public function __get(string $name): ?InputParam
    {
        // Search order: GET -> POST -> REQUEST -> COOKIE -> SERVER
        if (isset($_GET[$name])) {
            return new InputParam($name, InputSource::GET);
        }
        if (isset($_POST[$name])) {
            return new InputParam($name, InputSource::POST);
        }
        if (isset($_REQUEST[$name])) {
            return new InputParam($name, InputSource::REQUEST);
        }
        if (isset($_COOKIE[$name])) {
            return new InputParam($name, InputSource::COOKIES);
        }
        if (isset($_SERVER[$name])) {
            return new InputParam($name, InputSource::SERVER);
        }

        return null;
    }

    /**
     * Check if a parameter exists in any source
     */
    public function __isset(string $name): bool
    {
        return isset($_GET[$name])
            || isset($_POST[$name])
            || isset($_REQUEST[$name])
            || isset($_COOKIE[$name])
            || isset($_SERVER[$name]);
    }

    /**
     * Get parameter from a specific source
     */
    public function from(string $name, InputSource $source): ?InputParam
    {
        $array = $source->getArray();
        if (isset($array[$name])) {
            return new InputParam($name, $source);
        }
        return null;
    }

    /**
     * Get parameter from GET
     */
    public function get(string $name): ?InputParam
    {
        return $this->from($name, InputSource::GET);
    }

    /**
     * Get parameter from POST
     */
    public function post(string $name): ?InputParam
    {
        return $this->from($name, InputSource::POST);
    }

    /**
     * Get parameter from COOKIE
     */
    public function cookie(string $name): ?InputParam
    {
        return $this->from($name, InputSource::COOKIES);
    }

    /**
     * Get parameter from SERVER
     */
    public function server(string $name): ?InputParam
    {
        return $this->from($name, InputSource::SERVER);
    }

    /**
     * Get all parameters from a source as an array
     *
     * @return array<string, InputParam>
     */
    public function all(InputSource $source = InputSource::REQUEST): array
    {
        $result = [];
        foreach (array_keys($source->getArray()) as $name) {
            $result[$name] = new InputParam($name, $source);
        }
        return $result;
    }
}

// Create global input instance
global $input;
$input = new Input();
