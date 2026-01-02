<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use VCL\Core\Persistent;

class PersistentTest extends TestCase
{
    private Persistent $persistent;

    protected function setUp(): void
    {
        $this->persistent = new Persistent();
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(Persistent::class, $this->persistent);
    }

    public function testExtendsVCLObject(): void
    {
        $this->assertInstanceOf(\VCL\Core\VCLObject::class, $this->persistent);
    }
}
