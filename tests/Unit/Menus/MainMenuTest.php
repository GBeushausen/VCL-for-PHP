<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Menus;

use PHPUnit\Framework\TestCase;
use VCL\Menus\MainMenu;

class MainMenuTest extends TestCase
{
    private MainMenu $menu;

    protected function setUp(): void
    {
        $this->menu = new MainMenu();
        $this->menu->Name = 'TestMainMenu';
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(300, $this->menu->Width);
        $this->assertSame(24, $this->menu->Height);
    }

    public function testItemsProperty(): void
    {
        $items = [
            ['Caption' => 'File', 'Tag' => 1],
            ['Caption' => 'Edit', 'Tag' => 2],
        ];
        $this->menu->Items = $items;
        $this->assertSame($items, $this->menu->Items);
    }

    public function testOnClickEvent(): void
    {
        $this->menu->OnClick = 'handleMenuClick';
        $this->assertSame('handleMenuClick', $this->menu->OnClick);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->menu);
    }
}
