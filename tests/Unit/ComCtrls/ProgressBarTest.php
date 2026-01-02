<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ComCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ComCtrls\ProgressBar;
use VCL\ComCtrls\Enums\ProgressBarOrientation;

class ProgressBarTest extends TestCase
{
    private ProgressBar $progress;

    protected function setUp(): void
    {
        $this->progress = new ProgressBar();
        $this->progress->Name = 'TestProgressBar';
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(200, $this->progress->Width);
        $this->assertSame(17, $this->progress->Height);
    }

    public function testDefaultPosition(): void
    {
        $this->assertSame(50, $this->progress->Position);
    }

    public function testPositionProperty(): void
    {
        $this->progress->Position = 75;
        $this->assertSame(75, $this->progress->Position);
    }

    public function testPositionClampedToMax(): void
    {
        $this->progress->Position = 150;
        $this->assertSame(100, $this->progress->Position);
    }

    public function testPositionClampedToMin(): void
    {
        $this->progress->Position = -10;
        $this->assertSame(0, $this->progress->Position);
    }

    public function testDefaultMin(): void
    {
        $this->assertSame(0, $this->progress->Min);
    }

    public function testMinProperty(): void
    {
        $this->progress->Min = 10;
        $this->assertSame(10, $this->progress->Min);
    }

    public function testDefaultMax(): void
    {
        $this->assertSame(100, $this->progress->Max);
    }

    public function testMaxProperty(): void
    {
        $this->progress->Max = 200;
        $this->assertSame(200, $this->progress->Max);
    }

    public function testDefaultStep(): void
    {
        $this->assertSame(10, $this->progress->Step);
    }

    public function testStepProperty(): void
    {
        $this->progress->Step = 5;
        $this->assertSame(5, $this->progress->Step);
    }

    public function testStepMinimumIsOne(): void
    {
        $this->progress->Step = 0;
        $this->assertSame(1, $this->progress->Step);
    }

    public function testDefaultOrientation(): void
    {
        $this->assertEquals(ProgressBarOrientation::Horizontal, $this->progress->Orientation);
    }

    public function testOrientationProperty(): void
    {
        $this->progress->Orientation = ProgressBarOrientation::Vertical;
        $this->assertEquals(ProgressBarOrientation::Vertical, $this->progress->Orientation);
    }

    public function testStepIt(): void
    {
        $this->progress->Position = 0;
        $this->progress->Step = 10;
        $this->progress->stepIt();
        $this->assertSame(10, $this->progress->Position);
    }

    public function testStepBy(): void
    {
        $this->progress->Position = 20;
        $this->progress->stepBy(15);
        $this->assertSame(35, $this->progress->Position);
    }
}
