<?php
/**
 * VCL for PHP
 *
 * Copyright (c) 2004-2008 qadram software S.L.
 * Copyright (c) 2026 Gunnar Beushausen
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 */

declare(strict_types=1);

namespace VCL\Templates;

/**
 * Template manager for registering and creating template engines.
 *
 * Use this singleton to register custom template implementations and
 * create instances of them.
 *
 * Example usage:
 * ```php
 * // Register a template class
 * $manager = TemplateManager::getInstance();
 * $manager->register('twig', TwigTemplate::class);
 *
 * // Create an instance
 * $template = $manager->create('twig', $page);
 * $template->FileName = 'index.twig';
 * $template->render();
 * ```
 */
class TemplateManager
{
    private static ?TemplateManager $instance = null;

    /** @var array<string, class-string<PageTemplate>> */
    private array $templates = [];

    // =========================================================================
    // SINGLETON
    // =========================================================================

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    // =========================================================================
    // REGISTRATION METHODS
    // =========================================================================

    /**
     * Register a template class.
     *
     * @param string $name Template name/identifier
     * @param class-string<PageTemplate> $className Fully qualified class name
     */
    public function register(string $name, string $className): void
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Class {$className} does not exist");
        }

        if (!is_subclass_of($className, PageTemplate::class)) {
            throw new \InvalidArgumentException("Class {$className} must extend PageTemplate");
        }

        $this->templates[$name] = $className;
    }

    /**
     * Unregister a template class.
     *
     * @param string $name Template name
     */
    public function unregister(string $name): void
    {
        unset($this->templates[$name]);
    }

    /**
     * Check if a template is registered.
     *
     * @param string $name Template name
     * @return bool True if registered
     */
    public function has(string $name): bool
    {
        return isset($this->templates[$name]);
    }

    /**
     * Get all registered template names.
     *
     * @return string[] Template names
     */
    public function getRegistered(): array
    {
        return array_keys($this->templates);
    }

    // =========================================================================
    // FACTORY METHODS
    // =========================================================================

    /**
     * Create a template instance.
     *
     * @param string $name Template name
     * @param object|null $owner Owner component
     * @return PageTemplate Template instance
     */
    public function create(string $name, ?object $owner = null): PageTemplate
    {
        if (!isset($this->templates[$name])) {
            throw new \InvalidArgumentException("Template '{$name}' is not registered");
        }

        $className = $this->templates[$name];
        return new $className($owner);
    }

    /**
     * Get the class name for a registered template.
     *
     * @param string $name Template name
     * @return class-string<PageTemplate>|null Class name or null
     */
    public function getClassName(string $name): ?string
    {
        return $this->templates[$name] ?? null;
    }
}
