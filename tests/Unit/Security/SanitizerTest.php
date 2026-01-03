<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;
use VCL\Security\Sanitizer;

class SanitizerTest extends TestCase
{
    protected function setUp(): void
    {
        Sanitizer::resetInstance();
    }

    // =========================================================================
    // STRICT SANITIZATION TESTS
    // =========================================================================

    public function testSanitizeRemovesScriptTags(): void
    {
        $input = '<p>Hello</p><script>alert("XSS")</script>';
        $result = Sanitizer::sanitize($input);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringNotContainsString('alert', $result);
    }

    public function testSanitizeRemovesMostTags(): void
    {
        $input = '<div><p>Text</p><span>More</span></div>';
        $result = Sanitizer::sanitize($input);

        $this->assertStringNotContainsString('<div>', $result);
        $this->assertStringNotContainsString('<p>', $result);
        $this->assertStringNotContainsString('<span>', $result);
    }

    public function testSanitizeKeepsLineBreaks(): void
    {
        $input = 'Line 1<br>Line 2';
        $result = Sanitizer::sanitize($input);

        $this->assertStringContainsString('<br', $result);
    }

    // =========================================================================
    // RICH TEXT SANITIZATION TESTS
    // =========================================================================

    public function testSanitizeRichTextAllowsBasicFormatting(): void
    {
        $input = '<p>Hello <strong>World</strong> and <em>emphasis</em></p>';
        $result = Sanitizer::sanitizeRichText($input);

        $this->assertStringContainsString('<p>', $result);
        $this->assertStringContainsString('<strong>', $result);
        $this->assertStringContainsString('<em>', $result);
    }

    public function testSanitizeRichTextAllowsLinks(): void
    {
        $input = '<a href="https://example.com">Link</a>';
        $result = Sanitizer::sanitizeRichText($input);

        $this->assertStringContainsString('<a', $result);
        $this->assertStringContainsString('href=', $result);
    }

    public function testSanitizeRichTextForcesNoopenerOnLinks(): void
    {
        $input = '<a href="https://example.com">Link</a>';
        $result = Sanitizer::sanitizeRichText($input);

        $this->assertStringContainsString('rel="noopener noreferrer"', $result);
    }

    public function testSanitizeRichTextRemovesScripts(): void
    {
        $input = '<p>Text</p><script>evil()</script>';
        $result = Sanitizer::sanitizeRichText($input);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringNotContainsString('evil', $result);
    }

    public function testSanitizeRichTextAllowsLists(): void
    {
        $input = '<ul><li>Item 1</li><li>Item 2</li></ul>';
        $result = Sanitizer::sanitizeRichText($input);

        $this->assertStringContainsString('<ul>', $result);
        $this->assertStringContainsString('<li>', $result);
    }

    // =========================================================================
    // FULL SANITIZATION TESTS
    // =========================================================================

    public function testSanitizeFullAllowsHeaders(): void
    {
        $input = '<h1>Title</h1><h2>Subtitle</h2>';
        $result = Sanitizer::sanitizeFull($input);

        $this->assertStringContainsString('<h1>', $result);
        $this->assertStringContainsString('<h2>', $result);
    }

    public function testSanitizeFullAllowsTables(): void
    {
        $input = '<table><tr><td>Cell</td></tr></table>';
        $result = Sanitizer::sanitizeFull($input);

        $this->assertStringContainsString('<table>', $result);
        $this->assertStringContainsString('<tr>', $result);
        $this->assertStringContainsString('<td>', $result);
    }

    public function testSanitizeFullAllowsImages(): void
    {
        $input = '<img src="https://example.com/image.jpg" alt="Image">';
        $result = Sanitizer::sanitizeFull($input);

        $this->assertStringContainsString('<img', $result);
        $this->assertStringContainsString('src=', $result);
        $this->assertStringContainsString('alt=', $result);
    }

    public function testSanitizeFullStillRemovesScripts(): void
    {
        $input = '<div>Content<script>alert(1)</script></div>';
        $result = Sanitizer::sanitizeFull($input);

        $this->assertStringNotContainsString('<script>', $result);
    }

    // =========================================================================
    // STRIP TAGS TESTS
    // =========================================================================

    public function testStripRemovesAllTags(): void
    {
        $input = '<p>Hello <b>World</b></p>';
        $result = Sanitizer::strip($input);

        $this->assertStringNotContainsString('<p>', $result);
        $this->assertStringNotContainsString('<b>', $result);
        // Note: The strict sanitizer may remove all content
        // This test verifies no HTML tags remain
    }

    // =========================================================================
    // XSS PREVENTION TESTS
    // =========================================================================

    public function testRemovesOnEventHandlers(): void
    {
        $input = '<div onclick="alert(1)">Click me</div>';
        $result = Sanitizer::sanitizeFull($input);

        $this->assertStringNotContainsString('onclick', $result);
    }

    public function testRemovesJavascriptUrls(): void
    {
        $input = '<a href="javascript:alert(1)">Link</a>';
        $result = Sanitizer::sanitizeRichText($input);

        $this->assertStringNotContainsString('javascript:', $result);
    }

    public function testRemovesDataUrls(): void
    {
        $input = '<img src="data:text/html,<script>alert(1)</script>">';
        $result = Sanitizer::sanitizeFull($input);

        $this->assertStringNotContainsString('data:', $result);
    }

    // =========================================================================
    // INSTANCE MANAGEMENT TESTS
    // =========================================================================

    public function testInstanceMethodsWork(): void
    {
        $sanitizer = new Sanitizer();

        $input = '<script>evil()</script><p>Good</p>';
        $result = $sanitizer->clean($input);

        $this->assertStringNotContainsString('<script>', $result);
    }

    public function testSetInstanceAllowsCustomInstance(): void
    {
        $mock = $this->createMock(Sanitizer::class);
        $mock->method('clean')->willReturn('mocked');

        Sanitizer::setInstance($mock);

        $this->assertSame('mocked', Sanitizer::sanitize('test'));

        Sanitizer::resetInstance();
    }
}
