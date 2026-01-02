<?php

declare(strict_types=1);

namespace VCL\Buttons;

use VCL\UI\FocusControl;

/**
 * QWidget is the base class for QooxDoo-style widgets.
 *
 * This class provides common functionality for widgets that render
 * as interactive JavaScript components.
 *
 * PHP 8.4 version with Property Hooks.
 */
class QWidget extends FocusControl
{
    protected bool $_hidden = false;

    // Property Hooks
    public bool $Hidden {
        get => $this->_hidden;
        set => $this->_hidden = $value;
    }

    /**
     * Dump common widget properties as JavaScript.
     */
    protected function dumpCommonQWidgetProperties(string $name, bool $fontSupport = true): string
    {
        $output = '';

        $enabled = $this->_enabled ? 'true' : 'false';
        $output .= "  {$name}.disabled = !{$enabled};\n";

        if ($fontSupport && $this->_font !== null) {
            $output .= "  {$name}.style.fontFamily = '{$this->_font->Family}';\n";
            $output .= "  {$name}.style.fontSize = '{$this->_font->Size}px';\n";
            if ($this->_font->Color !== '') {
                $output .= "  {$name}.style.color = '{$this->_font->Color}';\n";
            }
        }

        $visible = $this->_hidden ? 'none' : 'block';
        $output .= "  {$name}.style.display = '{$visible}';\n";

        return $output;
    }

    /**
     * Prepare a JavaScript event handler.
     */
    protected function prepareJSEvent(string $name, ?string $event, string $eventName): string
    {
        if ($event !== null && $event !== '') {
            return "  {$name}.addEventListener('{$eventName}', function(e) { {$event}(e); });\n";
        }
        return '';
    }

    /**
     * Dump common JavaScript events.
     */
    protected function dumpCommonJSEvents(string $name): string
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return '';
        }

        $output = '';
        $output .= $this->prepareJSEvent($name, $this->_jsonclick, 'click');
        $output .= $this->prepareJSEvent($name, $this->_jsondblclick, 'dblclick');
        $output .= $this->prepareJSEvent($name, $this->_jsonfocus, 'focus');
        $output .= $this->prepareJSEvent($name, $this->_jsonblur, 'blur');
        $output .= $this->prepareJSEvent($name, $this->_jsonkeydown, 'keydown');
        $output .= $this->prepareJSEvent($name, $this->_jsonkeyup, 'keyup');
        $output .= $this->prepareJSEvent($name, $this->_jsonkeypress, 'keypress');
        $output .= $this->prepareJSEvent($name, $this->_jsonmousedown, 'mousedown');
        $output .= $this->prepareJSEvent($name, $this->_jsonmouseup, 'mouseup');
        $output .= $this->prepareJSEvent($name, $this->_jsonmousemove, 'mousemove');
        $output .= $this->prepareJSEvent($name, $this->_jsonmouseout, 'mouseout');
        $output .= $this->prepareJSEvent($name, $this->_jsonmouseover, 'mouseover');

        return $output;
    }

    /**
     * Get the hint attribute for HTML elements.
     */
    protected function getHintAttribute(): string
    {
        if ($this->_showhint && $this->_hint !== '') {
            return " title=\"" . htmlspecialchars($this->_hint) . "\"";
        }
        return '';
    }

    // Legacy getters/setters
    public function getHidden(): bool { return $this->_hidden; }
    public function setHidden(bool $value): void { $this->Hidden = $value; }
}
