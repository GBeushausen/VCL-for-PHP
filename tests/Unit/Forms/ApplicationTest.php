<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Forms;

use PHPUnit\Framework\TestCase;
use VCL\Forms\Application;

class ApplicationTest extends TestCase
{
    public function testGetInstance(): void
    {
        $app = Application::getInstance();
        $this->assertInstanceOf(Application::class, $app);
    }

    public function testGetInstanceReturnsSameInstance(): void
    {
        $app1 = Application::getInstance();
        $app2 = Application::getInstance();
        $this->assertSame($app1, $app2);
    }

    public function testDefaultLanguage(): void
    {
        $app = Application::getInstance();
        $this->assertSame('', $app->Language);
    }

    public function testLanguageProperty(): void
    {
        $app = Application::getInstance();
        $app->Language = 'de-DE';
        $this->assertSame('de-DE', $app->Language);

        // Reset for other tests
        $app->Language = '';
    }

    public function testIsComponent(): void
    {
        $app = Application::getInstance();
        $this->assertInstanceOf(\VCL\Core\Component::class, $app);
    }
}
