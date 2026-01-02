<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Templates;

use PHPUnit\Framework\TestCase;
use VCL\Templates\PageTemplate;

/**
 * Concrete implementation for testing.
 */
class TestPageTemplate extends PageTemplate
{
    public bool $initializeCalled = false;
    public bool $assignComponentsCalled = false;
    public bool $dumpTemplateCalled = false;

    public function initialize(): void
    {
        $this->initializeCalled = true;
    }

    public function assignComponents(): void
    {
        $this->assignComponentsCalled = true;
    }

    public function dumpTemplate(): void
    {
        $this->dumpTemplateCalled = true;
        echo 'Template output';
    }
}

class PageTemplateTest extends TestCase
{
    private TestPageTemplate $template;

    protected function setUp(): void
    {
        $this->template = new TestPageTemplate();
        $this->template->Name = 'TestTemplate';
    }

    public function testDefaultFileName(): void
    {
        $this->assertSame('', $this->template->FileName);
    }

    public function testFileNameProperty(): void
    {
        $this->template->FileName = 'template.html';
        $this->assertSame('template.html', $this->template->FileName);
    }

    public function testDefaultTemplateDir(): void
    {
        $this->assertSame('', $this->template->TemplateDir);
    }

    public function testTemplateDirProperty(): void
    {
        $this->template->TemplateDir = '/templates/';
        $this->assertSame('/templates', $this->template->TemplateDir);
    }

    public function testTemplateDirTrimsTrailingSlash(): void
    {
        $this->template->TemplateDir = '/path/to/templates/';
        $this->assertSame('/path/to/templates', $this->template->TemplateDir);
    }

    public function testAssignVariable(): void
    {
        $this->template->assignVariable('title', 'Page Title');
        $this->assertSame('Page Title', $this->template->get('title'));
    }

    public function testGet(): void
    {
        $this->assertNull($this->template->get('nonexistent'));

        $this->template->assignVariable('key', 'value');
        $this->assertSame('value', $this->template->get('key'));
    }

    public function testHas(): void
    {
        $this->assertFalse($this->template->has('key'));

        $this->template->assignVariable('key', 'value');
        $this->assertTrue($this->template->has('key'));
    }

    public function testClearVariables(): void
    {
        $this->template->assignVariable('key1', 'value1');
        $this->template->assignVariable('key2', 'value2');

        $this->assertTrue($this->template->has('key1'));

        $this->template->clearVariables();

        $this->assertFalse($this->template->has('key1'));
        $this->assertFalse($this->template->has('key2'));
    }

    public function testGetVariables(): void
    {
        $this->template->assignVariable('a', 1);
        $this->template->assignVariable('b', 2);

        $vars = $this->template->getVariables();

        $this->assertArrayHasKey('a', $vars);
        $this->assertArrayHasKey('b', $vars);
        $this->assertSame(1, $vars['a']);
        $this->assertSame(2, $vars['b']);
    }

    public function testRenderCallsAllMethods(): void
    {
        $this->assertFalse($this->template->initializeCalled);
        $this->assertFalse($this->template->assignComponentsCalled);
        $this->assertFalse($this->template->dumpTemplateCalled);

        ob_start();
        $this->template->render();
        ob_get_clean();

        $this->assertTrue($this->template->initializeCalled);
        $this->assertTrue($this->template->assignComponentsCalled);
        $this->assertTrue($this->template->dumpTemplateCalled);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->template);
    }
}
