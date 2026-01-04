<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

use VCL\UI\FocusControl;
use VCL\UI\Enums\RenderMode;
use VCL\StdCtrls\Enums\BorderStyle;
use VCL\StdCtrls\Enums\CharCase;

/**
 * CustomEdit is the base class for single-line text input controls.
 *
 * Provides text input with optional password masking, character limits,
 * and text transformation.
 *
 * PHP 8.4 version with Property Hooks.
 */
class CustomEdit extends FocusControl
{
    protected BorderStyle|string $_borderstyle = BorderStyle::Single;
    protected mixed $_datasource = null;
    protected string $_datafield = '';
    protected CharCase|string $_charcase = CharCase::Normal;
    protected bool $_ispassword = false;
    protected int $_maxlength = 0;
    protected int $_taborder = 0;
    protected bool $_tabstop = true;
    protected string $_text = '';
    protected bool $_readonly = false;
    protected bool $_filterinput = true;
    protected string $_placeholder = '';
    protected string $_extraAttributes = '';

    // Events
    protected ?string $_onclick = null;
    protected ?string $_ondblclick = null;
    protected ?string $_onsubmit = null;
    protected ?string $_jsonselect = null;

    // Property Hooks
    public BorderStyle|string $BorderStyle {
        get => $this->_borderstyle;
        set => $this->_borderstyle = $value instanceof BorderStyle ? $value : BorderStyle::from($value);
    }

    public mixed $DataSource {
        get => $this->_datasource;
        set => $this->_datasource = $value;
    }

    public string $DataField {
        get => $this->_datafield;
        set => $this->_datafield = $value;
    }

    public CharCase|string $CharCase {
        get => $this->_charcase;
        set => $this->_charcase = $value instanceof CharCase ? $value : CharCase::from($value);
    }

    public bool $IsPassword {
        get => $this->_ispassword;
        set => $this->_ispassword = $value;
    }

    public int $MaxLength {
        get => $this->_maxlength;
        set => $this->_maxlength = max(0, $value);
    }

    public int $TabOrder {
        get => $this->_taborder;
        set => $this->_taborder = $value;
    }

    public bool $TabStop {
        get => $this->_tabstop;
        set => $this->_tabstop = $value;
    }

    public string $Text {
        get => $this->_text;
        set => $this->_text = $value;
    }

    public bool $ReadOnly {
        get => $this->_readonly;
        set => $this->_readonly = $value;
    }

    public bool $FilterInput {
        get => $this->_filterinput;
        set => $this->_filterinput = $value;
    }

    public string $Placeholder {
        get => $this->_placeholder;
        set => $this->_placeholder = $value;
    }

    public string $ExtraAttributes {
        get => $this->_extraAttributes;
        set => $this->_extraAttributes = $value;
    }

    public ?string $OnClick {
        get => $this->_onclick;
        set => $this->_onclick = $value;
    }

    public ?string $OnDblClick {
        get => $this->_ondblclick;
        set => $this->_ondblclick = $value;
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
        $this->_width = 121;
        $this->_height = 21;
        $this->_controlstyle['csRenderOwner'] = true;
        $this->_controlstyle['csRenderAlso'] = 'StyleSheet';
    }

    protected function getComponentType(): string
    {
        return 'input';
    }

    /**
     * Pre-initialization: read submitted value.
     */
    public function preinit(): void
    {
        parent::preinit();

        $name = $this->Name;
        if ($name !== '' && isset($this->input)) {
            $submitted = $this->input->$name ?? null;
            if (!is_object($submitted)) {
                $submitted = $this->input->{$name . '_hidden'} ?? null;
            }

            if (is_object($submitted) && method_exists($submitted, 'asString')) {
                $text = $submitted->asString();
                if ($this->_filterinput) {
                    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                }
                $this->_text = $text;
            } elseif (is_string($submitted)) {
                $this->_text = $this->_filterinput
                    ? htmlspecialchars($submitted, ENT_QUOTES, 'UTF-8')
                    : $submitted;
            }
        }
    }

    /**
     * Initialization: fire OnSubmit event.
     */
    public function init(): void
    {
        parent::init();

        $name = $this->Name;
        if ($this->_onsubmit !== null && $name !== '' && isset($this->input->$name)) {
            $this->callEvent('onsubmit', []);
        }
    }

    /**
     * Get common HTML attributes.
     */
    protected function getCommonAttributes(): string
    {
        $attrs = [];

        // Disabled
        if (!$this->_enabled) {
            $attrs[] = 'disabled';
        }

        // Max length
        if ($this->_maxlength > 0) {
            $attrs[] = sprintf('maxlength="%d"', $this->_maxlength);
        }

        // Read only
        if ($this->_readonly) {
            $attrs[] = 'readonly';
        }

        // Tab order
        if ($this->_tabstop) {
            $attrs[] = sprintf('tabindex="%d"', $this->_taborder);
        }

        // Class
        $class = $this->readStyleClass();
        if ($class !== '') {
            $attrs[] = sprintf('class="%s"', htmlspecialchars($class));
        }

        // Hint
        if ($this->_hint !== '' && $this->_showHint) {
            $attrs[] = sprintf('title="%s"', htmlspecialchars($this->_hint));
        }

        // Events
        if ($this->_enabled) {
            $events = $this->getJSEventAttributes();
            if ($events !== '') {
                $attrs[] = $events;
            }

            if ($this->_jsonselect !== null) {
                $attrs[] = sprintf('onselect="return %s(event)"', htmlspecialchars($this->_jsonselect));
            }
        }

        return implode(' ', $attrs);
    }

    /**
     * Get common CSS styles.
     */
    protected function getCommonStyles(): string
    {
        $style = '';

        if ($this->_style === '') {
            // Font
            if ($this->_font !== null) {
                $style .= $this->Font->readFontString();
            }

            // Border style
            $borderStyle = $this->_borderstyle instanceof BorderStyle
                ? $this->_borderstyle
                : BorderStyle::from($this->_borderstyle);
            $style .= $borderStyle->toCss();

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

            // Char case
            $charCase = $this->_charcase instanceof CharCase
                ? $this->_charcase
                : CharCase::from($this->_charcase);
            $style .= $charCase->toCss();
        }

        return $style;
    }

    /**
     * Render the edit control.
     */
    protected function dumpContents(): void
    {
        // Check for Tailwind mode
        if ($this->_renderMode === RenderMode::Tailwind) {
            $this->dumpContentsTailwind();
            return;
        }

        $style = $this->getCommonStyles();

        // Size
        if (!$this->_adjusttolayout) {
            $style .= sprintf('width:%dpx;', $this->Width);
        } else {
            $style .= 'width:100%;';
        }

        // Hidden
        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $style .= 'visibility:hidden;';
        }

        $attrs = $this->getCommonAttributes();
        $type = $this->_ispassword ? 'password' : 'text';
        $value = htmlspecialchars($this->_text);
        $name = htmlspecialchars($this->Name);

        echo sprintf(
            '<input type="%s" id="%s" name="%s" value="%s" style="%s" %s />',
            $type,
            $name,
            $name,
            $value,
            $style,
            $attrs
        );
    }

    /**
     * Render the edit control using Tailwind CSS classes.
     */
    protected function dumpContentsTailwind(): void
    {
        // Build class list
        $classes = [];

        // Theme class (vcl-input)
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

        // Character case
        $charCase = $this->_charcase instanceof CharCase
            ? $this->_charcase
            : CharCase::from($this->_charcase);
        $caseClass = match ($charCase) {
            CharCase::LowerCase => 'lowercase',
            CharCase::UpperCase => 'uppercase',
            default => '',
        };
        if ($caseClass !== '') {
            $classes[] = $caseClass;
        }

        // Hidden
        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $classes[] = 'hidden';
        }

        // Build attributes
        $attrs = [];

        // Disabled
        if (!$this->_enabled) {
            $attrs[] = 'disabled';
        }

        // Max length
        if ($this->_maxlength > 0) {
            $attrs[] = sprintf('maxlength="%d"', $this->_maxlength);
        }

        // Read only
        if ($this->_readonly) {
            $attrs[] = 'readonly';
        }

        // Tab order
        if ($this->_tabstop) {
            $attrs[] = sprintf('tabindex="%d"', $this->_taborder);
        }

        // Hint
        if ($this->_hint !== '' && $this->_showHint) {
            $attrs[] = sprintf('title="%s"', htmlspecialchars($this->_hint));
        }

        // Events
        if ($this->_enabled) {
            $events = $this->getJSEventAttributes();
            if ($events !== '') {
                $attrs[] = $events;
            }
        }

        // Placeholder
        if ($this->_placeholder !== '') {
            $attrs[] = sprintf('placeholder="%s"', htmlspecialchars($this->_placeholder));
        }

        $type = $this->_ispassword ? 'password' : 'text';
        $value = htmlspecialchars($this->_text);
        $name = htmlspecialchars($this->Name);

        $classAttr = !empty($classes) ? sprintf(' class="%s"', htmlspecialchars(implode(' ', $classes))) : '';

        // Minimal inline style (only if absolutely necessary)
        $style = $this->getMinimalInlineStyle();
        $styleAttr = $style !== '' ? sprintf(' style="%s"', $style) : '';

        // Extra attributes (for htmx, etc.)
        $extraAttr = $this->_extraAttributes !== '' ? ' ' . $this->_extraAttributes : '';

        echo sprintf(
            '<input type="%s" id="%s" name="%s" value="%s"%s%s %s%s />',
            $type,
            $name,
            $name,
            $value,
            $classAttr,
            $styleAttr,
            implode(' ', $attrs),
            $extraAttr
        );
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
    public function getBorderStyle(): BorderStyle|string { return $this->_borderstyle; }
    public function setBorderStyle(BorderStyle|string $value): void { $this->BorderStyle = $value; }
    public function defaultBorderStyle(): string { return 'bsSingle'; }

    public function readDataSource(): mixed { return $this->_datasource; }
    public function writeDataSource(mixed $value): void { $this->DataSource = $value; }

    public function readDataField(): string { return $this->_datafield; }
    public function writeDataField(string $value): void { $this->DataField = $value; }
    public function defaultDataField(): string { return ''; }

    public function getCharCase(): CharCase|string { return $this->_charcase; }
    public function setCharCase(CharCase|string $value): void { $this->CharCase = $value; }
    public function defaultCharCase(): string { return 'ecNormal'; }

    public function getIsPassword(): bool { return $this->_ispassword; }
    public function setIsPassword(bool $value): void { $this->IsPassword = $value; }
    public function defaultIsPassword(): int { return 0; }

    public function getMaxLength(): int { return $this->_maxlength; }
    public function setMaxLength(int $value): void { $this->MaxLength = $value; }
    public function defaultMaxLength(): int { return 0; }

    public function getTabOrder(): int { return $this->_taborder; }
    public function setTabOrder(int $value): void { $this->TabOrder = $value; }
    public function defaultTabOrder(): int { return 0; }

    public function getTabStop(): bool { return $this->_tabstop; }
    public function setTabStop(bool $value): void { $this->TabStop = $value; }
    public function defaultTabStop(): int { return 1; }

    public function getText(): string { return $this->_text; }
    public function setText(string $value): void { $this->Text = $value; }
    public function defaultText(): string { return ''; }

    public function getReadOnly(): bool { return $this->_readonly; }
    public function setReadOnly(bool $value): void { $this->ReadOnly = $value; }
    public function defaultReadOnly(): int { return 0; }

    public function getFilterInput(): bool { return $this->_filterinput; }
    public function setFilterInput(bool $value): void { $this->FilterInput = $value; }
    public function defaultFilterInput(): int { return 1; }
}
