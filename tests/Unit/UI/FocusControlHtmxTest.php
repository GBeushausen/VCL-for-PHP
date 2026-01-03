<?php
/**
 * VCL for PHP 3.0
 *
 * Unit tests for FocusControl htmx methods
 */

declare(strict_types=1);

namespace VCL\Tests\Unit\UI;

use PHPUnit\Framework\TestCase;
use VCL\UI\FocusControl;
use VCL\Forms\Page;

/**
 * Test subclass to expose protected htmx methods
 */
class TestableFocusControl extends FocusControl
{
    public function testIsHtmxEnabled(): bool
    {
        return $this->isHtmxEnabled();
    }

    public function testGetHtmxAttributes(
        string $eventName,
        string $trigger,
        ?string $target = null,
        string $swap = 'innerHTML'
    ): string {
        return $this->getHtmxAttributes($eventName, $trigger, $target, $swap);
    }

    public function testGetHtmxClickAttributes(?string $target = null): string
    {
        return $this->getHtmxClickAttributes($target);
    }

    public function testGetHtmxChangeAttributes(?string $target = null): string
    {
        return $this->getHtmxChangeAttributes($target);
    }

    public function testGetHtmxSubmitAttributes(?string $target = null): string
    {
        return $this->getHtmxSubmitAttributes($target);
    }

    public function testGetHtmxKeyAttributes(
        string $eventName = 'onkeyup',
        int $delay = 300,
        ?string $target = null
    ): string {
        return $this->getHtmxKeyAttributes($eventName, $delay, $target);
    }

    public function testDumpHtmxResultDiv(): string
    {
        ob_start();
        $this->dumpHtmxResultDiv();
        return ob_get_clean();
    }
}

class FocusControlHtmxTest extends TestCase
{
    private TestableFocusControl $control;

    protected function setUp(): void
    {
        $this->control = new TestableFocusControl();
        $this->control->Name = 'TestControl';
        $_SERVER['PHP_SELF'] = '/test.php';
    }

    protected function tearDown(): void
    {
        unset($_SERVER['PHP_SELF']);
    }

    public function testIsHtmxEnabledReturnsFalseWithoutOwner(): void
    {
        $this->assertFalse($this->control->testIsHtmxEnabled());
    }

    public function testIsHtmxEnabledReturnsFalseWhenPageHasHtmxDisabled(): void
    {
        $page = new Page();
        $page->Name = 'TestPage';
        $page->UseHtmx = false;

        $control = new TestableFocusControl($page);
        $control->Name = 'TestControl';

        $this->assertFalse($control->testIsHtmxEnabled());
    }

    public function testIsHtmxEnabledReturnsTrueWhenPageHasHtmxEnabled(): void
    {
        $page = new Page();
        $page->Name = 'TestPage';
        $page->UseHtmx = true;

        $control = new TestableFocusControl($page);
        $control->Name = 'TestControl';

        $this->assertTrue($control->testIsHtmxEnabled());
    }

    public function testGetHtmxAttributesContainsHxPost(): void
    {
        $attrs = $this->control->testGetHtmxAttributes('onclick', 'click');
        $this->assertStringContainsString('hx-post="/test.php"', $attrs);
    }

    public function testGetHtmxAttributesContainsHxTrigger(): void
    {
        $attrs = $this->control->testGetHtmxAttributes('onclick', 'click');
        $this->assertStringContainsString('hx-trigger="click"', $attrs);
    }

    public function testGetHtmxAttributesContainsDefaultTarget(): void
    {
        $attrs = $this->control->testGetHtmxAttributes('onclick', 'click');
        $this->assertStringContainsString('hx-target="#TestControl_result"', $attrs);
    }

    public function testGetHtmxAttributesContainsCustomTarget(): void
    {
        $attrs = $this->control->testGetHtmxAttributes('onclick', 'click', '#customTarget');
        $this->assertStringContainsString('hx-target="#customTarget"', $attrs);
    }

    public function testGetHtmxAttributesContainsHxSwap(): void
    {
        $attrs = $this->control->testGetHtmxAttributes('onclick', 'click');
        $this->assertStringContainsString('hx-swap="innerHTML"', $attrs);
    }

    public function testGetHtmxAttributesContainsCustomSwap(): void
    {
        $attrs = $this->control->testGetHtmxAttributes('onclick', 'click', null, 'outerHTML');
        $this->assertStringContainsString('hx-swap="outerHTML"', $attrs);
    }

    public function testGetHtmxAttributesContainsHxVals(): void
    {
        $attrs = $this->control->testGetHtmxAttributes('onclick', 'click');
        $this->assertStringContainsString('hx-vals=', $attrs);
        $this->assertStringContainsString('_vcl_control', $attrs);
        $this->assertStringContainsString('_vcl_event', $attrs);
        $this->assertStringContainsString('onclick', $attrs);
    }

    public function testGetHtmxClickAttributesUsesClickTrigger(): void
    {
        $attrs = $this->control->testGetHtmxClickAttributes();
        $this->assertStringContainsString('hx-trigger="click"', $attrs);
    }

    public function testGetHtmxChangeAttributesUsesChangeTrigger(): void
    {
        $attrs = $this->control->testGetHtmxChangeAttributes();
        $this->assertStringContainsString('hx-trigger="change"', $attrs);
    }

    public function testGetHtmxSubmitAttributesUsesSubmitTrigger(): void
    {
        $attrs = $this->control->testGetHtmxSubmitAttributes();
        $this->assertStringContainsString('hx-trigger="submit"', $attrs);
    }

    public function testGetHtmxKeyAttributesContainsDebounce(): void
    {
        $attrs = $this->control->testGetHtmxKeyAttributes('onkeyup', 500);
        $this->assertStringContainsString('delay:500ms', $attrs);
    }

    public function testGetHtmxKeyAttributesContainsKeyupTrigger(): void
    {
        $attrs = $this->control->testGetHtmxKeyAttributes();
        $this->assertStringContainsString('keyup', $attrs);
    }

    public function testDumpHtmxResultDivOutputsNothingWhenHtmxDisabled(): void
    {
        $output = $this->control->testDumpHtmxResultDiv();
        $this->assertEmpty($output);
    }

    public function testDumpHtmxResultDivOutputsDivWhenHtmxEnabled(): void
    {
        $page = new Page();
        $page->Name = 'TestPage';
        $page->UseHtmx = true;

        $control = new TestableFocusControl($page);
        $control->Name = 'TestControl';

        $output = $control->testDumpHtmxResultDiv();
        $this->assertStringContainsString('<div id="TestControl_result"></div>', $output);
    }

    public function testHtmxAttributesEscapeSpecialCharacters(): void
    {
        $control = new TestableFocusControl();
        $control->Name = 'Test<Control>';
        $_SERVER['PHP_SELF'] = '/test.php?a=1&b=2';

        $attrs = $control->testGetHtmxAttributes('onclick', 'click');

        // Check that special characters are escaped
        $this->assertStringContainsString('&amp;', $attrs);
    }
}
