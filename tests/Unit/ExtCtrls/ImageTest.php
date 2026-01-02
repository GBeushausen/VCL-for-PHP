<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ExtCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ExtCtrls\Image;

class ImageTest extends TestCase
{
    private Image $image;

    protected function setUp(): void
    {
        $this->image = new Image();
        $this->image->Name = 'TestImage';
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(105, $this->image->Width);
        $this->assertSame(105, $this->image->Height);
    }

    public function testAutoSizeProperty(): void
    {
        $this->image->AutoSize = true;
        $this->assertTrue($this->image->AutoSize);
    }

    public function testBorderProperty(): void
    {
        $this->image->Border = true;
        $this->assertTrue($this->image->Border);
    }

    public function testBorderColorProperty(): void
    {
        $this->image->BorderColor = '#000000';
        $this->assertSame('#000000', $this->image->BorderColor);
    }

    public function testCenterProperty(): void
    {
        $this->image->Center = true;
        $this->assertTrue($this->image->Center);
    }

    public function testImageSourceProperty(): void
    {
        $this->image->ImageSource = 'images/photo.jpg';
        $this->assertSame('images/photo.jpg', $this->image->ImageSource);
    }

    public function testLinkProperty(): void
    {
        $this->image->Link = 'https://example.com';
        $this->assertSame('https://example.com', $this->image->Link);
    }

    public function testLinkTargetProperty(): void
    {
        $this->image->LinkTarget = '_blank';
        $this->assertSame('_blank', $this->image->LinkTarget);
    }

    public function testProportionalProperty(): void
    {
        $this->image->Proportional = true;
        $this->assertTrue($this->image->Proportional);
    }

    public function testStretchProperty(): void
    {
        $this->image->Stretch = true;
        $this->assertTrue($this->image->Stretch);
    }

    public function testBinaryProperty(): void
    {
        $this->image->Binary = true;
        $this->assertTrue($this->image->Binary);
    }

    public function testBinaryTypeProperty(): void
    {
        $this->image->BinaryType = 'image/png';
        $this->assertSame('image/png', $this->image->BinaryType);
    }

    public function testDefaultBinaryType(): void
    {
        $this->assertSame('image/jpeg', $this->image->BinaryType);
    }

    public function testOnClickEvent(): void
    {
        $this->image->OnClick = 'handleClick';
        $this->assertSame('handleClick', $this->image->OnClick);
    }

    public function testDataFieldProperty(): void
    {
        $this->image->DataField = 'photo';
        $this->assertSame('photo', $this->image->DataField);
    }
}
