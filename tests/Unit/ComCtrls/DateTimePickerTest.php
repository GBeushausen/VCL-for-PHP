<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ComCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ComCtrls\DateTimePicker;

class DateTimePickerTest extends TestCase
{
    private DateTimePicker $picker;

    protected function setUp(): void
    {
        $this->picker = new DateTimePicker();
        $this->picker->Name = 'TestDateTimePicker';
    }

    public function testNameProperty(): void
    {
        $this->assertSame('TestDateTimePicker', $this->picker->Name);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->picker);
    }
}
