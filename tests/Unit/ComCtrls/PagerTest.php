<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ComCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ComCtrls\Pager;

class PagerTest extends TestCase
{
    private Pager $pager;

    protected function setUp(): void
    {
        $this->pager = new Pager();
        $this->pager->Name = 'TestPager';
    }

    public function testNameProperty(): void
    {
        $this->assertSame('TestPager', $this->pager->Name);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->pager);
    }
}
