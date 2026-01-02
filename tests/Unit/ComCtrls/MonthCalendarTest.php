<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ComCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ComCtrls\MonthCalendar;

class MonthCalendarTest extends TestCase
{
    private MonthCalendar $calendar;

    protected function setUp(): void
    {
        $this->calendar = new MonthCalendar();
        $this->calendar->Name = 'TestMonthCalendar';
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(200, $this->calendar->Width);
        $this->assertSame(200, $this->calendar->Height);
    }

    public function testDefaultTimeZone(): void
    {
        $this->assertSame('UTC', $this->calendar->TimeZone);
    }

    public function testTimeZoneProperty(): void
    {
        $this->calendar->TimeZone = 'Europe/Berlin';
        $this->assertSame('Europe/Berlin', $this->calendar->TimeZone);
    }

    public function testDefaultShowsTime(): void
    {
        $this->assertTrue($this->calendar->ShowsTime);
    }

    public function testShowsTimeProperty(): void
    {
        $this->calendar->ShowsTime = false;
        $this->assertFalse($this->calendar->ShowsTime);
    }

    public function testDefaultFirstDay(): void
    {
        $this->assertSame(1, $this->calendar->FirstDay);
    }

    public function testFirstDayProperty(): void
    {
        $this->calendar->FirstDay = 0;
        $this->assertSame(0, $this->calendar->FirstDay);
    }

    public function testFirstDayClampedTo0To6(): void
    {
        $this->calendar->FirstDay = 10;
        $this->assertSame(6, $this->calendar->FirstDay);

        $this->calendar->FirstDay = -1;
        $this->assertSame(0, $this->calendar->FirstDay);
    }

    public function testDateProperty(): void
    {
        $this->calendar->Date = '2024-01-15';
        $this->assertSame('2024-01-15', $this->calendar->Date);
    }

    public function testDefaultDateFormat(): void
    {
        $this->assertSame('%m-%d-%Y %I:%M', $this->calendar->DateFormat);
    }

    public function testJsOnUpdateProperty(): void
    {
        $this->calendar->jsOnUpdate = 'handleUpdate';
        $this->assertSame('handleUpdate', $this->calendar->jsOnUpdate);
    }

    public function testIsFocusControl(): void
    {
        $this->assertInstanceOf(\VCL\UI\FocusControl::class, $this->calendar);
    }
}
