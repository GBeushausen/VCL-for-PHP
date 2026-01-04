<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

use VCL\UI\Enums\RenderMode;

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
    protected function dumpContents(): void
    {
        if ($this->_renderMode === RenderMode::Tailwind) {
            $this->dumpCheckboxTailwind();
            return;
        }

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
     * Render checkbox using Tailwind CSS classes.
     */
    protected function dumpCheckboxTailwind(): void
    {
        $name = htmlspecialchars($this->Name);
        $caption = htmlspecialchars($this->Caption);

        // Wrapper classes
        $wrapperClasses = ['inline-flex', 'items-center', 'gap-2'];

        // Custom CSS classes go on wrapper
        if (!empty($this->_cssClasses)) {
            $wrapperClasses = array_merge($wrapperClasses, $this->_cssClasses);
        }

        // Hidden state
        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $wrapperClasses[] = 'hidden';
        }

        // Checkbox input classes
        $inputClasses = [
            'w-4',
            'h-4',
            'rounded',
            'border-vcl-border',
            'bg-vcl-surface',
            'text-vcl-primary',
            'focus:ring-2',
            'focus:ring-vcl-primary',
            'focus:ring-offset-0',
            'cursor-pointer',
        ];

        // Disabled state
        if (!$this->_enabled) {
            $inputClasses[] = 'opacity-50';
            $inputClasses[] = 'cursor-not-allowed';
        }

        // Label classes
        $labelClasses = [
            'text-vcl-text',
            'select-none',
            'cursor-pointer',
        ];

        if (!$this->_enabled) {
            $labelClasses[] = 'opacity-50';
            $labelClasses[] = 'cursor-not-allowed';
        }

        $wrapperClassAttr = implode(' ', $wrapperClasses);
        $inputClassAttr = implode(' ', $inputClasses);
        $labelClassAttr = implode(' ', $labelClasses);

        $checked = $this->_checked ? ' checked' : '';
        $disabled = !$this->_enabled ? ' disabled' : '';

        echo sprintf(
            '<label id="%s_wrapper" class="%s">' .
            '<input type="checkbox" id="%s" name="%s" value="on" class="%s"%s%s />' .
            '<span class="%s">%s</span>' .
            '</label>',
            $name,
            $wrapperClassAttr,
            $name,
            $name,
            $inputClassAttr,
            $checked,
            $disabled,
            $labelClassAttr,
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
