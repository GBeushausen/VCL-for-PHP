<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

use VCL\UI\FocusControl;
use VCL\UI\Enums\RenderMode;

/**
 * ButtonControl is the base class for button-type controls.
 *
 * Provides common functionality for buttons, checkboxes, and radio buttons.
 *
 * PHP 8.4 version with Property Hooks.
 */
class ButtonControl extends FocusControl
{
    protected bool $_checked = false;
    protected mixed $_datasource = null;
    protected string $_datafield = '';
    protected string $_datafieldproperty = 'Caption';
    protected int $_taborder = 0;
    protected bool $_tabstop = true;

    // Events
    protected ?string $_onclick = null;
    protected ?string $_onsubmit = null;
    protected ?string $_jsonselect = null;

    // Property Hooks
    public bool $Checked {
        get => $this->_checked;
        set => $this->_checked = $value;
    }

    public mixed $DataSource {
        get => $this->_datasource;
        set => $this->_datasource = $value;
    }

    public string $DataField {
        get => $this->_datafield;
        set => $this->_datafield = $value;
    }

    public int $TabOrder {
        get => $this->_taborder;
        set => $this->_taborder = $value;
    }

    public bool $TabStop {
        get => $this->_tabstop;
        set => $this->_tabstop = $value;
    }

    public ?string $OnClick {
        get => $this->_onclick;
        set => $this->_onclick = $value;
    }

    public ?string $OnSubmit {
        get => $this->_onsubmit;
        set => $this->_onsubmit = $value;
    }

    public ?string $jsOnSelect {
        get => $this->_jsonselect;
        set => $this->_jsonselect = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_width = 75;
        $this->_height = 25;
        $this->_controlstyle['csRenderOwner'] = true;
        $this->_controlstyle['csRenderAlso'] = 'StyleSheet';
    }

    protected function getComponentType(): string
    {
        return 'button';
    }

    /**
     * Initialization: fire OnSubmit and OnClick events.
     */
    public function init(): void
    {
        parent::init();

        $name = $this->Name;
        if ($name === '') {
            return;
        }

        // Skip event processing for htmx requests - HtmxHandler handles those
        if ($this->isHtmxEnabled() && \VCL\Ajax\HtmxHandler::isHtmxRequest()) {
            return;
        }

        // Check for OnSubmit event
        if ($this->_onsubmit !== null && isset($this->input->$name)) {
            $this->callEvent('onsubmit', []);
        }

        // Check for OnClick event - button must have been clicked (name in POST)
        if ($this->_onclick !== null && $this->_enabled && isset($this->input->$name)) {
            $this->callEvent('onclick', []);
        }
    }

    /**
     * Get common button styles.
     */
    protected function getButtonStyles(): string
    {
        $style = '';

        if ($this->_style === '') {
            // Font
            if ($this->_font !== null) {
                $style .= $this->Font->readFontString();
            }

            // Background color
            if ($this->Color !== '') {
                $style .= "background-color: {$this->Color};";
            }

            // Cursor
            $cursorValue = $this->_cursor;
            if ($cursorValue instanceof \VCL\UI\Enums\Cursor) {
                $cursorValue = $cursorValue->value;
            }
            if ($cursorValue !== '' && $cursorValue !== 'crDefault') {
                $cursor = strtolower(substr($cursorValue, 2));
                $style .= "cursor: {$cursor};";
            }
        }

        return $style;
    }

    /**
     * Get common button attributes.
     */
    protected function getButtonAttributes(): string
    {
        $attrs = [];

        // Checked
        if ($this->_checked) {
            $attrs[] = 'checked="checked"';
        }

        // Disabled
        if (!$this->_enabled) {
            $attrs[] = 'disabled="disabled"';
        }

        // Tab order
        if ($this->_tabstop) {
            $attrs[] = sprintf('tabindex="%d"', $this->_taborder);
        }

        // Hint
        if ($this->_hint !== '' && $this->_showHint) {
            $attrs[] = sprintf('title="%s"', htmlspecialchars($this->_hint));
        }

        // Events - use htmx if enabled, otherwise traditional JS
        if ($this->_enabled) {
            if ($this->isHtmxEnabled() && $this->_onclick !== null) {
                // Use htmx for the click event (inherited from FocusControl)
                $attrs[] = $this->getHtmxClickAttributes();
            } else {
                // Traditional JavaScript events
                $events = $this->getJSEventAttributes();
                if ($events !== '') {
                    $attrs[] = $events;
                }
            }

            if ($this->_jsonselect !== null) {
                $attrs[] = sprintf('onselect="return %s(event)"', htmlspecialchars($this->_jsonselect));
            }
        }

        return implode(' ', $attrs);
    }

    /**
     * Dump button control content.
     */
    protected function dumpContentsButtonControl(
        string $inputType,
        string $name,
        string $additionalAttributes = '',
        string $surroundingTags = '%s',
        bool $composite = false
    ): void {
        // Check for Tailwind mode
        if ($this->_renderMode === RenderMode::Tailwind) {
            $this->dumpContentsTailwind($inputType, $name, $additionalAttributes, $surroundingTags);
            return;
        }

        $style = $this->getButtonStyles();

        // Size
        if (!$composite) {
            if (!$this->_adjusttolayout) {
                $style .= sprintf('height:%dpx;width:%dpx;', $this->Height, $this->Width);
            } else {
                $style .= 'height:100%;width:100%;';
            }
        }

        // Hidden
        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $style .= 'visibility:hidden;';
        }

        $attrs = $this->getButtonAttributes();
        $class = $this->readStyleClass();

        $styleAttr = $style !== '' ? sprintf(' style="%s"', $style) : '';
        $classAttr = $class !== '' ? sprintf(' class="%s"', htmlspecialchars($class)) : '';

        $input = sprintf(
            '<input type="%s" id="%s" name="%s" value="%s"%s%s %s %s />',
            htmlspecialchars($inputType),
            htmlspecialchars($name),
            htmlspecialchars($name),
            htmlspecialchars($this->Caption),
            $styleAttr,
            $classAttr,
            $attrs,
            $additionalAttributes
        );

        echo sprintf($surroundingTags, $input);
    }

    /**
     * Dump button using Tailwind CSS classes.
     */
    protected function dumpContentsTailwind(
        string $inputType,
        string $name,
        string $additionalAttributes = '',
        string $surroundingTags = '%s'
    ): void {
        // Build class list
        $classes = [];

        // Theme class (vcl-button, vcl-button-primary, etc.)
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

        $attrs = $this->getButtonAttributes();
        $classAttr = !empty($classes) ? sprintf(' class="%s"', htmlspecialchars(implode(' ', $classes))) : '';

        // Minimal inline style (only if absolutely necessary)
        $style = $this->getMinimalInlineStyle();
        $styleAttr = $style !== '' ? sprintf(' style="%s"', $style) : '';

        $input = sprintf(
            '<input type="%s" id="%s" name="%s" value="%s"%s%s %s %s />',
            htmlspecialchars($inputType),
            htmlspecialchars($name),
            htmlspecialchars($name),
            htmlspecialchars($this->Caption),
            $classAttr,
            $styleAttr,
            $attrs,
            $additionalAttributes
        );

        echo sprintf($surroundingTags, $input);
    }

    // Legacy getters/setters
    public function getChecked(): bool { return $this->_checked; }
    public function setChecked(bool $value): void { $this->Checked = $value; }
    public function defaultChecked(): int { return 0; }

    public function readDataSource(): mixed { return $this->_datasource; }
    public function writeDataSource(mixed $value): void { $this->DataSource = $value; }

    public function readDataField(): string { return $this->_datafield; }
    public function writeDataField(string $value): void { $this->DataField = $value; }
    public function defaultDataField(): string { return ''; }

    public function getTabOrder(): int { return $this->_taborder; }
    public function setTabOrder(int $value): void { $this->TabOrder = $value; }
    public function defaultTabOrder(): int { return 0; }

    public function getTabStop(): bool { return $this->_tabstop; }
    public function setTabStop(bool $value): void { $this->TabStop = $value; }
    public function defaultTabStop(): int { return 1; }

    public function getOnClick(): ?string { return $this->_onclick; }
    public function setOnClick(?string $value): void { $this->OnClick = $value; }
    public function defaultOnClick(): ?string { return null; }

    public function getOnSubmit(): ?string { return $this->_onsubmit; }
    public function setOnSubmit(?string $value): void { $this->OnSubmit = $value; }
    public function defaultOnSubmit(): ?string { return null; }
}
