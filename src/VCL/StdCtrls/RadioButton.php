<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

use VCL\UI\FocusControl;

/**
 * RadioButton is a radio button control that can be grouped.
 *
 * PHP 8.4 version with Property Hooks.
 */
class RadioButton extends FocusControl
{
    protected string $_caption = '';
    protected bool $_checked = false;
    protected string $_group = '';

    // Property Hooks
    public string $Caption {
        get => $this->_caption;
        set => $this->_caption = $value;
    }

    public bool $Checked {
        get => $this->_checked;
        set => $this->_checked = $value;
    }

    public string $Group {
        get => $this->_group;
        set => $this->_group = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_width = 100;
        $this->_height = 20;
    }

    public function preinit(): void
    {
        parent::preinit();

        $submittedValue = $this->input->{$this->_group} ?? null;
        if (is_object($submittedValue)) {
            $this->_checked = ($submittedValue->asString() === $this->_name);
        }
    }

    public function dumpContents(): void
    {
        $checked = $this->_checked ? ' checked' : '';
        $disabled = !$this->_enabled ? ' disabled' : '';
        $style = $this->buildInlineStyle();

        echo "<span id=\"{$this->_name}_wrapper\" style=\"{$style}\">";
        echo "<input type=\"radio\" id=\"{$this->_name}\" name=\"{$this->_group}\" value=\"{$this->_name}\"{$checked}{$disabled} />";
        echo "<label for=\"{$this->_name}\">" . htmlspecialchars($this->_caption) . "</label>";
        echo "</span>\n";
    }

    protected function buildInlineStyle(): string
    {
        $styles = [];
        $styles[] = "display: inline-block";
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
    public function getCaption(): string { return $this->_caption; }
    public function setCaption(string $value): void { $this->Caption = $value; }

    public function getChecked(): bool { return $this->_checked; }
    public function setChecked(bool $value): void { $this->Checked = $value; }

    public function getGroup(): string { return $this->_group; }
    public function setGroup(string $value): void { $this->Group = $value; }
}
