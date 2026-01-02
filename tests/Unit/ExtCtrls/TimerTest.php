<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ExtCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ExtCtrls\Timer;

class TimerTest extends TestCase
{
    private Timer $timer;

    protected function setUp(): void
    {
        $this->timer = new Timer();
        $this->timer->Name = 'TestTimer';
    }

    public function testDefaultInterval(): void
    {
        $this->assertSame(1000, $this->timer->Interval);
    }

    public function testIntervalProperty(): void
    {
        $this->timer->Interval = 5000;
        $this->assertSame(5000, $this->timer->Interval);
    }

    public function testIntervalRejectsNegative(): void
    {
        $this->timer->Interval = -100;
        $this->assertSame(0, $this->timer->Interval);
    }

    public function testDefaultEnabled(): void
    {
        $this->assertTrue($this->timer->Enabled);
    }

    public function testEnabledProperty(): void
    {
        $this->timer->Enabled = false;
        $this->assertFalse($this->timer->Enabled);
    }

    public function testJsOnTimerEvent(): void
    {
        $this->timer->jsOnTimer = 'onTimerTick';
        $this->assertSame('onTimerTick', $this->timer->jsOnTimer);
    }

    public function testDefaultJsOnTimerIsNull(): void
    {
        $this->assertNull($this->timer->jsOnTimer);
    }
}
