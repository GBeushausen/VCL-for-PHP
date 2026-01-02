<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\RTL;

use PHPUnit\Framework\TestCase;
use VCL\RTL\Helpers;

class HelpersTest extends TestCase
{
    public function testBoolToStrTrue(): void
    {
        $this->assertSame('true', Helpers::boolToStr(true));
    }

    public function testBoolToStrFalse(): void
    {
        $this->assertSame('false', Helpers::boolToStr(false));
    }

    public function testTextToHtml(): void
    {
        $text = "Line 1\nLine 2";
        $result = Helpers::textToHtml($text);
        $this->assertStringContainsString('<br', $result);
    }

    public function testTextToHtmlEscapesSpecialChars(): void
    {
        $text = '<script>alert("test")</script>';
        $result = Helpers::textToHtml($text);
        $this->assertStringNotContainsString('<script>', $result);
    }

    public function testHtmlToText(): void
    {
        $html = "Line 1<br>Line 2";
        $result = Helpers::htmlToText($html);
        $this->assertStringContainsString("\r\n", $result);
    }

    public function testHtmlToTextDecodesEntities(): void
    {
        $html = '&lt;div&gt;';
        $result = Helpers::htmlToText($html);
        $this->assertSame('<div>', $result);
    }

    public function testAssignedWithNull(): void
    {
        $this->assertFalse(Helpers::assigned(null));
    }

    public function testAssignedWithValue(): void
    {
        $this->assertTrue(Helpers::assigned('test'));
        $this->assertTrue(Helpers::assigned(0));
        $this->assertTrue(Helpers::assigned(false));
        $this->assertTrue(Helpers::assigned(''));
    }

    public function testExtractJScript(): void
    {
        $html = '<html><script>var x = 1;</script><body>Content</body></html>';
        [$js, $htmlOnly] = Helpers::extractJScript($html);

        $this->assertStringContainsString('var x = 1;', $js);
        $this->assertStringNotContainsString('<script>', $htmlOnly);
    }

    public function testGenerateGUID(): void
    {
        $guid1 = Helpers::generateGUID();
        $guid2 = Helpers::generateGUID();

        // Check format
        $this->assertMatchesRegularExpression('/^\{[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\}$/i', $guid1);

        // Check uniqueness
        $this->assertNotSame($guid1, $guid2);
    }

    public function testEscapeJS(): void
    {
        $str = "It's a \"test\"\nNew line";
        $result = Helpers::escapeJS($str);

        $this->assertStringContainsString("\\'", $result);
        $this->assertStringContainsString('\\"', $result);
        $this->assertStringContainsString('\n', $result);
    }

    public function testColorToHexKnownColor(): void
    {
        $this->assertSame('#FF0000', Helpers::colorToHex('red'));
        $this->assertSame('#0000FF', Helpers::colorToHex('blue'));
        $this->assertSame('#00FF00', Helpers::colorToHex('green'));
    }

    public function testColorToHexCaseInsensitive(): void
    {
        $this->assertSame('#FF0000', Helpers::colorToHex('RED'));
        $this->assertSame('#FF0000', Helpers::colorToHex('Red'));
    }

    public function testColorToHexUnknownColorReturnsOriginal(): void
    {
        $this->assertSame('#ABCDEF', Helpers::colorToHex('#ABCDEF'));
    }

    public function testSafeUnserialize(): void
    {
        $data = serialize(['key' => 'value']);
        $result = Helpers::safeUnserialize($data);

        $this->assertIsArray($result);
        $this->assertSame('value', $result['key']);
    }
}
