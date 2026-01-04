<?php
/**
 * VCL for PHP 3.0
 *
 * ThemeManager - Manages Tailwind CSS themes for VCL components
 */

declare(strict_types=1);

namespace VCL\Theming;

/**
 * ThemeManager provides centralized theme management for VCL components.
 *
 * This singleton class manages the current theme (light/dark) and provides
 * helper methods for generating component class names based on the VCL
 * theming convention.
 *
 * Usage:
 *   $theme = ThemeManager::getInstance();
 *   $theme->setTheme('dark');
 *   $btnClass = $theme->getComponentClass('button', 'primary'); // 'vcl-button-primary'
 */
class ThemeManager
{
    private static ?self $instance = null;

    private string $theme = 'light';
    private string $prefix = 'vcl';

    /**
     * Component variants registry.
     * Maps component types to their available variants.
     *
     * @var array<string, array<string>>
     */
    private array $componentVariants = [
        'button' => ['default', 'primary', 'secondary', 'outline', 'ghost', 'danger'],
        'input' => ['default', 'error'],
        'label' => ['default', 'required'],
        'panel' => ['default', 'elevated', 'sunken'],
        'checkbox' => ['default'],
        'radio' => ['default'],
        'select' => ['default'],
        'textarea' => ['default'],
        'link' => ['default'],
        'control' => ['default'],
        'flex' => ['default'],
        'grid' => ['default'],
    ];

    private function __construct()
    {
        // Private constructor for singleton
    }

    /**
     * Get the singleton instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set the current theme.
     *
     * @param string $theme Theme name ('light' or 'dark')
     */
    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * Get the current theme.
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * Check if dark mode is enabled.
     */
    public function isDarkMode(): bool
    {
        return $this->theme === 'dark';
    }

    /**
     * Get the data-theme attribute for the HTML element.
     */
    public function getThemeAttribute(): string
    {
        return 'data-theme="' . htmlspecialchars($this->theme) . '"';
    }

    /**
     * Set the class prefix for component classes.
     *
     * @param string $prefix Class prefix (default: 'vcl')
     */
    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * Get the class prefix.
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Get the CSS class for a component with a specific variant.
     *
     * @param string $component Component type (e.g., 'button', 'input', 'panel')
     * @param string $variant Variant name (e.g., 'primary', 'secondary', 'default')
     * @return string The CSS class name
     */
    public function getComponentClass(string $component, string $variant = 'default'): string
    {
        $component = strtolower($component);
        $variant = strtolower($variant);

        // Base class
        $baseClass = "{$this->prefix}-{$component}";

        // If variant is default or empty, return base class only
        if ($variant === 'default' || $variant === '') {
            return $baseClass;
        }

        // Return variant class
        return "{$baseClass}-{$variant}";
    }

    /**
     * Get all available variants for a component type.
     *
     * @param string $component Component type
     * @return array<string> List of variant names
     */
    public function getVariants(string $component): array
    {
        $component = strtolower($component);
        return $this->componentVariants[$component] ?? ['default'];
    }

    /**
     * Check if a variant exists for a component.
     *
     * @param string $component Component type
     * @param string $variant Variant name
     */
    public function hasVariant(string $component, string $variant): bool
    {
        $variants = $this->getVariants($component);
        return in_array(strtolower($variant), $variants, true);
    }

    /**
     * Register custom variants for a component type.
     *
     * @param string $component Component type
     * @param array<string> $variants List of variant names
     */
    public function registerVariants(string $component, array $variants): void
    {
        $component = strtolower($component);
        $this->componentVariants[$component] = array_map('strtolower', $variants);
    }

    /**
     * Add a variant to an existing component type.
     *
     * @param string $component Component type
     * @param string $variant Variant name to add
     */
    public function addVariant(string $component, string $variant): void
    {
        $component = strtolower($component);
        $variant = strtolower($variant);

        if (!isset($this->componentVariants[$component])) {
            $this->componentVariants[$component] = ['default'];
        }

        if (!in_array($variant, $this->componentVariants[$component], true)) {
            $this->componentVariants[$component][] = $variant;
        }
    }

    /**
     * Generate the CSS file link tag for the VCL theme.
     *
     * @param string $path Path to the compiled CSS file
     */
    public function getCssLink(string $path = '/assets/css/vcl-theme.css'): string
    {
        return '<link rel="stylesheet" href="' . htmlspecialchars($path) . '">';
    }

    /**
     * Generate a script tag for theme switching functionality.
     */
    public function getThemeSwitchScript(): string
    {
        return <<<'HTML'
<script>
(function() {
    const storageKey = 'vcl-theme';
    const defaultTheme = 'light';

    function getStoredTheme() {
        return localStorage.getItem(storageKey);
    }

    function setStoredTheme(theme) {
        localStorage.setItem(storageKey, theme);
    }

    function getPreferredTheme() {
        const storedTheme = getStoredTheme();
        if (storedTheme) return storedTheme;

        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : defaultTheme;
    }

    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        setStoredTheme(theme);
    }

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || defaultTheme;
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
        return newTheme;
    }

    // Initialize theme on page load
    setTheme(getPreferredTheme());

    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!getStoredTheme()) {
            setTheme(e.matches ? 'dark' : 'light');
        }
    });

    // Expose toggle function globally
    window.VCLTheme = {
        toggle: toggleTheme,
        set: setTheme,
        get: () => document.documentElement.getAttribute('data-theme') || defaultTheme
    };
})();
</script>
HTML;
    }

    /**
     * Reset the singleton instance (useful for testing).
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
