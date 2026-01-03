<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use VCL\Security\Escaper;
use VCL\Security\Sanitizer;

/**
 * XSS Payload Test Suite.
 *
 * Tests the Escaper and Sanitizer against known XSS attack vectors.
 * Based on OWASP XSS Filter Evasion Cheat Sheet and common attack patterns.
 */
class XssPayloadTest extends TestCase
{
    protected function setUp(): void
    {
        Escaper::resetInstance();
    }

    // =========================================================================
    // HTML CONTEXT XSS PAYLOADS
    // =========================================================================

    #[DataProvider('htmlXssPayloads')]
    public function testEscaperBlocksHtmlXss(string $payload): void
    {
        $escaped = Escaper::html($payload);

        // The escaped output should not contain raw HTML tags
        // htmlspecialchars converts < to &lt; and > to &gt;
        $this->assertStringNotContainsString('<', $escaped, "Raw < found in escaped output");
        $this->assertStringNotContainsString('>', $escaped, "Raw > found in escaped output");

        // The output should be safe to embed in HTML - verify it's properly escaped
        $this->assertStringContainsString('&lt;', $escaped);
    }

    public static function htmlXssPayloads(): array
    {
        return [
            'basic script' => ['<script>alert("XSS")</script>'],
            'script with src' => ['<script src="http://evil.com/xss.js"></script>'],
            'img onerror' => ['<img src=x onerror=alert(1)>'],
            'svg onload' => ['<svg onload=alert(1)>'],
            'body onload' => ['<body onload=alert(1)>'],
            'div onclick' => ['<div onclick="alert(1)">click</div>'],
            'a href javascript' => ['<a href="javascript:alert(1)">click</a>'],
            'iframe' => ['<iframe src="javascript:alert(1)">'],
            'input onfocus' => ['<input onfocus=alert(1) autofocus>'],
            'marquee onstart' => ['<marquee onstart=alert(1)>'],
            'video onerror' => ['<video><source onerror="alert(1)">'],
            'math' => ['<math><maction actiontype="statusline#http://evil">CLICKME</maction></math>'],
            'object data' => ['<object data="javascript:alert(1)">'],
            'embed' => ['<embed src="javascript:alert(1)">'],
            'form action' => ['<form action="javascript:alert(1)"><input type=submit>'],
            'button formaction' => ['<button formaction="javascript:alert(1)">X</button>'],
            'isindex action' => ['<isindex action="javascript:alert(1)">'],
            'meta refresh' => ['<meta http-equiv="refresh" content="0;url=javascript:alert(1)">'],
            'table background' => ['<table background="javascript:alert(1)">'],
            'td background' => ['<td background="javascript:alert(1)">'],
        ];
    }

    // =========================================================================
    // ATTRIBUTE CONTEXT XSS PAYLOADS
    // =========================================================================

    #[DataProvider('attributeXssPayloads')]
    public function testEscaperBlocksAttributeXss(string $payload): void
    {
        $escaped = Escaper::attr($payload);

        // Should not be able to break out of attribute context
        // Double quotes are escaped to &quot;
        $this->assertStringNotContainsString('"', $escaped);

        // Single quotes are also escaped (to &apos; or &#039;)
        // The attacker cannot break out of a quoted attribute value
        if (str_contains($payload, "'")) {
            $this->assertTrue(
                str_contains($escaped, '&apos;') || str_contains($escaped, '&#039;'),
                "Single quotes should be escaped"
            );
        }
    }

    public static function attributeXssPayloads(): array
    {
        return [
            'break out double quote' => ['" onclick="alert(1)'],
            'break out single quote' => ["' onclick='alert(1)"],
            'break out with space' => ['" onclick=alert(1) x="'],
            'event handler injection' => ['" onmouseover="alert(1)" x="'],
            'style injection' => ['" style="background:url(javascript:alert(1))" x="'],
            'expression' => ['" style="width:expression(alert(1))" x="'],
        ];
    }

    // =========================================================================
    // JAVASCRIPT CONTEXT XSS PAYLOADS
    // =========================================================================

    #[DataProvider('jsXssPayloads')]
    public function testEscaperBlocksJsXss(string $payload): void
    {
        $escaped = Escaper::js($payload);

        // Should be valid JSON
        $decoded = json_decode($escaped);
        $this->assertNotNull($decoded, "Failed to decode: $escaped");

        // Should not contain script tags that could break out of script context
        $this->assertStringNotContainsString('</script', strtolower($escaped));
    }

    public static function jsXssPayloads(): array
    {
        return [
            'close script tag' => ['</script><script>alert(1)</script>'],
            'html entities' => ['</script><img src=x onerror=alert(1)>'],
            'unicode escape' => ['\u003cscript\u003ealert(1)\u003c/script\u003e'],
            'newline injection' => ["test\nalert(1)//"],
            'string break single' => ["'; alert(1); '"],
            'string break double' => ['"; alert(1); "'],
            'comment injection' => ['*/alert(1)/*'],
        ];
    }

    // =========================================================================
    // URL CONTEXT XSS PAYLOADS
    // =========================================================================

    #[DataProvider('urlXssPayloads')]
    public function testEscaperBlocksUrlXss(string $payload): void
    {
        $escaped = Escaper::urlAttr($payload);

        // Should return safe value (# or escaped URL)
        $lowercaseEscaped = strtolower($escaped);
        $this->assertStringNotContainsString('javascript:', $lowercaseEscaped);
        $this->assertStringNotContainsString('data:', $lowercaseEscaped);
        $this->assertStringNotContainsString('vbscript:', $lowercaseEscaped);
    }

    public static function urlXssPayloads(): array
    {
        return [
            'javascript protocol' => ['javascript:alert(1)'],
            'javascript with spaces' => ['   javascript:alert(1)'],
            'javascript uppercase' => ['JAVASCRIPT:alert(1)'],
            'javascript mixed case' => ['JaVaScRiPt:alert(1)'],
            'javascript with tab' => ["java\tscript:alert(1)"],
            'javascript with newline' => ["java\nscript:alert(1)"],
            'javascript with null' => ["java\0script:alert(1)"],
            'data uri html' => ['data:text/html,<script>alert(1)</script>'],
            'data uri base64' => ['data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg=='],
            'vbscript' => ['vbscript:msgbox(1)'],
            'livescript' => ['livescript:alert(1)'],
            'mocha' => ['mocha:alert(1)'],
        ];
    }

    // =========================================================================
    // CSS CONTEXT XSS PAYLOADS
    // =========================================================================

    #[DataProvider('cssXssPayloads')]
    public function testEscaperBlocksCssXss(string $payload): void
    {
        $escaped = Escaper::css($payload);

        // Should return empty or safe value
        $lowercaseEscaped = strtolower($escaped);
        $this->assertStringNotContainsString('expression(', $lowercaseEscaped);
        $this->assertStringNotContainsString('javascript:', $lowercaseEscaped);
        $this->assertStringNotContainsString('behavior:', $lowercaseEscaped);
        $this->assertStringNotContainsString('-moz-binding:', $lowercaseEscaped);
    }

    public static function cssXssPayloads(): array
    {
        return [
            'expression' => ['expression(alert(1))'],
            'expression with spaces' => ['expr/**/ession(alert(1))'],
            'url javascript' => ['url(javascript:alert(1))'],
            'url data' => ['url(data:text/html,<script>alert(1)</script>)'],
            'behavior' => ['behavior:url(xss.htc)'],
            'moz binding' => ['-moz-binding:url(xss.xml#xss)'],
            'import' => ['@import "http://evil.com/xss.css"'],
            'charset' => ['@charset "UTF-7"'],
        ];
    }

    // =========================================================================
    // CSS URL VALIDATION (safe URLs should pass)
    // =========================================================================

    #[DataProvider('safeCssUrls')]
    public function testEscaperAllowsSafeCssUrls(string $value): void
    {
        $escaped = Escaper::css($value);
        $this->assertSame($value, $escaped, "Safe CSS url() value should be allowed: $value");
    }

    public static function safeCssUrls(): array
    {
        return [
            'https url' => ['url(https://example.com/image.png)'],
            'http url' => ['url(http://example.com/image.png)'],
            'relative path' => ['url(/images/bg.png)'],
            'relative with dot' => ['url(./images/bg.png)'],
            'quoted https' => ['url("https://example.com/image.png")'],
        ];
    }

    // =========================================================================
    // SANITIZER XSS TESTS
    // =========================================================================

    #[DataProvider('htmlXssPayloads')]
    public function testSanitizerRemovesXss(string $payload): void
    {
        $sanitizer = new Sanitizer();
        $sanitized = $sanitizer->sanitize($payload);

        // Should not contain dangerous elements
        $this->assertStringNotContainsString('<script', strtolower($sanitized));
        $this->assertStringNotContainsString('javascript:', strtolower($sanitized));
        $this->assertStringNotContainsString('onerror', strtolower($sanitized));
        $this->assertStringNotContainsString('onclick', strtolower($sanitized));
    }

    public function testSanitizerRichTextRemovesScripts(): void
    {
        $sanitizer = new Sanitizer();

        $input = '<p>Hello</p><script>alert(1)</script><b>World</b>';
        $sanitized = $sanitizer->sanitizeRichText($input);

        $this->assertStringContainsString('<p>Hello</p>', $sanitized);
        $this->assertStringContainsString('<b>World</b>', $sanitized);
        $this->assertStringNotContainsString('<script', $sanitized);
    }

    public function testSanitizerRichTextRemovesEventHandlers(): void
    {
        $sanitizer = new Sanitizer();

        $input = '<p onclick="alert(1)">Click me</p>';
        $sanitized = $sanitizer->sanitizeRichText($input);

        $this->assertStringContainsString('<p>', $sanitized);
        $this->assertStringNotContainsString('onclick', $sanitized);
    }

    public function testSanitizerRichTextRemovesJavascriptUrls(): void
    {
        $sanitizer = new Sanitizer();

        $input = '<a href="javascript:alert(1)">Click</a>';
        $sanitized = $sanitizer->sanitizeRichText($input);

        $this->assertStringNotContainsString('javascript:', $sanitized);
    }

    // =========================================================================
    // ENCODING BYPASS ATTEMPTS
    // =========================================================================

    #[DataProvider('encodingBypassPayloads')]
    public function testEscaperHandlesEncodingBypasses(string $payload): void
    {
        $escaped = Escaper::html($payload);

        // Verify the output doesn't execute
        $this->assertStringNotContainsString('<script', strtolower($escaped));
    }

    public static function encodingBypassPayloads(): array
    {
        return [
            'html entity decimal' => ['&#60;script&#62;alert(1)&#60;/script&#62;'],
            'html entity hex' => ['&#x3c;script&#x3e;alert(1)&#x3c;/script&#x3e;'],
            'html entity named' => ['&lt;script&gt;alert(1)&lt;/script&gt;'],
            'double encoding' => ['%253Cscript%253Ealert(1)%253C/script%253E'],
            'utf7' => ['+ADw-script+AD4-alert(1)+ADw-/script+AD4-'],
        ];
    }

    // =========================================================================
    // EDGE CASES
    // =========================================================================

    public function testEscaperHandlesEmptyString(): void
    {
        $this->assertSame('', Escaper::html(''));
        $this->assertSame('', Escaper::attr(''));
        $this->assertSame('""', Escaper::js(''));
    }

    public function testEscaperHandlesNullBytes(): void
    {
        $payload = "test\0<script>";
        $escaped = Escaper::html($payload);

        $this->assertStringNotContainsString('<script', $escaped);
    }

    public function testEscaperHandlesUnicodeCharacters(): void
    {
        $payload = '<script>alert("中文")</script>';
        $escaped = Escaper::html($payload);

        $this->assertStringNotContainsString('<script', $escaped);
        // Unicode should be preserved in the content
        $this->assertStringContainsString('中文', $escaped);
    }

    public function testEscaperHandlesVeryLongStrings(): void
    {
        $payload = str_repeat('<script>alert(1)</script>', 1000);
        $escaped = Escaper::html($payload);

        $this->assertStringNotContainsString('<script', $escaped);
    }
}
