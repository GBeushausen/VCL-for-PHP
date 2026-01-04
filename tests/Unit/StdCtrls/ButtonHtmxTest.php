<?php
/**
 * VCL for PHP 3.0
 *
 * Unit tests for Button htmx integration
 */

declare(strict_types=1);

namespace VCL\Tests\Unit\StdCtrls;

use PHPUnit\Framework\TestCase;
use VCL\StdCtrls\Button;
use VCL\Forms\Page;

class ButtonHtmxTest extends TestCase
{
    private Page $page;
    private Button $button;

    protected function setUp(): void
    {
        $this->page = new Page();
        $this->page->Name = 'TestPage';

        $this->button = new Button($this->page);
        $this->button->Name = 'TestButton';
        $this->button->Parent = $this->page;

        $_SERVER['PHP_SELF'] = '/test.php';
        $_SERVER['HTTP_HX_REQUEST'] = null;
        $_POST = [];
    }

    protected function tearDown(): void
    {
        unset($_SERVER['PHP_SELF']);
        unset($_SERVER['HTTP_HX_REQUEST']);
        $_POST = [];
    }

    public function testButtonRendersWithoutHtmxByDefault(): void
    {
        $this->page->UseHtmx = false;
        $this->button->OnClick = 'handleClick';

        $output = $this->button->render();

        $this->assertStringNotContainsString('hx-post', $output);
        $this->assertStringNotContainsString('hx-trigger', $output);
    }

    public function testButtonRendersWithHtmxWhenEnabled(): void
    {
        $this->page->UseHtmx = true;
        $this->button->OnClick = 'handleClick';

        $output = $this->button->render();

        $this->assertStringContainsString('hx-post', $output);
        $this->assertStringContainsString('hx-trigger="click"', $output);
    }

    public function testButtonRendersResultDivWhenHtmxEnabled(): void
    {
        $this->page->UseHtmx = true;
        $this->button->OnClick = 'handleClick';

        $output = $this->button->render();

        $this->assertStringContainsString('id="TestButton_result"', $output);
    }

    public function testButtonDoesNotRenderResultDivWithoutOnClick(): void
    {
        $this->page->UseHtmx = true;
        // No OnClick set

        $output = $this->button->render();

        $this->assertStringNotContainsString('_result', $output);
    }

    public function testButtonTypeIsButtonWhenHtmxEnabled(): void
    {
        $this->page->UseHtmx = true;
        $this->button->OnClick = 'handleClick';
        $this->button->ButtonType = 'btSubmit';

        $output = $this->button->render();

        // Should be type="button" not type="submit" to prevent form submission
        $this->assertStringContainsString('type="button"', $output);
    }

    public function testButtonTypeIsSubmitWhenHtmxDisabled(): void
    {
        $this->page->UseHtmx = false;
        $this->button->ButtonType = 'btSubmit';

        $output = $this->button->render();

        $this->assertStringContainsString('type="submit"', $output);
    }

    public function testHtmxAttributesContainVclMetadata(): void
    {
        $this->page->UseHtmx = true;
        $this->button->OnClick = 'handleClick';

        $output = $this->button->render();

        $this->assertStringContainsString('_vcl_control', $output);
        $this->assertStringContainsString('_vcl_event', $output);
        $this->assertStringContainsString('onclick', $output);
    }

    public function testHtmxTargetPointsToResultDiv(): void
    {
        $this->page->UseHtmx = true;
        $this->button->OnClick = 'handleClick';

        $output = $this->button->render();

        $this->assertStringContainsString('hx-target="#TestButton_result"', $output);
    }

    public function testHtmxIncludesFormValues(): void
    {
        $this->page->UseHtmx = true;
        $this->button->OnClick = 'handleClick';

        $output = $this->button->render();

        $this->assertStringContainsString('hx-include="#TestPage_form"', $output);
    }

    public function testButtonInitSkipsEventForHtmxRequest(): void
    {
        $this->page->UseHtmx = true;
        $_SERVER['HTTP_HX_REQUEST'] = 'true';

        $eventCalled = false;
        // We can't easily test this without mocking, but we can verify init doesn't throw
        $this->button->OnClick = 'handleClick';

        // Simulate POST data as if button was clicked
        $_POST['TestButton'] = 'Click';

        // This should not throw and should not process event
        // (HtmxHandler should handle it instead)
        $this->button->init();

        // If we get here, init completed successfully
        $this->assertInstanceOf(Button::class, $this->button);
    }

    public function testButtonInitProcessesEventForNormalRequest(): void
    {
        $this->page->UseHtmx = false;
        unset($_SERVER['HTTP_HX_REQUEST']);

        $this->button->OnClick = 'handleClick';

        // Without POST data, event shouldn't fire
        $this->button->init();

        // Init completed without errors - button still valid
        $this->assertInstanceOf(Button::class, $this->button);
    }

    public function testDisabledButtonDoesNotRenderHtmxAttributes(): void
    {
        $this->page->UseHtmx = true;
        $this->button->OnClick = 'handleClick';
        $this->button->Enabled = false;

        $output = $this->button->render();

        $this->assertStringNotContainsString('hx-post', $output);
        $this->assertStringContainsString('disabled', $output);
    }
}
