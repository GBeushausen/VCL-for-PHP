<?php

declare(strict_types=1);

namespace VCL\ComCtrls;

use VCL\UI\FocusControl;
use VCL\ComCtrls\Enums\ProgressBarOrientation;

/**
 * CustomProgressBar is the base class for progress bar controls.
 *
 * Progress bars provide users with visual feedback about the progress
 * of a procedure within an application.
 *
 * PHP 8.4 version with Property Hooks.
 */
class CustomProgressBar extends FocusControl
{
    protected ProgressBarOrientation|string $_orientation = ProgressBarOrientation::Horizontal;
    protected int $_position = 50;
    protected int $_min = 0;
    protected int $_max = 100;
    protected int $_step = 10;

    // Property Hooks
    public ProgressBarOrientation|string $Orientation {
        get => $this->_orientation;
        set {
            $newValue = $value instanceof ProgressBarOrientation
                ? $value
                : ProgressBarOrientation::from($value);

            if ($newValue !== $this->_orientation) {
                // Swap dimensions when orientation changes
                $w = $this->_width;
                $h = $this->_height;

                if ($newValue === ProgressBarOrientation::Horizontal && $w < $h) {
                    $this->_height = $w;
                    $this->_width = $h;
                } elseif ($newValue === ProgressBarOrientation::Vertical && $w > $h) {
                    $this->_height = $w;
                    $this->_width = $h;
                }

                $this->_orientation = $newValue;
            }
        }
    }

    public int $Position {
        get => $this->_position;
        set => $this->_position = max($this->_min, min($this->_max, $value));
    }

    public int $Min {
        get => $this->_min;
        set => $this->_min = $value;
    }

    public int $Max {
        get => $this->_max;
        set => $this->_max = $value;
    }

    public int $Step {
        get => $this->_step;
        set => $this->_step = max(1, $value);
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_controlstyle['csSlowRedraw'] = true;
        $this->_width = 200;
        $this->_height = 17;
    }

    /**
     * Advance position by specified amount.
     */
    public function stepBy(int $value): void
    {
        $newPosition = $this->_position + $value;
        $this->Position = $newPosition;
    }

    /**
     * Advance position by step amount.
     */
    public function stepIt(): void
    {
        $this->stepBy($this->_step);
    }

    /**
     * Render the progress bar using HTML5 progress element.
     */
    protected function dumpContents(): void
    {
        $name = htmlspecialchars($this->Name);
        $class = $this->readStyleClass();
        $classAttr = $class !== '' ? " class=\"{$class}\"" : '';

        $orient = $this->_orientation === ProgressBarOrientation::Vertical ||
                  (is_string($this->_orientation) && $this->_orientation === 'pbVertical');

        $style = "width:{$this->_width}px;height:{$this->_height}px;";
        if ($orient) {
            $style .= "writing-mode:vertical-lr;";
        }

        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $style .= "visibility:hidden;";
        }

        echo "<progress id=\"{$name}\" value=\"{$this->_position}\" max=\"{$this->_max}\" style=\"{$style}\"{$classAttr}>";
        echo round(($this->_position / $this->_max) * 100) . "%";
        echo "</progress>";
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
    protected function readOrientation(): ProgressBarOrientation|string { return $this->_orientation; }
    protected function writeOrientation(ProgressBarOrientation|string $value): void { $this->Orientation = $value; }
    public function defaultOrientation(): string { return 'pbHorizontal'; }

    protected function readPosition(): int { return $this->_position; }
    protected function writePosition(int $value): void { $this->Position = $value; }

    protected function readMin(): int { return $this->_min; }
    protected function writeMin(int $value): void { $this->Min = $value; }
    public function defaultMin(): int { return 0; }

    protected function readMax(): int { return $this->_max; }
    protected function writeMax(int $value): void { $this->Max = $value; }
    public function defaultMax(): int { return 100; }

    protected function readStep(): int { return $this->_step; }
    protected function writeStep(int $value): void { $this->Step = $value; }
    public function defaultStep(): int { return 10; }
}
