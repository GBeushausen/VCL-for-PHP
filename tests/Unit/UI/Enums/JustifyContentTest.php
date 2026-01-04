<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\UI\Enums;

use PHPUnit\Framework\TestCase;
use VCL\UI\Enums\JustifyContent;

class JustifyContentTest extends TestCase
{
    public function testToTailwind(): void
    {
        $this->assertSame('justify-start', JustifyContent::Start->toTailwind());
        $this->assertSame('justify-end', JustifyContent::End->toTailwind());
        $this->assertSame('justify-center', JustifyContent::Center->toTailwind());
        $this->assertSame('justify-between', JustifyContent::Between->toTailwind());
        $this->assertSame('justify-around', JustifyContent::Around->toTailwind());
        $this->assertSame('justify-evenly', JustifyContent::Evenly->toTailwind());
        $this->assertSame('justify-stretch', JustifyContent::Stretch->toTailwind());
    }
}
