<?php

declare(strict_types=1);

namespace VCL\ExtCtrls;

use VCL\UI\Control;
use VCL\Graphics\Canvas;

/**
 * PaintBox provides a canvas for custom drawing.
 *
 * Use PaintBox to add custom images to a form. Unlike Image, which displays
 * a stored image, PaintBox requires the application to draw directly on a canvas.
 * Use the OnPaint event handler to draw on the paint box's Canvas.
 *
 * PHP 8.4 version with Property Hooks.
 */
class PaintBox extends Control
{
    protected ?Canvas $_canvas = null;
    protected ?string $_onpaint = null;
    protected ?string $_onclick = null;
    protected ?string $_ondblclick = null;

    // Property Hooks
    public Canvas $Canvas {
        get {
            if ($this->_canvas === null) {
                $this->_canvas = new Canvas($this);
            }
            $this->_canvas->SetCanvasProperties($this->Name);
            return $this->_canvas;
        }
    }

    public ?string $OnPaint {
        get => $this->_onpaint;
        set => $this->_onpaint = $value;
    }

    public ?string $OnClick {
        get => $this->_onclick;
        set => $this->_onclick = $value;
    }

    public ?string $OnDblClick {
        get => $this->_ondblclick;
        set => $this->_ondblclick = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_canvas = new Canvas($this);
        $this->_width = 100;
        $this->_height = 100;
    }

    /**
     * Initialize and handle events.
     */
    public function init(): void
    {
        parent::init();

        $hiddenName = $this->readJSWrapperHiddenFieldName();
        $submitEventValue = $this->input->$hiddenName ?? null;

        if (is_object($submitEventValue) && method_exists($submitEventValue, 'asString')) {
            // Check for click event
            if ($this->_onclick !== null) {
                $expectedValue = $this->readJSWrapperSubmitEventValue($this->_onclick);
                if ($submitEventValue->asString() === $expectedValue) {
                    $this->callEvent('onclick', []);
                }
            }

            // Check for double-click event
            if ($this->_ondblclick !== null) {
                $expectedValue = $this->readJSWrapperSubmitEventValue($this->_ondblclick);
                if ($submitEventValue->asString() === $expectedValue) {
                    $this->callEvent('ondblclick', []);
                }
            }
        }
    }

    /**
     * Dump for AJAX updates.
     */
    public function dumpForAjax(): void
    {
        $this->callEvent('onpaint', $this->Canvas);
        if ($this->_canvas !== null) {
            $this->_canvas->Paint();
        }
    }

    /**
     * Dump header code for canvas initialization.
     */
    public function dumpHeaderCode(): void
    {
        if (($this->ControlState & CS_DESIGNING) !== CS_DESIGNING && $this->_canvas !== null) {
            $this->_canvas->InitLibrary();
        }
    }

    /**
     * Dump JavaScript for event handling.
     */
    public function dumpJavascript(): void
    {
        parent::dumpJavascript();

        if ($this->_onclick !== null && !defined($this->_onclick)) {
            define($this->_onclick, 1);
            echo $this->getJSWrapperFunction($this->_onclick);
        }

        if ($this->_ondblclick !== null && !defined($this->_ondblclick)) {
            define($this->_ondblclick, 1);
            echo $this->getJSWrapperFunction($this->_ondblclick);
        }
    }

    /**
     * Render the paint box.
     */
    protected function dumpContents(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            $name = htmlspecialchars($this->Name);
            echo "<table width=\"{$this->_width}\" height=\"{$this->_height}\" border=\"0\" style=\"border:1px dotted #000000\" cellpadding=\"0\" cellspacing=\"0\">\n";
            echo "<tr>\n";
            echo "<td align=\"center\">{$name}</td>\n";
            echo "</tr>\n";
            echo "</table>\n";
            return;
        }

        $style = '';
        $hint = '';

        if ($this->_showHint && $this->_hint !== '') {
            $hint = "title=\"" . htmlspecialchars($this->_hint) . "\"";
        }

        // Size
        if (!$this->_adjusttolayout) {
            $style .= "height:{$this->_height}px;width:{$this->_width}px;";
        } else {
            $style .= "height:100%;width:100%;";
        }

        // Events
        $events = $this->readJsEvents();
        $this->addJSWrapperToEvents($events, $this->_onclick, $this->_jsonclick ?? null, 'onclick');
        $this->addJSWrapperToEvents($events, $this->_ondblclick, $this->_jsondblclick ?? null, 'ondblclick');

        $name = htmlspecialchars($this->Name);

        echo "<div id=\"{$name}\" {$hint} style=\"{$style}\" {$events}>";

        if ($this->_canvas !== null) {
            $this->_canvas->BeginDraw();
            $this->callEvent('onpaint', $this->_canvas);
            $this->_canvas->EndDraw();
        }

        // Hidden field for event handling
        if ($this->_onclick !== null || $this->_ondblclick !== null) {
            $hiddenwrapperfield = $this->readJSWrapperHiddenFieldName();
            echo "<input type=\"hidden\" id=\"{$hiddenwrapperfield}\" name=\"{$hiddenwrapperfield}\" value=\"\" />";
        }

        echo "</div>";
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
    public function readCanvas(): Canvas { return $this->Canvas; }

    public function getOnPaint(): ?string { return $this->_onpaint; }
    public function setOnPaint(?string $value): void { $this->OnPaint = $value; }
    public function defaultOnPaint(): ?string { return null; }

    public function getOnClick(): ?string { return $this->_onclick; }
    public function setOnClick(?string $value): void { $this->OnClick = $value; }
    public function defaultOnClick(): ?string { return null; }

    public function getOnDblClick(): ?string { return $this->_ondblclick; }
    public function setOnDblClick(?string $value): void { $this->OnDblClick = $value; }
    public function defaultOnDblClick(): ?string { return null; }
}
