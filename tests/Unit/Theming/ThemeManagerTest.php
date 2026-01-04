<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Theming;

use PHPUnit\Framework\TestCase;
use VCL\Theming\ThemeManager;

class ThemeManagerTest extends TestCase
{
    protected function setUp(): void
    {
        ThemeManager::reset();
    }

    protected function tearDown(): void
    {
        ThemeManager::reset();
    }

    public function testSingletonInstance(): void
    {
        $instance1 = ThemeManager::getInstance();
        $instance2 = ThemeManager::getInstance();

        $this->assertSame($instance1, $instance2);
    }

    public function testDefaultTheme(): void
    {
        $manager = ThemeManager::getInstance();

        $this->assertSame('light', $manager->getTheme());
        $this->assertFalse($manager->isDarkMode());
    }

    public function testSetTheme(): void
    {
        $manager = ThemeManager::getInstance();
        $manager->setTheme('dark');

        $this->assertSame('dark', $manager->getTheme());
        $this->assertTrue($manager->isDarkMode());
    }

    public function testGetThemeAttribute(): void
    {
        $manager = ThemeManager::getInstance();

        $this->assertSame('data-theme="light"', $manager->getThemeAttribute());

        $manager->setTheme('dark');
        $this->assertSame('data-theme="dark"', $manager->getThemeAttribute());
    }

    public function testDefaultPrefix(): void
    {
        $manager = ThemeManager::getInstance();

        $this->assertSame('vcl', $manager->getPrefix());
    }

    public function testSetPrefix(): void
    {
        $manager = ThemeManager::getInstance();
        $manager->setPrefix('myapp');

        $this->assertSame('myapp', $manager->getPrefix());
    }

    public function testGetComponentClassDefault(): void
    {
        $manager = ThemeManager::getInstance();

        $this->assertSame('vcl-button', $manager->getComponentClass('button'));
        $this->assertSame('vcl-button', $manager->getComponentClass('button', 'default'));
        $this->assertSame('vcl-button', $manager->getComponentClass('button', ''));
    }

    public function testGetComponentClassWithVariant(): void
    {
        $manager = ThemeManager::getInstance();

        $this->assertSame('vcl-button-primary', $manager->getComponentClass('button', 'primary'));
        $this->assertSame('vcl-button-secondary', $manager->getComponentClass('button', 'secondary'));
        $this->assertSame('vcl-input-error', $manager->getComponentClass('input', 'error'));
    }

    public function testGetVariants(): void
    {
        $manager = ThemeManager::getInstance();

        $buttonVariants = $manager->getVariants('button');
        $this->assertContains('default', $buttonVariants);
        $this->assertContains('primary', $buttonVariants);
        $this->assertContains('secondary', $buttonVariants);
    }

    public function testHasVariant(): void
    {
        $manager = ThemeManager::getInstance();

        $this->assertTrue($manager->hasVariant('button', 'primary'));
        $this->assertTrue($manager->hasVariant('button', 'default'));
        $this->assertFalse($manager->hasVariant('button', 'nonexistent'));
    }

    public function testRegisterVariants(): void
    {
        $manager = ThemeManager::getInstance();
        $manager->registerVariants('custom', ['default', 'special', 'extra']);

        $variants = $manager->getVariants('custom');
        $this->assertContains('default', $variants);
        $this->assertContains('special', $variants);
        $this->assertContains('extra', $variants);
    }

    public function testAddVariant(): void
    {
        $manager = ThemeManager::getInstance();
        $manager->addVariant('button', 'custom');

        $this->assertTrue($manager->hasVariant('button', 'custom'));
    }

    public function testGetCssLink(): void
    {
        $manager = ThemeManager::getInstance();

        $link = $manager->getCssLink('/assets/css/theme.css');
        $this->assertStringContainsString('href="/assets/css/theme.css"', $link);
        $this->assertStringContainsString('<link', $link);
        $this->assertStringContainsString('stylesheet', $link);
    }

    public function testGetThemeSwitchScript(): void
    {
        $manager = ThemeManager::getInstance();

        $script = $manager->getThemeSwitchScript();
        $this->assertStringContainsString('<script>', $script);
        $this->assertStringContainsString('VCLTheme', $script);
        $this->assertStringContainsString('toggleTheme', $script);
    }
}
