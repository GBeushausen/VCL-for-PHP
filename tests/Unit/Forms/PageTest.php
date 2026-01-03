<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Forms;

use PHPUnit\Framework\TestCase;
use VCL\Forms\Page;
use VCL\Forms\Enums\DocType;
use VCL\Forms\Enums\Directionality;

class PageTest extends TestCase
{
    private Page $page;

    protected function setUp(): void
    {
        $this->page = new Page();
        $this->page->Name = 'TestPage';
    }

    public function testDefaultShowHeader(): void
    {
        $this->assertTrue($this->page->ShowHeader);
    }

    public function testShowHeaderProperty(): void
    {
        $this->page->ShowHeader = false;
        $this->assertFalse($this->page->ShowHeader);
    }

    public function testDefaultShowFooter(): void
    {
        $this->assertTrue($this->page->ShowFooter);
    }

    public function testShowFooterProperty(): void
    {
        $this->page->ShowFooter = false;
        $this->assertFalse($this->page->ShowFooter);
    }

    public function testDefaultIsForm(): void
    {
        $this->assertTrue($this->page->IsForm);
    }

    public function testIsFormProperty(): void
    {
        $this->page->IsForm = false;
        $this->assertFalse($this->page->IsForm);
    }

    public function testDefaultDocType(): void
    {
        $this->assertEquals(DocType::HTML5, $this->page->DocType);
    }

    public function testDocTypeProperty(): void
    {
        $this->page->DocType = DocType::XHTML_1_0_Strict;
        $this->assertEquals(DocType::XHTML_1_0_Strict, $this->page->DocType);
    }

    public function testDefaultEncoding(): void
    {
        $this->assertSame('UTF-8|utf-8', $this->page->Encoding);
    }

    public function testEncodingProperty(): void
    {
        $this->page->Encoding = 'ISO-8859-1|iso-8859-1';
        $this->assertSame('ISO-8859-1|iso-8859-1', $this->page->Encoding);
    }

    public function testGetCharset(): void
    {
        $this->assertSame('utf-8', $this->page->getCharset());
    }

    public function testDefaultDirectionality(): void
    {
        $this->assertEquals(Directionality::LeftToRight, $this->page->Directionality);
    }

    public function testDirectionalityProperty(): void
    {
        $this->page->Directionality = Directionality::RightToLeft;
        $this->assertEquals(Directionality::RightToLeft, $this->page->Directionality);
    }

    public function testMarginProperties(): void
    {
        $this->page->LeftMargin = 10;
        $this->page->TopMargin = 20;
        $this->page->RightMargin = 10;
        $this->page->BottomMargin = 20;

        $this->assertSame(10, $this->page->LeftMargin);
        $this->assertSame(20, $this->page->TopMargin);
        $this->assertSame(10, $this->page->RightMargin);
        $this->assertSame(20, $this->page->BottomMargin);
    }

    public function testBackgroundProperty(): void
    {
        $this->page->Background = 'images/bg.jpg';
        $this->assertSame('images/bg.jpg', $this->page->Background);
    }

    public function testIconProperty(): void
    {
        $this->page->Icon = 'favicon.ico';
        $this->assertSame('favicon.ico', $this->page->Icon);
    }

    public function testLanguageProperty(): void
    {
        $this->page->Language = 'German';
        $this->assertSame('German', $this->page->Language);
    }

    public function testGetLanguageCodeDefault(): void
    {
        $this->assertSame('de', $this->page->getLanguageCode());
    }

    public function testActionProperty(): void
    {
        $this->page->Action = 'submit.php';
        $this->assertSame('submit.php', $this->page->Action);
    }

    public function testJsOnLoadEvent(): void
    {
        $this->page->jsOnLoad = 'pageLoad';
        $this->assertSame('pageLoad', $this->page->jsOnLoad);
    }

    public function testJsOnUnloadEvent(): void
    {
        $this->page->jsOnUnload = 'pageUnload';
        $this->assertSame('pageUnload', $this->page->jsOnUnload);
    }

    public function testReadStartForm(): void
    {
        $this->page->Name = 'TestForm';
        $formStart = $this->page->readStartForm();

        $this->assertStringContainsString('<form', $formStart);
        $this->assertStringContainsString('TestForm_form', $formStart);
        $this->assertStringContainsString('method="post"', $formStart);
    }

    public function testReadEndForm(): void
    {
        $this->assertSame('</form>', $this->page->readEndForm());
    }

    public function testReadStartFormEmptyWhenNotForm(): void
    {
        $this->page->IsForm = false;
        $this->assertSame('', $this->page->readStartForm());
    }

    public function testIsCustomPage(): void
    {
        $this->assertInstanceOf(\VCL\Forms\CustomPage::class, $this->page);
    }
}
