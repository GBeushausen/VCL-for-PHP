<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

/**
 * CustomCheckBox is the base class for checkbox controls.
 *
 * Checkboxes allow users to toggle between checked and unchecked states.
 *
 * PHP 8.4 version with Property Hooks.
 */
class CustomCheckBox extends ButtonControl
{
    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_width = 113;
        $this->_height = 17;
        $this->_datafieldproperty = 'Checked';
    }

    /**
     * Pre-initialization: read checked state from request.
     */
    public function preinit(): void
    {
        parent::preinit();

        $name = $this->Name;
        if ($name !== '' && isset($this->input)) {
            $submitted = $this->input->$name ?? null;
            if (is_object($submitted) && method_exists($submitted, 'asString')) {
                $this->_checked = ($submitted->asString() === 'on' || $submitted->asString() === '1');
            } elseif (is_string($submitted)) {
                $this->_checked = ($submitted === 'on' || $submitted === '1');
            } else {
                // Checkbox not in POST means unchecked
                $this->_checked = false;
            }
        }
    }

    /**
     * Render the checkbox.
     */
    public function dumpContents(): void
    {
        $style = "";
        $style .= $this->getButtonStyles();

        // Size
        if ($this->Width > 0) {
            $style .= "width: {$this->Width}px;";
        }

        // Hidden
        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $style .= 'visibility:hidden;';
        }

        $attrs = $this->getButtonAttributes();
        $class = $this->readStyleClass();
        $name = htmlspecialchars($this->Name);
        $caption = htmlspecialchars($this->Caption);

        $styleAttr = $style !== '' ? sprintf(' style="%s"', $style) : '';
        $classAttr = $class !== '' ? sprintf(' class="%s"', htmlspecialchars($class)) : '';

        echo sprintf(
            '<span id="%s_wrapper"%s%s>' .
            '<input type="checkbox" id="%s" name="%s" value="on" %s />' .
            '<label for="%s">%s</label>' .
            '</span>',
            $name,
            $styleAttr,
            $classAttr,
            $name,
            $name,
            $attrs,
            $name,
            $caption
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
}
