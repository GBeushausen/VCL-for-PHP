<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ExtCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ExtCtrls\Clock;

class ClockTest extends TestCase
{
    private Clock $clock;

    protected function setUp(): void
    {
        $this->clock = new Clock();
        $this->clock->Name = 'TestClock';
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(120, $this->clock->Width);
        $this->assertSame(40, $this->clock->Height);
    }

    public function testDefaultShowSeconds(): void
    {
        $this->assertTrue($this->clock->ShowSeconds);
    }

    public function testShowSecondsProperty(): void
    {
        $this->clock->ShowSeconds = false;
        $this->assertFalse($this->clock->ShowSeconds);
    }

    public function testDefaultShowDate(): void
    {
        $this->assertFalse($this->clock->ShowDate);
    }

    public function testShowDateProperty(): void
    {
        $this->clock->ShowDate = true;
        $this->assertTrue($this->clock->ShowDate);
    }

    public function testDefaultFormat24h(): void
    {
        $this->assertTrue($this->clock->Format24h);
    }

    public function testFormat24hProperty(): void
    {
        $this->clock->Format24h = false;
        $this->assertFalse($this->clock->Format24h);
    }

    public function testDefaultDateFormat(): void
    {
        $this->assertSame('YYYY-MM-DD', $this->clock->DateFormat);
    }

    public function testDateFormatProperty(): void
    {
        $this->clock->DateFormat = 'DD/MM/YYYY';
        $this->assertSame('DD/MM/YYYY', $this->clock->DateFormat);
    }

    public function testAlarmTimeProperty(): void
    {
        $this->clock->AlarmTime = '+5000';
        $this->assertSame('+5000', $this->clock->AlarmTime);
    }

    public function testJsOnAlarmProperty(): void
    {
        $this->clock->jsOnAlarm = 'function() { alert("Alarm!"); }';
        $this->assertSame('function() { alert("Alarm!"); }', $this->clock->jsOnAlarm);
    }

    public function testExtendsPanel(): void
    {
        $this->assertInstanceOf(\VCL\ExtCtrls\Panel::class, $this->clock);
    }
}
