<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;
use VCL\Security\Escaper;

class EscaperTest extends TestCase
{
    protected function setUp(): void
    {
        Escaper::resetInstance();
    }

    // =========================================================================
    // HTML ESCAPING TESTS
    // =========================================================================

    public function testHtmlEscapesSpecialCharacters(): void
    {
        $input = '<script>alert("XSS")</script>';
        $expected = '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;';

        $this->assertSame($expected, Escaper::html($input));
    }

    public function testHtmlEscapesSingleQuotes(): void
    {
        $input = "It's a test";
        $escaped = Escaper::html($input);
        // HTML5 uses &apos; instead of &#039;
        $this->assertTrue(
            str_contains($escaped, '&#039;') || str_contains($escaped, '&apos;'),
            "Expected single quote to be escaped, got: $escaped"
        );
    }

    public function testHtmlEscapesAmpersand(): void
    {
        $input = 'A & B';
        $this->assertSame('A &amp; B', Escaper::html($input));
    }

    // =========================================================================
    // ATTRIBUTE ESCAPING TESTS
    // =========================================================================

    public function testAttrEscapesForHtmlAttribute(): void
    {
        $input = 'onclick="alert(1)"';
        $escaped = Escaper::attr($input);

        $this->assertStringNotContainsString('"', $escaped);
    }

    // =========================================================================
    // JAVASCRIPT ESCAPING TESTS
    // =========================================================================

    public function testJsEscapesString(): void
    {
        $input = 'Hello "World"';
        $escaped = Escaper::js($input);

        $this->assertSame('"Hello \u0022World\u0022"', $escaped);
    }

    public function testJsEscapesArray(): void
    {
        $input = ['key' => '<script>'];
        $escaped = Escaper::js($input);

        $this->assertStringContainsString('\u003C', $escaped);
        $this->assertStringContainsString('\u003E', $escaped);
    }

    public function testJsEscapesInteger(): void
    {
        $this->assertSame('42', Escaper::js(42));
    }

    public function testJsEscapesBoolean(): void
    {
        $this->assertSame('true', Escaper::js(true));
        $this->assertSame('false', Escaper::js(false));
    }

    public function testJsStringRemovesQuotes(): void
    {
        $input = 'Hello';
        $escaped = Escaper::jsString($input);

        $this->assertSame('Hello', $escaped);
        $this->assertStringNotContainsString('"', $escaped);
    }

    // =========================================================================
    // CSS ESCAPING TESTS
    // =========================================================================

    public function testCssAllowsValidColors(): void
    {
        $this->assertSame('#fff', Escaper::css('#fff'));
        $this->assertSame('#aabbcc', Escaper::css('#aabbcc'));
        $this->assertSame('red', Escaper::css('red'));
        $this->assertSame('transparent', Escaper::css('transparent'));
    }

    public function testCssAllowsValidLengths(): void
    {
        $this->assertSame('10px', Escaper::css('10px'));
        $this->assertSame('1.5em', Escaper::css('1.5em'));
        $this->assertSame('100%', Escaper::css('100%'));
        $this->assertSame('auto', Escaper::css('auto'));
    }

    public function testCssBlocksDangerousValues(): void
    {
        $this->assertSame('', Escaper::css('expression(alert(1))'));
        $this->assertSame('', Escaper::css('url(javascript:alert(1))'));
        $this->assertSame('', Escaper::css('behavior:url(xss.htc)'));
    }

    public function testCssReturnsDefaultForInvalid(): void
    {
        $this->assertSame('fallback', Escaper::css('invalid-value', 'fallback'));
    }

    public function testCssColorValidation(): void
    {
        $this->assertSame('#ff0000', Escaper::cssColor('#ff0000'));
        $this->assertSame('rgba(255,0,0,0.5)', Escaper::cssColor('rgba(255,0,0,0.5)'));
        $this->assertSame('', Escaper::cssColor('not-a-color'));
    }

    // =========================================================================
    // URL ESCAPING TESTS
    // =========================================================================

    public function testUrlEncodesSpecialCharacters(): void
    {
        $input = 'hello world&foo=bar';
        $escaped = Escaper::url($input);

        $this->assertStringContainsString('%20', $escaped);
        $this->assertStringContainsString('%26', $escaped);
    }

    public function testUrlAttrAllowsSafeUrls(): void
    {
        $this->assertSame('https://example.com', Escaper::urlAttr('https://example.com'));
        $this->assertSame('/path/to/page', Escaper::urlAttr('/path/to/page'));
        $this->assertSame('./relative', Escaper::urlAttr('./relative'));
        $this->assertSame('#anchor', Escaper::urlAttr('#anchor'));
    }

    public function testUrlAttrBlocksDangerousSchemes(): void
    {
        $this->assertSame('#', Escaper::urlAttr('javascript:alert(1)'));
        $this->assertSame('#', Escaper::urlAttr('data:text/html,<script>'));
        $this->assertSame('#', Escaper::urlAttr('vbscript:msgbox'));
    }

    public function testUrlAttrReturnsDefaultForInvalid(): void
    {
        $this->assertSame('/safe', Escaper::urlAttr('javascript:void(0)', '/safe'));
    }

    // =========================================================================
    // ID ESCAPING TESTS
    // =========================================================================

    public function testIdRemovesInvalidCharacters(): void
    {
        $this->assertSame('valid_id', Escaper::id('valid_id'));
        $this->assertSame('test_123', Escaper::id('test 123'));
        // Hyphens are valid in HTML IDs
        $this->assertSame('my-id', Escaper::id('my-id'));
    }

    public function testIdPrefixesNumberStart(): void
    {
        $this->assertSame('_123', Escaper::id('123'));
    }

    // =========================================================================
    // INSTANCE MANAGEMENT TESTS
    // =========================================================================

    public function testSetInstanceAllowsCustomInstance(): void
    {
        $mock = $this->createMock(Escaper::class);
        $mock->method('escapeHtml')->willReturn('mocked');

        Escaper::setInstance($mock);

        $this->assertSame('mocked', Escaper::html('test'));

        Escaper::resetInstance();
    }

    public function testResetInstanceRestoresDefault(): void
    {
        $mock = $this->createMock(Escaper::class);
        $mock->method('escapeHtml')->willReturn('mocked');

        Escaper::setInstance($mock);
        Escaper::resetInstance();

        $this->assertSame('test', Escaper::html('test'));
    }

    // =========================================================================
    // INSTANCE METHOD TESTS
    // =========================================================================

    public function testInstanceMethodsWork(): void
    {
        $escaper = new Escaper();

        $this->assertSame('&lt;b&gt;', $escaper->escapeHtml('<b>'));
        $this->assertSame('"test"', $escaper->escapeJs('test'));
        $this->assertSame('hello%20world', $escaper->escapeUrl('hello world'));
    }
}
