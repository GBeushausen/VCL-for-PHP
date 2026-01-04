<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

use VCL\UI\FocusControl;
use VCL\UI\Enums\RenderMode;
use VCL\Security\Escaper;

/**
 * Memo is a multiline text editing control.
 *
 * PHP 8.4 version with Property Hooks.
 */
class Memo extends FocusControl
{
    protected string $_text = '';
    protected array $_lines = [];
    protected bool $_readonly = false;
    protected int $_maxlength = 0;
    protected string $_placeholder = '';
    protected bool $_wordwrap = true;
    protected string $_scrollbars = 'ssNone';
    protected string $_extraAttributes = '';
    protected int $_rows = 4;

    // Property Hooks
    public string $Text {
        get => $this->_text;
        set => $this->_text = $value;
    }

    public array $Lines {
        get => $this->_lines;
        set {
            $this->_lines = $value;
            $this->_text = implode("\n", $value);
        }
    }

    public bool $ReadOnly {
        get => $this->_readonly;
        set => $this->_readonly = $value;
    }

    public int $MaxLength {
        get => $this->_maxlength;
        set => $this->_maxlength = max(0, $value);
    }

    public string $Placeholder {
        get => $this->_placeholder;
        set => $this->_placeholder = $value;
    }

    public bool $WordWrap {
        get => $this->_wordwrap;
        set => $this->_wordwrap = $value;
    }

    public string $ScrollBars {
        get => $this->_scrollbars;
        set => $this->_scrollbars = $value;
    }

    public string $ExtraAttributes {
        get => $this->_extraAttributes;
        set => $this->_extraAttributes = $value;
    }

    public int $Rows {
        get => $this->_rows;
        set => $this->_rows = max(1, $value);
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_width = 200;
        $this->_height = 100;
    }

    protected function getComponentType(): string
    {
        return 'input';
    }

    public function preinit(): void
    {
        parent::preinit();

        $submittedValue = $this->input->{$this->_name} ?? null;
        if (is_object($submittedValue)) {
            $this->_text = $submittedValue->asString();
            $this->_lines = explode("\n", $this->_text);
        }
    }

    protected function dumpContents(): void
    {
        // Check for Tailwind mode
        if ($this->_renderMode === RenderMode::Tailwind) {
            $this->dumpContentsTailwind();
            return;
        }

        $readonly = $this->_readonly ? ' readonly' : '';
        $disabled = !$this->_enabled ? ' disabled' : '';
        $maxlength = $this->_maxlength > 0 ? " maxlength=\"{$this->_maxlength}\"" : '';
        $placeholder = $this->_placeholder !== '' ? " placeholder=\"" . Escaper::attr($this->_placeholder) . "\"" : '';
        $wrap = $this->_wordwrap ? 'soft' : 'off';

        $style = $this->buildMemoStyle();
        $htmlName = Escaper::attr($this->_name);

        echo "<textarea id=\"{$htmlName}\" name=\"{$htmlName}\" style=\"{$style}\" wrap=\"{$wrap}\"{$readonly}{$disabled}{$maxlength}{$placeholder}>";
        echo Escaper::html($this->_text);
        echo "</textarea>\n";
    }

    /**
     * Render the memo using Tailwind CSS classes.
     */
    protected function dumpContentsTailwind(): void
    {
        // Build class list
        $classes = [];

        // Theme class (vcl-input for textarea)
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

        // Build attributes
        $attrs = [];

        if ($this->_readonly) {
            $attrs[] = 'readonly';
        }
        if (!$this->_enabled) {
            $attrs[] = 'disabled';
        }
        if ($this->_maxlength > 0) {
            $attrs[] = sprintf('maxlength="%d"', $this->_maxlength);
        }
        if ($this->_placeholder !== '') {
            $attrs[] = sprintf('placeholder="%s"', Escaper::attr($this->_placeholder));
        }

        $wrap = $this->_wordwrap ? 'soft' : 'off';
        $attrs[] = sprintf('wrap="%s"', $wrap);
        $attrs[] = sprintf('rows="%d"', $this->_rows);

        $htmlName = Escaper::attr($this->_name);
        $classAttr = !empty($classes) ? sprintf(' class="%s"', implode(' ', $classes)) : '';

        // Extra attributes (for htmx, etc.)
        $extraAttr = $this->_extraAttributes !== '' ? ' ' . $this->_extraAttributes : '';

        echo sprintf(
            '<textarea id="%s" name="%s"%s %s%s>',
            $htmlName,
            $htmlName,
            $classAttr,
            implode(' ', $attrs),
            $extraAttr
        );
        echo Escaper::html($this->_text);
        echo "</textarea>\n";
    }

    protected function buildMemoStyle(): string
    {
        $styles = [];
        $styles[] = "width: 100%";
        $styles[] = "height: 100%";
        $styles[] = "resize: both";
        $styles[] = "font-family: inherit";
        $styles[] = "font-size: inherit";
        $styles[] = "padding: 4px";
        $styles[] = "border: 1px solid #ccc";
        $styles[] = "border-radius: 3px";
        $styles[] = "box-sizing: border-box";

        // ScrollBars
        $overflow = match ($this->_scrollbars) {
            'ssVertical' => 'overflow-y: auto; overflow-x: hidden',
            'ssHorizontal' => 'overflow-x: auto; overflow-y: hidden',
            'ssBoth' => 'overflow: auto',
            default => 'overflow: auto',
        };
        $styles[] = $overflow;

        return implode('; ', $styles);
    }

    /**
     * Override render to use dumpContents.
     */
    public function render(): string
    {
        ob_start();
        $this->dumpContents();
        return ob_get_clean();
    }

    // Legacy getters/setters
    public function getText(): string { return $this->_text; }
    public function setText(string $value): void { $this->Text = $value; }

    public function getLines(): array { return $this->_lines; }
    public function setLines(array $value): void { $this->Lines = $value; }

    public function getReadOnly(): bool { return $this->_readonly; }
    public function setReadOnly(bool $value): void { $this->ReadOnly = $value; }

    public function getMaxLength(): int { return $this->_maxlength; }
    public function setMaxLength(int $value): void { $this->MaxLength = $value; }
}
