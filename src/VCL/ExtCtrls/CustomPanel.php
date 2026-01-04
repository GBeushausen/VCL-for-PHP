<?php

declare(strict_types=1);

namespace VCL\ExtCtrls;

use VCL\UI\CustomControl;
use VCL\UI\Enums\Anchors;
use VCL\UI\Enums\RenderMode;

/**
 * CustomPanel is the base class for panel controls.
 *
 * Panels are container controls that can hold other controls.
 *
 * PHP 8.4 version with Property Hooks.
 */
class CustomPanel extends CustomControl
{
    protected string $_include = '';
    protected bool $_dynamic = false;
    protected string $_background = '';
    protected int $_borderwidth = 0;
    protected string $_bordercolor = '';
    protected string $_backgroundrepeat = '';
    protected string $_backgroundposition = '';
    protected int $_activelayer = 0;

    // Property Hooks
    public string $Include {
        get => $this->_include;
        set => $this->_include = $value;
    }

    public bool $Dynamic {
        get => $this->_dynamic;
        set => $this->_dynamic = $value;
    }

    public string $Background {
        get => $this->_background;
        set => $this->_background = $value;
    }

    public int $BorderWidth {
        get => $this->_borderwidth;
        set => $this->_borderwidth = max(0, $value);
    }

    public string $BorderColor {
        get => $this->_bordercolor;
        set => $this->_bordercolor = $value;
    }

    public string $BackgroundRepeat {
        get => $this->_backgroundrepeat;
        set => $this->_backgroundrepeat = $value;
    }

    public string $BackgroundPosition {
        get => $this->_backgroundposition;
        set => $this->_backgroundposition = $value;
    }

    public int $ActiveLayer {
        get => $this->_activelayer;
        set => $this->_activelayer = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_controlstyle['csAcceptsControls'] = true;
        $this->_controlstyle['csRenderOwner'] = true;
        $this->_controlstyle['csRenderAlso'] = 'StyleSheet';
    }

    protected function getComponentType(): string
    {
        return 'panel';
    }

    /**
     * Get the active layer for this panel.
     */
    public function getActiveLayer(): int
    {
        return $this->_activelayer;
    }

    /**
     * Render the panel.
     */
    protected function dumpContents(): void
    {
        // Check for Tailwind mode
        if ($this->_renderMode === RenderMode::Tailwind) {
            $this->dumpContentsTailwind();
            return;
        }

        $style = '';
        $alignment = '';

        // Alignment
        $alignValue = $this->_alignment;
        if ($alignValue instanceof Anchors) {
            $alignValue = $alignValue->value;
        }

        $alignment = match ($alignValue) {
            'agLeft', Anchors::Left->value => ' align="left"',
            'agCenter', Anchors::Center->value => ' align="center"',
            'agRight', Anchors::Right->value => ' align="right"',
            default => '',
        };

        // Build styles
        if ($this->_style === '') {
            // Font
            if ($this->_font !== null) {
                $style .= $this->Font->readFontString();
            }

            // Background color
            if ($this->Color !== '') {
                $style .= "background-color: {$this->Color};";
            }

            // Background image
            if ($this->_background !== '') {
                $style .= "background-image: url('{$this->_background}');";

                if ($this->_backgroundrepeat !== '') {
                    $style .= "background-repeat: {$this->_backgroundrepeat};";
                }

                if ($this->_backgroundposition !== '') {
                    $style .= "background-position: {$this->_backgroundposition};";
                }
            }

            // Border
            if ($this->_borderwidth > 0) {
                $borderColor = $this->_bordercolor !== '' ? $this->_bordercolor : '#000000';
                $style .= "border: {$this->_borderwidth}px solid {$borderColor};";
            }
        }

        // Size
        if (!$this->_adjusttolayout) {
            $style .= "width:{$this->Width}px;height:{$this->Height}px;";
        } else {
            $style .= "width:100%;height:100%;";
        }

        // Visibility
        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $style .= "visibility:hidden;";
        }

        // Class
        $class = $this->readStyleClass();
        $classAttr = $class !== '' ? " class=\"{$class}\"" : '';

        $name = htmlspecialchars($this->Name);
        $styleAttr = $style !== '' ? " style=\"{$style}\"" : '';

        echo "<div id=\"{$name}\"{$classAttr}{$styleAttr}{$alignment}>\n";

        // Include file if specified
        if ($this->_include !== '' && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            include $this->_include;
        }

        // Dump child controls using layout
        $this->Layout->dumpLayoutContents();

        echo "</div>\n";
    }

    /**
     * Render the panel using Tailwind CSS classes.
     */
    protected function dumpContentsTailwind(): void
    {
        // Build class list
        $classes = [];

        // Theme class (vcl-panel)
        $themeClass = $this->getThemeClass();
        if ($themeClass !== '') {
            $classes[] = $themeClass;
        }

        // Custom CSS classes
        if (!empty($this->_cssClasses)) {
            $classes = array_merge($classes, $this->_cssClasses);
        }

        // Padding/margin from Control
        if ($this->_padding !== '') {
            $classes[] = $this->_padding;
        }
        if ($this->_margin !== '') {
            $classes[] = $this->_margin;
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

        $name = htmlspecialchars($this->Name);
        $classAttr = !empty($classes) ? sprintf(' class="%s"', htmlspecialchars(implode(' ', $classes))) : '';

        // Minimal inline style (only if absolutely necessary)
        $style = $this->getMinimalInlineStyle();
        $styleAttr = $style !== '' ? sprintf(' style="%s"', $style) : '';

        echo "<div id=\"{$name}\"{$classAttr}{$styleAttr}>\n";

        // Include file if specified
        if ($this->_include !== '' && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            include $this->_include;
        }

        // Render child controls
        if ($this->controls !== null) {
            foreach ($this->controls->items as $child) {
                if (!$child->Visible) {
                    continue;
                }

                if (method_exists($child, 'show')) {
                    $child->show();
                } elseif (method_exists($child, 'dumpContents')) {
                    $child->dumpContents();
                } elseif (method_exists($child, 'render')) {
                    echo $child->render();
                }
            }
        }

        echo "</div>\n";
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

    // Legacy getters/setters
    public function readInclude(): string { return $this->_include; }
    public function writeInclude(string $value): void { $this->Include = $value; }
    public function defaultInclude(): string { return ''; }

    public function readDynamic(): bool { return $this->_dynamic; }
    public function writeDynamic(bool $value): void { $this->Dynamic = $value; }
    public function defaultDynamic(): int { return 0; }

    public function readBackground(): string { return $this->_background; }
    public function writeBackground(string $value): void { $this->Background = $value; }
    public function defaultBackground(): string { return ''; }

    public function readBorderWidth(): int { return $this->_borderwidth; }
    public function writeBorderWidth(int $value): void { $this->BorderWidth = $value; }
    public function defaultBorderWidth(): int { return 0; }

    public function readBorderColor(): string { return $this->_bordercolor; }
    public function writeBorderColor(string $value): void { $this->BorderColor = $value; }
    public function defaultBorderColor(): string { return ''; }

    public function readBackgroundRepeat(): string { return $this->_backgroundrepeat; }
    public function writeBackgroundRepeat(string $value): void { $this->BackgroundRepeat = $value; }
    public function defaultBackgroundRepeat(): string { return ''; }

    public function readBackgroundPosition(): string { return $this->_backgroundposition; }
    public function writeBackgroundPosition(string $value): void { $this->BackgroundPosition = $value; }
    public function defaultBackgroundPosition(): string { return ''; }

    public function setActiveLayer(int $value): void { $this->ActiveLayer = $value; }
    public function defaultActiveLayer(): int { return 0; }
}
