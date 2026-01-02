<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Buttons;

use PHPUnit\Framework\TestCase;
use VCL\Buttons\BitBtn;

class BitBtnTest extends TestCase
{
    private BitBtn $btn;

    protected function setUp(): void
    {
        $this->btn = new BitBtn();
        $this->btn->Name = 'TestBitBtn';
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(75, $this->btn->Width);
        $this->assertSame(25, $this->btn->Height);
    }

    public function testImageSourceProperty(): void
    {
        $this->btn->ImageSource = 'images/icon.png';
        $this->assertSame('images/icon.png', $this->btn->ImageSource);
    }

    public function testImageDisabledProperty(): void
    {
        $this->btn->ImageDisabled = 'images/icon_disabled.png';
        $this->assertSame('images/icon_disabled.png', $this->btn->ImageDisabled);
    }

    public function testImageClickedProperty(): void
    {
        $this->btn->ImageClicked = 'images/icon_clicked.png';
        $this->assertSame('images/icon_clicked.png', $this->btn->ImageClicked);
    }

    public function testDefaultButtonLayout(): void
    {
        $this->assertSame('blImageLeft', $this->btn->ButtonLayout);
    }

    public function testButtonLayoutProperty(): void
    {
        $this->btn->ButtonLayout = 'blImageTop';
        $this->assertSame('blImageTop', $this->btn->ButtonLayout);
    }

    public function testDefaultKind(): void
    {
        $this->assertSame('bkCustom', $this->btn->Kind);
    }

    public function testKindProperty(): void
    {
        $this->btn->Kind = 'bkOK';
        $this->assertSame('bkOK', $this->btn->Kind);
    }

    public function testDefaultButtonType(): void
    {
        $this->assertSame('btSubmit', $this->btn->ButtonType);
    }

    public function testButtonTypeProperty(): void
    {
        $this->btn->ButtonType = 'btReset';
        $this->assertSame('btReset', $this->btn->ButtonType);
    }

    public function testDefaultSpacing(): void
    {
        $this->assertSame(4, $this->btn->Spacing);
    }

    public function testSpacingProperty(): void
    {
        $this->btn->Spacing = 8;
        $this->assertSame(8, $this->btn->Spacing);
    }

    public function testSpacingRejectsNegative(): void
    {
        $this->btn->Spacing = -5;
        $this->assertSame(0, $this->btn->Spacing);
    }

    public function testDefaultProperty(): void
    {
        $this->btn->Default = true;
        $this->assertTrue($this->btn->Default);
    }

    public function testCancelProperty(): void
    {
        $this->btn->Cancel = true;
        $this->assertTrue($this->btn->Cancel);
    }

    public function testOnClickEvent(): void
    {
        $this->btn->OnClick = 'handleClick';
        $this->assertSame('handleClick', $this->btn->OnClick);
    }

    public function testActionProperty(): void
    {
        $this->btn->Action = 'submit_form';
        $this->assertSame('submit_form', $this->btn->Action);
    }
}
