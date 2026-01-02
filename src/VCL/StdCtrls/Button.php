<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

// Button type constants
if (!defined('btSubmit')) {
    define('btSubmit', 'btSubmit');
    define('btReset', 'btReset');
    define('btButton', 'btButton');
}

/**
 * Button is a standard push button control.
 *
 * Use Button to allow users to trigger actions. When clicked, the button
 * fires the OnClick event.
 *
 * PHP 8.4 version with Property Hooks.
 */
class Button extends ButtonControl
{
    protected bool $_default = false;
    protected bool $_cancel = false;
    protected string $_buttontype = 'btSubmit';

    // Property Hooks
    public bool $Default {
        get => $this->_default;
        set => $this->_default = $value;
    }

    public bool $Cancel {
        get => $this->_cancel;
        set => $this->_cancel = $value;
    }

    public string $ButtonType {
        get => $this->_buttontype;
        set => $this->_buttontype = $value;
    }

    /**
     * Get the HTML input type for this button.
     */
    protected function getInputType(): string
    {
        return match ($this->_buttontype) {
            'btReset' => 'reset',
            'btButton' => 'button',
            default => 'submit',
        };
    }

    /**
     * Render the button.
     */
    public function dumpContents(): void
    {
        $this->dumpContentsButtonControl($this->getInputType(), $this->Name);
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
    public function getDefault(): bool { return $this->_default; }
    public function setDefault(bool $value): void { $this->Default = $value; }
    public function defaultDefault(): int { return 0; }

    public function getCancel(): bool { return $this->_cancel; }
    public function setCancel(bool $value): void { $this->Cancel = $value; }
    public function defaultCancel(): int { return 0; }

    public function getButtonType(): string { return $this->_buttontype; }
    public function setButtonType(string $value): void { $this->ButtonType = $value; }
    public function defaultButtonType(): string { return 'btSubmit'; }
}
