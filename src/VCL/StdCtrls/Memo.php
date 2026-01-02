<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

use VCL\UI\FocusControl;

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

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_width = 200;
        $this->_height = 100;
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

    public function dumpContents(): void
    {
        $readonly = $this->_readonly ? ' readonly' : '';
        $disabled = !$this->_enabled ? ' disabled' : '';
        $maxlength = $this->_maxlength > 0 ? " maxlength=\"{$this->_maxlength}\"" : '';
        $placeholder = $this->_placeholder !== '' ? " placeholder=\"" . htmlspecialchars($this->_placeholder) . "\"" : '';
        $wrap = $this->_wordwrap ? 'soft' : 'off';

        $style = $this->buildMemoStyle();

        echo "<textarea id=\"{$this->_name}\" name=\"{$this->_name}\" style=\"{$style}\" wrap=\"{$wrap}\"{$readonly}{$disabled}{$maxlength}{$placeholder}>";
        echo htmlspecialchars($this->_text);
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
