<?php

declare(strict_types=1);

namespace VCL\UI\StdCtrls;

use VCL\UI\Control;
use VCL\UI\Enums\ButtonType;

/**
 * Button control - a clickable button
 *
 * Demonstrates a complete control implementation with PHP 8.4 Property Hooks.
 */
class Button extends Control
{
    // Button-specific backing fields
    private ButtonType $_buttonType = ButtonType::Normal;
    private bool $_default = false;
    private bool $_cancel = false;

    // Event handlers
    private ?\Closure $_onClick = null;

    /**
     * Button type (submit, reset, or normal)
     */
    public ButtonType $ButtonType {
        get => $this->_buttonType;
        set => $this->_buttonType = $value;
    }

    /**
     * Whether this is the default button (responds to Enter)
     */
    public bool $Default {
        get => $this->_default;
        set => $this->_default = $value;
    }

    /**
     * Whether this is the cancel button (responds to Escape)
     */
    public bool $Cancel {
        get => $this->_cancel;
        set => $this->_cancel = $value;
    }

    /**
     * Click event handler
     */
    public ?\Closure $OnClick {
        get => $this->_onClick;
        set => $this->_onClick = $value;
    }

    public function __construct(?\VCL\Core\Component $owner = null)
    {
        parent::__construct($owner);

        // Default button size
        $this->Width = 75;
        $this->Height = 25;
        $this->Caption = 'Button';
    }

    /**
     * Trigger the click event
     */
    public function click(): void
    {
        if ($this->_onClick !== null && $this->Enabled) {
            ($this->_onClick)($this);
        }
    }

    /**
     * Render button HTML
     */
    public function render(): string
    {
        $attrs = [];

        if ($this->Name !== '') {
            $attrs[] = 'id="' . htmlspecialchars($this->Name) . '"';
            $attrs[] = 'name="' . htmlspecialchars($this->Name) . '"';
        }

        $attrs[] = 'type="' . $this->_buttonType->toHtml() . '"';

        $style = $this->getStyle();
        if ($style !== '') {
            $attrs[] = 'style="' . htmlspecialchars($style) . '"';
        }

        if (!$this->Enabled) {
            $attrs[] = 'disabled';
        }

        if ($this->Hint !== '' && $this->ShowHint) {
            $attrs[] = 'title="' . htmlspecialchars($this->Hint) . '"';
        }

        return sprintf(
            '<button %s>%s</button>',
            implode(' ', $attrs),
            htmlspecialchars($this->Caption)
        );
    }
}
