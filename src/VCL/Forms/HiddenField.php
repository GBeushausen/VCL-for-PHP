<?php

declare(strict_types=1);

namespace VCL\Forms;

use VCL\UI\Control;

/**
 * HiddenField represents an HTML hidden input field.
 *
 * Used to store values that should be submitted with the form but not
 * displayed to the user.
 *
 * PHP 8.4 version with Property Hooks.
 */
class HiddenField extends Control
{
    protected string $_value = '';
    protected ?string $_onsubmit = null;

    // Property Hooks
    public string $Value {
        get => $this->_value;
        set => $this->_value = $value;
    }

    public ?string $OnSubmit {
        get => $this->_onsubmit;
        set => $this->_onsubmit = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_width = 200;
        $this->_height = 18;
    }

    /**
     * Pre-initialization: read submitted value from request.
     */
    public function preinit(): void
    {
        parent::preinit();

        $name = $this->Name;
        if ($name !== '' && isset($this->input->$name)) {
            $submitted = $this->input->$name;
            if (is_object($submitted) && method_exists($submitted, 'asString')) {
                $this->_value = $submitted->asString();
            } elseif (is_scalar($submitted)) {
                $this->_value = (string)$submitted;
            }
        }
    }

    /**
     * Initialization: fire OnSubmit event if applicable.
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
     * Render the hidden field.
     */
    public function dumpContents(): void
    {
        if (($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            if ($this->_onshow !== null) {
                $this->callEvent('onshow', []);
            } else {
                echo sprintf(
                    '<input type="hidden" id="%s" name="%s" value="%s" />',
                    htmlspecialchars($this->Name),
                    htmlspecialchars($this->Name),
                    htmlspecialchars($this->_value)
                );
            }
        } else {
            // Design-time rendering
            echo sprintf(
                '<table width="%d" cellpadding="0" cellspacing="0" height="%d">' .
                '<tr><td style="background-color: #FFFF99; border: 1px solid #666666; font-size:10px; font-family:verdana,tahoma,arial" align="center">' .
                '%s=%s</td></tr></table>',
                $this->Width ?? 200,
                $this->Height ?? 18,
                htmlspecialchars($this->Name),
                htmlspecialchars($this->_value)
            );
        }
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
    public function getValue(): string { return $this->_value; }
    public function setValue(string $value): void { $this->Value = $value; }
    public function defaultValue(): string { return ''; }

    public function getOnSubmit(): ?string { return $this->_onsubmit; }
    public function setOnSubmit(?string $value): void { $this->OnSubmit = $value; }
    public function defaultOnSubmit(): ?string { return null; }
}
