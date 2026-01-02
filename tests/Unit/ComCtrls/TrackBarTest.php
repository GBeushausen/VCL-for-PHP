<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ComCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ComCtrls\TrackBar;

class TrackBarTest extends TestCase
{
    private TrackBar $trackbar;

    protected function setUp(): void
    {
        $this->trackbar = new TrackBar();
        $this->trackbar->Name = 'TestTrackBar';
    }

    public function testNameProperty(): void
    {
        $this->assertSame('TestTrackBar', $this->trackbar->Name);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->trackbar);
    }
}
