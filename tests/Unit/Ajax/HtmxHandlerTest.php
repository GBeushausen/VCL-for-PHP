<?php
/**
 * VCL for PHP 3.0
 *
 * Unit tests for HtmxHandler class
 */

declare(strict_types=1);

namespace VCL\Tests\Unit\Ajax;

use PHPUnit\Framework\TestCase;
use VCL\Ajax\HtmxHandler;
use VCL\Forms\Page;

class HtmxHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset server variables
        unset($_SERVER['HTTP_HX_REQUEST']);
        unset($_SERVER['HTTP_HX_BOOSTED']);
        unset($_SERVER['HTTP_HX_TRIGGER']);
        unset($_SERVER['HTTP_HX_TRIGGER_NAME']);
        unset($_SERVER['HTTP_HX_TARGET']);
        unset($_SERVER['HTTP_HX_CURRENT_URL']);
        $_POST = [];
    }

    protected function tearDown(): void
    {
        $this->setUp();
    }

    public function testIsHtmxRequestReturnsFalseByDefault(): void
    {
        $this->assertFalse(HtmxHandler::isHtmxRequest());
    }

    public function testIsHtmxRequestReturnsTrueWhenHeaderSet(): void
    {
        $_SERVER['HTTP_HX_REQUEST'] = 'true';
        $this->assertTrue(HtmxHandler::isHtmxRequest());
    }

    public function testIsHtmxRequestReturnsFalseWhenHeaderNotTrue(): void
    {
        $_SERVER['HTTP_HX_REQUEST'] = 'false';
        $this->assertFalse(HtmxHandler::isHtmxRequest());
    }

    public function testIsBoostedRequestReturnsFalseByDefault(): void
    {
        $this->assertFalse(HtmxHandler::isBoostedRequest());
    }

    public function testIsBoostedRequestReturnsTrueWhenHeaderSet(): void
    {
        $_SERVER['HTTP_HX_BOOSTED'] = 'true';
        $this->assertTrue(HtmxHandler::isBoostedRequest());
    }

    public function testGetTriggerReturnsNullByDefault(): void
    {
        $this->assertNull(HtmxHandler::getTrigger());
    }

    public function testGetTriggerReturnsHeaderValue(): void
    {
        $_SERVER['HTTP_HX_TRIGGER'] = 'button1';
        $this->assertSame('button1', HtmxHandler::getTrigger());
    }

    public function testGetTriggerNameReturnsNullByDefault(): void
    {
        $this->assertNull(HtmxHandler::getTriggerName());
    }

    public function testGetTriggerNameReturnsHeaderValue(): void
    {
        $_SERVER['HTTP_HX_TRIGGER_NAME'] = 'submit-btn';
        $this->assertSame('submit-btn', HtmxHandler::getTriggerName());
    }

    public function testGetTargetReturnsNullByDefault(): void
    {
        $this->assertNull(HtmxHandler::getTarget());
    }

    public function testGetTargetReturnsHeaderValue(): void
    {
        $_SERVER['HTTP_HX_TARGET'] = '#result-div';
        $this->assertSame('#result-div', HtmxHandler::getTarget());
    }

    public function testGetCurrentUrlReturnsNullByDefault(): void
    {
        $this->assertNull(HtmxHandler::getCurrentUrl());
    }

    public function testGetCurrentUrlReturnsHeaderValue(): void
    {
        $_SERVER['HTTP_HX_CURRENT_URL'] = 'https://example.com/page';
        $this->assertSame('https://example.com/page', HtmxHandler::getCurrentUrl());
    }

    public function testConstructorWithOwner(): void
    {
        $page = new Page();
        $page->Name = 'TestPage';
        $handler = new HtmxHandler($page);

        // Handler should be created without errors
        $this->assertInstanceOf(HtmxHandler::class, $handler);
    }

    public function testConstructorWithoutOwner(): void
    {
        $handler = new HtmxHandler();
        $this->assertInstanceOf(HtmxHandler::class, $handler);
    }

    public function testSetDebug(): void
    {
        $handler = new HtmxHandler();
        $handler->setDebug(true);
        // No error should occur - handler is still valid
        $this->assertInstanceOf(HtmxHandler::class, $handler);
    }

    public function testProcessRequestReturnsFalseForNonHtmxRequest(): void
    {
        $handler = new HtmxHandler();
        $this->assertFalse($handler->processRequest());
    }

    public function testProcessRequestReturnsFalseWithoutControlName(): void
    {
        $_SERVER['HTTP_HX_REQUEST'] = 'true';
        $_POST['_vcl_event'] = 'onclick';
        // Missing _vcl_control

        $handler = new HtmxHandler();
        $this->assertFalse($handler->processRequest());
    }

    public function testProcessRequestReturnsFalseWithoutEventName(): void
    {
        $_SERVER['HTTP_HX_REQUEST'] = 'true';
        $_POST['_vcl_control'] = 'Button1';
        // Missing _vcl_event

        $handler = new HtmxHandler();
        $this->assertFalse($handler->processRequest());
    }

    public function testUpdateElementGeneratesCorrectHtml(): void
    {
        $html = HtmxHandler::updateElement('myDiv', '<p>Content</p>');
        $this->assertStringContainsString('id="myDiv"', $html);
        $this->assertStringContainsString('hx-swap-oob="innerHTML"', $html);
        $this->assertStringContainsString('<p>Content</p>', $html);
    }

    public function testReplaceElementGeneratesCorrectHtml(): void
    {
        $html = HtmxHandler::replaceElement('myDiv', '<p>New Content</p>');
        $this->assertStringContainsString('id="myDiv"', $html);
        $this->assertStringContainsString('hx-swap-oob="outerHTML"', $html);
        $this->assertStringContainsString('<p>New Content</p>', $html);
    }

    public function testAppendToElementGeneratesCorrectHtml(): void
    {
        $html = HtmxHandler::appendToElement('myDiv', '<p>Appended</p>');
        $this->assertStringContainsString('hx-swap-oob="beforeend:#myDiv"', $html);
        $this->assertStringContainsString('<p>Appended</p>', $html);
    }

    public function testPrependToElementGeneratesCorrectHtml(): void
    {
        $html = HtmxHandler::prependToElement('myDiv', '<p>Prepended</p>');
        $this->assertStringContainsString('hx-swap-oob="afterbegin:#myDiv"', $html);
        $this->assertStringContainsString('<p>Prepended</p>', $html);
    }

    public function testRemoveElementGeneratesCorrectHtml(): void
    {
        $html = HtmxHandler::removeElement('myDiv');
        $this->assertStringContainsString('id="myDiv"', $html);
        $this->assertStringContainsString('hx-swap-oob="delete"', $html);
    }

    public function testExecuteScriptGeneratesScriptTag(): void
    {
        $html = HtmxHandler::executeScript('console.log("test")');
        $this->assertSame('<script>console.log("test")</script>', $html);
    }

    public function testAlertGeneratesAlertScript(): void
    {
        $html = HtmxHandler::alert('Hello World');
        $this->assertStringContainsString('<script>', $html);
        $this->assertStringContainsString('alert(', $html);
        $this->assertStringContainsString('Hello World', $html);
    }

    public function testFocusGeneratesFocusScript(): void
    {
        $html = HtmxHandler::focus('myInput');
        $this->assertStringContainsString('<script>', $html);
        $this->assertStringContainsString('focus()', $html);
        $this->assertStringContainsString('myInput', $html);
    }

    public function testSetValueGeneratesValueScript(): void
    {
        $html = HtmxHandler::setValue('myInput', 'newValue');
        $this->assertStringContainsString('<script>', $html);
        $this->assertStringContainsString('myInput', $html);
        $this->assertStringContainsString('newValue', $html);
    }

    public function testUpdateElementEscapesHtmlInId(): void
    {
        $html = HtmxHandler::updateElement('<script>bad</script>', 'content');
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>bad</script>"', $html);
    }
}
