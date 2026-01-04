<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\UI\Enums;

use PHPUnit\Framework\TestCase;
use VCL\UI\Enums\AlignItems;

class AlignItemsTest extends TestCase
{
    public function testToTailwind(): void
    {
        $this->assertSame('items-start', AlignItems::Start->toTailwind());
        $this->assertSame('items-end', AlignItems::End->toTailwind());
        $this->assertSame('items-center', AlignItems::Center->toTailwind());
        $this->assertSame('items-baseline', AlignItems::Baseline->toTailwind());
        $this->assertSame('items-stretch', AlignItems::Stretch->toTailwind());
    }
}
