<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Graphics;

use PHPUnit\Framework\TestCase;
use VCL\Graphics\ImageList;

class ImageListTest extends TestCase
{
    private ImageList $imageList;

    protected function setUp(): void
    {
        $this->imageList = new ImageList();
        $this->imageList->Name = 'TestImageList';
    }

    public function testDefaultImages(): void
    {
        $this->assertSame([], $this->imageList->Images);
    }

    public function testImagesProperty(): void
    {
        $images = [
            'home' => '/images/home.png',
            'save' => '/images/save.png',
        ];
        $this->imageList->Images = $images;
        $this->assertSame($images, $this->imageList->Images);
    }

    public function testCount(): void
    {
        $this->assertSame(0, $this->imageList->Count);

        $this->imageList->Images = [
            'a' => '/a.png',
            'b' => '/b.png',
            'c' => '/c.png',
        ];
        $this->assertSame(3, $this->imageList->Count);
    }

    public function testAddImage(): void
    {
        $count = $this->imageList->addImage('/images/icon.png', 'icon');
        $this->assertSame(1, $count);
        $this->assertSame('/images/icon.png', $this->imageList->getImage('icon'));
    }

    public function testAddImageWithoutKey(): void
    {
        $this->imageList->addImage('/images/first.png');
        $this->imageList->addImage('/images/second.png');

        $this->assertSame(2, $this->imageList->Count);
        $this->assertSame('/images/first.png', $this->imageList->getImage(0));
        $this->assertSame('/images/second.png', $this->imageList->getImage(1));
    }

    public function testGetImage(): void
    {
        $this->imageList->Images = ['test' => '/test.png'];
        $this->assertSame('/test.png', $this->imageList->getImage('test'));
    }

    public function testGetImageReturnsNullForNonexistent(): void
    {
        $this->assertNull($this->imageList->getImage('nonexistent'));
    }

    public function testGetImageByID(): void
    {
        $this->imageList->Images = ['icon' => '/images/icon.png'];
        $result = $this->imageList->getImageByID('icon');
        $this->assertSame('/images/icon.png', $result);
    }

    public function testGetImageByIDPreformatted(): void
    {
        $this->imageList->Images = ['icon' => '/images/icon.png'];

        $result = $this->imageList->getImageByID('icon', true);
        $this->assertSame('"/images/icon.png"', $result);

        $result = $this->imageList->getImageByID('nonexistent', true);
        $this->assertSame('null', $result);
    }

    public function testRemoveImage(): void
    {
        $this->imageList->Images = ['a' => '/a.png', 'b' => '/b.png'];

        $this->assertTrue($this->imageList->removeImage('a'));
        $this->assertSame(1, $this->imageList->Count);
        $this->assertNull($this->imageList->getImage('a'));
    }

    public function testRemoveImageReturnsFalseForNonexistent(): void
    {
        $this->assertFalse($this->imageList->removeImage('nonexistent'));
    }

    public function testClear(): void
    {
        $this->imageList->Images = ['a' => '/a.png', 'b' => '/b.png'];
        $this->assertSame(2, $this->imageList->Count);

        $this->imageList->clear();
        $this->assertSame(0, $this->imageList->Count);
    }

    public function testHasImage(): void
    {
        $this->assertFalse($this->imageList->hasImage('icon'));

        $this->imageList->addImage('/icon.png', 'icon');
        $this->assertTrue($this->imageList->hasImage('icon'));
    }

    public function testGetKeys(): void
    {
        $this->imageList->Images = [
            'home' => '/home.png',
            'save' => '/save.png',
            'edit' => '/edit.png',
        ];

        $keys = $this->imageList->getKeys();
        $this->assertCount(3, $keys);
        $this->assertContains('home', $keys);
        $this->assertContains('save', $keys);
        $this->assertContains('edit', $keys);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->imageList);
    }
}
