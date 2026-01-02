<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Menus;

use PHPUnit\Framework\TestCase;
use VCL\Menus\PopupMenu;

class PopupMenuTest extends TestCase
{
    private PopupMenu $menu;

    protected function setUp(): void
    {
        $this->menu = new PopupMenu();
        $this->menu->Name = 'TestPopupMenu';
    }

    public function testOnClickEvent(): void
    {
        $this->menu->OnClick = 'handleClick';
        $this->assertSame('handleClick', $this->menu->OnClick);
    }

    public function testJsOnClickEvent(): void
    {
        $this->menu->jsOnClick = 'jsHandleClick';
        $this->assertSame('jsHandleClick', $this->menu->jsOnClick);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->menu);
    }
}
