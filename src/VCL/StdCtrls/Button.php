<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

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

    // Property Hooks
    public bool $Default {
        get => $this->_default;
        set => $this->_default = $value;
    }

    public bool $Cancel {
        get => $this->_cancel;
        set => $this->_cancel = $value;
    }

    /**
     * Render the button.
     */
    public function dumpContents(): void
    {
        $this->dumpContentsButtonControl('submit', $this->Name);
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
}
