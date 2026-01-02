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

use VCL\Core\Component;

/**
 * Abstract base class for template engines.
 *
 * Extend this class to integrate template engines like Twig, Blade, or others.
 * The template system allows VCL pages to be rendered using templates instead
 * of the default component-based rendering.
 *
 * Example implementation:
 * ```php
 * class TwigTemplate extends PageTemplate
 * {
 *     private $twig;
 *
 *     public function initialize(): void
 *     {
 *         $loader = new \Twig\Loader\FilesystemLoader($this->TemplateDir);
 *         $this->twig = new \Twig\Environment($loader);
 *     }
 *
 *     public function assignComponents(): void
 *     {
 *         $form = $this->Owner;
 *         foreach ($form->controls->items as $control) {
 *             $this->variables[$control->Name] = $control->render();
 *         }
 *     }
 *
 *     public function dumpTemplate(): void
 *     {
 *         echo $this->twig->render($this->FileName, $this->variables);
 *     }
 * }
 * ```
 */
abstract class PageTemplate extends Component
{
    protected string $_filename = '';
    protected string $_templatedir = '';
    protected array $variables = [];

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    /**
     * Template filename.
     */
    public string $FileName {
        get => $this->_filename;
        set => $this->_filename = $value;
    }

    /**
     * Directory containing templates.
     */
    public string $TemplateDir {
        get => $this->_templatedir;
        set => $this->_templatedir = rtrim($value, '/\\');
    }

    // =========================================================================
    // ABSTRACT METHODS
    // =========================================================================

    /**
     * Initialize the template engine.
     *
     * Called once before rendering. Create your template engine instance here.
     */
    abstract public function initialize(): void;

    /**
     * Assign VCL components to template variables.
     *
     * Iterate through the owner's controls and assign their rendered output
     * to template variables.
     */
    abstract public function assignComponents(): void;

    /**
     * Render and output the template.
     */
    abstract public function dumpTemplate(): void;

    // =========================================================================
    // TEMPLATE VARIABLE METHODS
    // =========================================================================

    /**
     * Set a template variable.
     *
     * @param string $name Variable name
     * @param mixed $value Variable value
     */
    public function assign(string $name, mixed $value): void
    {
        $this->variables[$name] = $value;
    }

    /**
     * Get a template variable.
     *
     * @param string $name Variable name
     * @return mixed Variable value or null
     */
    public function get(string $name): mixed
    {
        return $this->variables[$name] ?? null;
    }

    /**
     * Check if a variable is set.
     *
     * @param string $name Variable name
     * @return bool True if set
     */
    public function has(string $name): bool
    {
        return isset($this->variables[$name]);
    }

    /**
     * Clear all variables.
     */
    public function clearVariables(): void
    {
        $this->variables = [];
    }

    /**
     * Get all variables.
     *
     * @return array All template variables
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    // =========================================================================
    // RENDERING
    // =========================================================================

    /**
     * Render the template.
     *
     * Calls initialize(), assignComponents(), and dumpTemplate() in order.
     */
    public function render(): void
    {
        $this->initialize();
        $this->assignComponents();
        $this->dumpTemplate();
    }

    /**
     * Get the full path to a template file.
     *
     * @param string|null $filename Optional filename (defaults to FileName property)
     * @return string Full path
     */
    protected function getTemplatePath(?string $filename = null): string
    {
        $file = $filename ?? $this->_filename;

        if ($this->_templatedir !== '') {
            return $this->_templatedir . DIRECTORY_SEPARATOR . $file;
        }

        return $file;
    }

    // =========================================================================
    // DEFAULT VALUE METHODS
    // =========================================================================

    protected function defaultFileName(): string
    {
        return '';
    }

    protected function defaultTemplateDir(): string
    {
        return '';
    }
}
