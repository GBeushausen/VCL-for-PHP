<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

use VCL\UI\GraphicControl;
use VCL\UI\Enums\RenderMode;
use VCL\Security\Escaper;

/**
 * Html is a component for rendering raw HTML content or Twig templates.
 *
 * This component eliminates the need for ob_start()/ob_get_clean() patterns
 * by providing direct HTML content setting or Twig template rendering.
 *
 * Usage with direct HTML:
 * ```php
 * $content = new Html();
 * $content->Html = '<h1>Hello World</h1>';
 * ```
 *
 * Usage with Twig template:
 * ```php
 * $content = new Html();
 * $content->TemplatePath = __DIR__ . '/templates';
 * $content->Template = 'card.twig';
 * $content->Variables = ['name' => 'John', 'email' => 'john@example.com'];
 * ```
 *
 * PHP 8.4 version with Property Hooks.
 */
class Html extends GraphicControl
{
    protected string $_html = '';
    protected string $_template = '';
    protected string $_templatePath = '';
    protected array $_variables = [];
    protected bool $_escapeHtml = false;
    protected string $_wrapperTag = 'div';
    protected bool $_useWrapper = true;

    // Twig environment (lazy-loaded)
    protected static ?\Twig\Environment $twigEnvironment = null;

    // Property Hooks
    public string $Html {
        get => $this->_html;
        set => $this->_html = $value;
    }

    public string $Template {
        get => $this->_template;
        set => $this->_template = $value;
    }

    public string $TemplatePath {
        get => $this->_templatePath;
        set => $this->_templatePath = $value;
    }

    public array $Variables {
        get => $this->_variables;
        set => $this->_variables = $value;
    }

    public bool $EscapeHtml {
        get => $this->_escapeHtml;
        set => $this->_escapeHtml = $value;
    }

    public string $WrapperTag {
        get => $this->_wrapperTag;
        set => $this->_wrapperTag = $value;
    }

    public bool $UseWrapper {
        get => $this->_useWrapper;
        set => $this->_useWrapper = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_width = 0;
        $this->_height = 0;
        $this->_controlstyle['csRenderOwner'] = true;
    }

    protected function getComponentType(): string
    {
        return 'html';
    }

    /**
     * Set a template variable.
     */
    public function setVariable(string $name, mixed $value): self
    {
        $this->_variables[$name] = $value;
        return $this;
    }

    /**
     * Add multiple template variables (fluent).
     */
    public function addVariables(array $variables): self
    {
        $this->_variables = array_merge($this->_variables, $variables);
        return $this;
    }

    /**
     * Get the rendered content (HTML or Twig template).
     */
    public function getContent(): string
    {
        // Template rendering takes precedence
        if ($this->_template !== '') {
            return $this->renderTemplate();
        }

        // Direct HTML content
        if ($this->_escapeHtml) {
            return Escaper::html($this->_html);
        }

        return $this->_html;
    }

    /**
     * Render a Twig template.
     */
    protected function renderTemplate(): string
    {
        if (!class_exists(\Twig\Environment::class)) {
            throw new \RuntimeException(
                'Twig is required for template rendering. Install with: composer require twig/twig'
            );
        }

        $twig = $this->getTwigEnvironment();

        try {
            return $twig->render($this->_template, $this->_variables);
        } catch (\Twig\Error\Error $e) {
            if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
                return sprintf(
                    '<div class="text-red-500">Twig Error: %s</div>',
                    Escaper::html($e->getMessage())
                );
            }
            throw $e;
        }
    }

    /**
     * Get or create the Twig environment.
     */
    protected function getTwigEnvironment(): \Twig\Environment
    {
        // Use cached environment if template path matches
        if (self::$twigEnvironment !== null) {
            return self::$twigEnvironment;
        }

        $templatePath = $this->_templatePath;
        if ($templatePath === '') {
            $templatePath = getcwd();
        }

        $loader = new \Twig\Loader\FilesystemLoader($templatePath);

        self::$twigEnvironment = new \Twig\Environment($loader, [
            'cache' => false, // Disable cache for development; enable in production
            'auto_reload' => true,
            'strict_variables' => false,
            'autoescape' => 'html',
        ]);

        // Add VCL-specific functions and filters
        $this->registerTwigExtensions(self::$twigEnvironment);

        return self::$twigEnvironment;
    }

    /**
     * Register VCL-specific Twig extensions.
     */
    protected function registerTwigExtensions(\Twig\Environment $twig): void
    {
        // Add escape functions
        $twig->addFunction(new \Twig\TwigFunction('vcl_escape_html', [Escaper::class, 'html']));
        $twig->addFunction(new \Twig\TwigFunction('vcl_escape_attr', [Escaper::class, 'attr']));
        $twig->addFunction(new \Twig\TwigFunction('vcl_escape_js', [Escaper::class, 'js']));
        $twig->addFunction(new \Twig\TwigFunction('vcl_escape_url', [Escaper::class, 'urlAttr']));

        // Add filters
        $twig->addFilter(new \Twig\TwigFilter('vcl_html', [Escaper::class, 'html']));
        $twig->addFilter(new \Twig\TwigFilter('vcl_attr', [Escaper::class, 'attr']));
    }

    /**
     * Configure the Twig environment with custom options.
     */
    public static function configureTwig(array $options): void
    {
        // Reset the cached environment so it will be recreated with new options
        self::$twigEnvironment = null;
    }

    /**
     * Set a custom Twig environment.
     */
    public static function setTwigEnvironment(\Twig\Environment $twig): void
    {
        self::$twigEnvironment = $twig;
    }

    /**
     * Render the HTML component.
     */
    protected function dumpContents(): void
    {
        // Check for Tailwind mode
        if ($this->_renderMode === RenderMode::Tailwind) {
            $this->dumpContentsTailwind();
            return;
        }

        $content = $this->getContent();

        if (!$this->_useWrapper) {
            echo $content;
            return;
        }

        $styles = [];

        // Size
        if ($this->Width > 0) {
            $styles[] = "width: {$this->Width}px";
        }
        if ($this->Height > 0) {
            $styles[] = "height: {$this->Height}px";
        }

        // Visibility
        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $styles[] = "visibility: hidden";
        }

        // Build class attribute
        $class = $this->readStyleClass();
        $classAttr = $class !== '' ? " class=\"{$class}\"" : '';

        // Build style attribute
        $style = implode('; ', $styles);
        $styleAttr = $style !== '' ? " style=\"{$style}\"" : '';

        $tag = htmlspecialchars($this->_wrapperTag);
        $id = htmlspecialchars($this->Name);

        echo "<{$tag} id=\"{$id}\"{$classAttr}{$styleAttr}>{$content}</{$tag}>";
    }

    /**
     * Render using Tailwind CSS classes.
     */
    protected function dumpContentsTailwind(): void
    {
        $content = $this->getContent();

        if (!$this->_useWrapper) {
            echo $content;
            return;
        }

        // Build class list
        $classes = [];

        // Theme class
        $themeClass = $this->getThemeClass();
        if ($themeClass !== '') {
            $classes[] = $themeClass;
        }

        // Custom CSS classes
        if (!empty($this->_cssClasses)) {
            $classes = array_merge($classes, $this->_cssClasses);
        }

        // Style class from Style property
        $styleClass = $this->readStyleClass();
        if ($styleClass !== '') {
            $classes[] = $styleClass;
        }

        // Hidden
        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $classes[] = 'hidden';
        }

        $classAttr = !empty($classes) ? sprintf(' class="%s"', htmlspecialchars(implode(' ', $classes))) : '';

        // Minimal inline style (only if absolutely necessary)
        $style = $this->getMinimalInlineStyle();
        $styleAttr = $style !== '' ? sprintf(' style="%s"', $style) : '';

        $tag = htmlspecialchars($this->_wrapperTag);
        $id = htmlspecialchars($this->Name);

        echo "<{$tag} id=\"{$id}\"{$classAttr}{$styleAttr}>{$content}</{$tag}>";
    }

    /**
     * Override render.
     */
    public function render(): string
    {
        ob_start();
        $this->dumpContents();
        return ob_get_clean();
    }

    /**
     * Render without wrapper - returns just the content.
     */
    public function renderContent(): string
    {
        return $this->getContent();
    }

    /**
     * Render only the opening tag (for manual content rendering).
     */
    public function renderOpen(): string
    {
        if (!$this->_useWrapper) {
            return '';
        }

        $tag = htmlspecialchars($this->_wrapperTag);
        $id = htmlspecialchars($this->Name);

        if ($this->_renderMode === RenderMode::Tailwind) {
            // Build class list for Tailwind mode
            $classes = [];

            $themeClass = $this->getThemeClass();
            if ($themeClass !== '') {
                $classes[] = $themeClass;
            }

            if (!empty($this->_cssClasses)) {
                $classes = array_merge($classes, $this->_cssClasses);
            }

            $styleClass = $this->readStyleClass();
            if ($styleClass !== '') {
                $classes[] = $styleClass;
            }

            if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
                $classes[] = 'hidden';
            }

            $classAttr = !empty($classes) ? sprintf(' class="%s"', htmlspecialchars(implode(' ', $classes))) : '';
            $style = $this->getMinimalInlineStyle();
            $styleAttr = $style !== '' ? sprintf(' style="%s"', $style) : '';

            return "<{$tag} id=\"{$id}\"{$classAttr}{$styleAttr}>";
        }

        // Classic mode
        $styles = [];
        if ($this->Width > 0) {
            $styles[] = "width: {$this->Width}px";
        }
        if ($this->Height > 0) {
            $styles[] = "height: {$this->Height}px";
        }
        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $styles[] = "visibility: hidden";
        }

        $class = $this->readStyleClass();
        $classAttr = $class !== '' ? " class=\"{$class}\"" : '';
        $style = implode('; ', $styles);
        $styleAttr = $style !== '' ? " style=\"{$style}\"" : '';

        return "<{$tag} id=\"{$id}\"{$classAttr}{$styleAttr}>";
    }

    /**
     * Render only the closing tag.
     */
    public function renderClose(): string
    {
        if (!$this->_useWrapper) {
            return '';
        }

        $tag = htmlspecialchars($this->_wrapperTag);
        return "</{$tag}>";
    }

    // Legacy getters/setters
    public function getHtml(): string { return $this->_html; }
    public function setHtml(string $value): void { $this->Html = $value; }
    public function defaultHtml(): string { return ''; }

    public function getTemplate(): string { return $this->_template; }
    public function setTemplate(string $value): void { $this->Template = $value; }
    public function defaultTemplate(): string { return ''; }

    public function getTemplatePath(): string { return $this->_templatePath; }
    public function setTemplatePath(string $value): void { $this->TemplatePath = $value; }
    public function defaultTemplatePath(): string { return ''; }

    public function getVariables(): array { return $this->_variables; }
    public function setVariables(array $value): void { $this->Variables = $value; }
    public function defaultVariables(): array { return []; }

    public function getEscapeHtml(): bool { return $this->_escapeHtml; }
    public function setEscapeHtml(bool $value): void { $this->EscapeHtml = $value; }
    public function defaultEscapeHtml(): bool { return false; }

    public function getWrapperTag(): string { return $this->_wrapperTag; }
    public function setWrapperTag(string $value): void { $this->WrapperTag = $value; }
    public function defaultWrapperTag(): string { return 'div'; }

    public function getUseWrapper(): bool { return $this->_useWrapper; }
    public function setUseWrapper(bool $value): void { $this->UseWrapper = $value; }
    public function defaultUseWrapper(): bool { return true; }
}
