<?php
/**
 * VCL for PHP 3.0
 *
 * Integration tests for Page rendering
 */

declare(strict_types=1);

namespace VCL\Tests\Integration\Forms;

use PHPUnit\Framework\TestCase;
use VCL\Forms\Page;
use VCL\Forms\Application;
use VCL\StdCtrls\Button;
use VCL\StdCtrls\Label;
use VCL\StdCtrls\Edit;

class PageRenderTest extends TestCase
{
    private Page $page;

    protected function setUp(): void
    {
        // Create application context
        $app = new Application();

        $this->page = new Page($app);
        $this->page->Name = 'TestPage';
    }

    public function testPageWithButtonRendersHtml(): void
    {
        $button = new Button($this->page);
        $button->Name = 'Button1';
        $button->Parent = $this->page;
        $button->Caption = 'Test Button';

        $output = $this->captureOutput(function () {
            $this->page->show();
        });

        $this->assertStringContainsString('Button1', $output);
        $this->assertStringContainsString('Test Button', $output);
        $this->assertStringContainsString('<input', $output);
    }

    public function testPageWithLabelRendersHtml(): void
    {
        $label = new Label($this->page);
        $label->Name = 'Label1';
        $label->Parent = $this->page;
        $label->Caption = 'Hello World';

        $output = $this->captureOutput(function () {
            $this->page->show();
        });

        $this->assertStringContainsString('Label1', $output);
        $this->assertStringContainsString('Hello World', $output);
    }

    public function testPageWithMultipleControlsRendersAll(): void
    {
        $label = new Label($this->page);
        $label->Name = 'Label1';
        $label->Parent = $this->page;
        $label->Caption = 'Enter name:';

        $edit = new Edit($this->page);
        $edit->Name = 'Edit1';
        $edit->Parent = $this->page;

        $button = new Button($this->page);
        $button->Name = 'Button1';
        $button->Parent = $this->page;
        $button->Caption = 'Submit';

        $output = $this->captureOutput(function () {
            $this->page->show();
        });

        $this->assertStringContainsString('Label1', $output);
        $this->assertStringContainsString('Edit1', $output);
        $this->assertStringContainsString('Button1', $output);
    }

    public function testPageGeneratesValidHtmlStructure(): void
    {
        $output = $this->captureOutput(function () {
            $this->page->show();
        });

        $this->assertStringContainsString('<!DOCTYPE', $output);
        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('<head>', $output);
        $this->assertStringContainsString('<body', $output);
        $this->assertStringContainsString('</html>', $output);
    }

    /**
     * Capture output from a callable.
     */
    private function captureOutput(callable $callback): string
    {
        ob_start();
        $callback();
        return ob_get_clean() ?: '';
    }
}
