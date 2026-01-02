<?php

declare(strict_types=1);

namespace VCL\ComCtrls;

use VCL\ComCtrls\Enums\ProgressBarOrientation;

/**
 * ProgressBar provides visual feedback about the progress of a procedure.
 *
 * Progress bars provide users with visual feedback about the progress of
 * a procedure within an application. As the procedure progresses, the
 * rectangular progress bar gradually fills from left to right.
 *
 * PHP 8.4 version.
 */
class ProgressBar extends CustomProgressBar
{
    // Publish properties
    public function getOrientation(): ProgressBarOrientation|string
    {
        return $this->readOrientation();
    }

    public function setOrientation(ProgressBarOrientation|string $value): void
    {
        $this->writeOrientation($value);
    }

    public function getPosition(): int
    {
        return $this->readPosition();
    }

    public function setPosition(int $value): void
    {
        $this->writePosition($value);
    }

    public function getMin(): int
    {
        return $this->readMin();
    }

    public function setMin(int $value): void
    {
        $this->writeMin($value);
    }

    public function getMax(): int
    {
        return $this->readMax();
    }

    public function setMax(int $value): void
    {
        $this->writeMax($value);
    }

    public function getStep(): int
    {
        return $this->readStep();
    }

    public function setStep(int $value): void
    {
        $this->writeStep($value);
    }
}
