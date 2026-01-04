<?php

declare(strict_types=1);

namespace VCL\ComCtrls;

use VCL\UI\Control;
use VCL\ComCtrls\Enums\TrackBarOrientation;

/**
 * TrackBar represents a slider control for selecting values.
 *
 * TrackBar is used to allow users to change a numerical value by
 * dragging a slider. The slider can be oriented horizontally or vertically.
 *
 * PHP 8.4 version with Property Hooks and HTML5 range input.
 */
class TrackBar extends Control
{
    protected TrackBarOrientation|string $_orientation = TrackBarOrientation::Horizontal;
    protected int $_position = 0;
    protected int $_minposition = 0;
    protected int $_maxposition = 10;

    // Property Hooks
    public TrackBarOrientation|string $Orientation {
        get => $this->_orientation;
        set {
            $newValue = $value instanceof TrackBarOrientation
                ? $value
                : TrackBarOrientation::from($value);

            if ($newValue !== $this->_orientation) {
                $w = $this->_width;
                $h = $this->_height;

                if ($newValue === TrackBarOrientation::Horizontal && $w < $h) {
                    $this->_height = $w;
                    $this->_width = $h;
                } elseif ($newValue === TrackBarOrientation::Vertical && $w > $h) {
                    $this->_height = $w;
                    $this->_width = $h;
                }

                $this->_orientation = $newValue;
            }
        }
    }

    public int $Position {
        get => $this->_position;
        set => $this->_position = max($this->_minposition, min($this->_maxposition, $value));
    }

    public int $MinPosition {
        get => $this->_minposition;
        set => $this->_minposition = $value;
    }

    public int $MaxPosition {
        get => $this->_maxposition;
        set => $this->_maxposition = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_width = 150;
        $this->_height = 25;
    }

    /**
     * Pre-initialization: read submitted value.
     */
    public function preinit(): void
    {
        parent::preinit();

        $submitted = $this->input->{$this->Name . "_position"} ?? null;
        if (is_object($submitted) && method_exists($submitted, 'asInteger')) {
            $this->_position = $submitted->asInteger();
        } elseif (is_numeric($submitted)) {
            $this->_position = (int) $submitted;
        }
    }

    /**
     * Dump JavaScript events.
     */
    public function dumpJsEvents(): void
    {
        $this->dumpJSEvent($this->jsOnChange);
    }

    /**
     * Dump hidden form fields.
     */
    public function dumpFormItems(): void
    {
        $name = htmlspecialchars($this->Name);
        echo "<input type=\"hidden\" id=\"{$name}_position\" name=\"{$name}_position\" value=\"{$this->_position}\" />";
    }

    /**
     * Render the track bar using HTML5 range input.
     */
    protected function dumpContents(): void
    {
        $name = htmlspecialchars($this->Name);
        $class = $this->readStyleClass();
        $classAttr = $class !== '' ? " class=\"{$class}\"" : '';

        $isVertical = $this->_orientation === TrackBarOrientation::Vertical ||
            (is_string($this->_orientation) && $this->_orientation === 'tbVertical');

        $style = "width:{$this->_width}px;";
        if ($isVertical) {
            $style .= "writing-mode:vertical-lr;direction:rtl;height:{$this->_height}px;";
        }

        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $style .= "visibility:hidden;";
        }

        // JavaScript for updating hidden field
        $onchange = "document.getElementById('{$name}_position').value=this.value;";
        if (($this->ControlState & CS_DESIGNING) !== CS_DESIGNING && $this->_jsonchange !== null) {
            $onchange .= htmlspecialchars($this->_jsonchange) . "(this);";
        }

        echo "<input type=\"range\" id=\"{$name}\" name=\"{$name}_input\"";
        echo " min=\"{$this->_minposition}\" max=\"{$this->_maxposition}\" value=\"{$this->_position}\"";
        echo " style=\"{$style}\"{$classAttr}";
        echo " oninput=\"{$onchange}\"";
        echo " onchange=\"{$onchange}\"";

        if (!$this->_enabled) {
            echo " disabled";
        }

        echo " />";
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
    public function getOrientation(): TrackBarOrientation|string { return $this->_orientation; }
    public function setOrientation(TrackBarOrientation|string $value): void { $this->Orientation = $value; }
    public function defaultOrientation(): string { return 'tbHorizontal'; }

    public function getPosition(): int { return $this->_position; }
    public function setPosition(int $value): void { $this->Position = $value; }
    public function defaultPosition(): int { return 0; }

    public function getMinPosition(): int { return $this->_minposition; }
    public function setMinPosition(int $value): void { $this->MinPosition = $value; }
    public function defaultMinPosition(): int { return 0; }

    public function getMaxPosition(): int { return $this->_maxposition; }
    public function setMaxPosition(int $value): void { $this->MaxPosition = $value; }
    public function defaultMaxPosition(): int { return 10; }
}
