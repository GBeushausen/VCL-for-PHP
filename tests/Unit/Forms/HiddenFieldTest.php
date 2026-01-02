<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Forms;

use PHPUnit\Framework\TestCase;
use VCL\Forms\HiddenField;

class HiddenFieldTest extends TestCase
{
    private HiddenField $hidden;

    protected function setUp(): void
    {
        $this->hidden = new HiddenField();
        $this->hidden->Name = 'TestHiddenField';
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(200, $this->hidden->Width);
        $this->assertSame(18, $this->hidden->Height);
    }

    public function testValueProperty(): void
    {
        $this->hidden->Value = 'secret_token';
        $this->assertSame('secret_token', $this->hidden->Value);
    }

    public function testDefaultValueIsEmpty(): void
    {
        $this->assertSame('', $this->hidden->Value);
    }

    public function testOnSubmitEvent(): void
    {
        $this->hidden->OnSubmit = 'handleSubmit';
        $this->assertSame('handleSubmit', $this->hidden->OnSubmit);
    }

    public function testDefaultOnSubmitIsNull(): void
    {
        $this->assertNull($this->hidden->OnSubmit);
    }
}
