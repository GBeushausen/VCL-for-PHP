<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Forms;

use PHPUnit\Framework\TestCase;
use VCL\Forms\DataModule;

class DataModuleTest extends TestCase
{
    private DataModule $dataModule;

    protected function setUp(): void
    {
        $this->dataModule = new DataModule();
        $this->dataModule->Name = 'TestDataModule';
    }

    public function testShowDoesNothing(): void
    {
        // DataModule.show() should not output anything
        ob_start();
        $this->dataModule->show();
        $output = ob_get_clean();
        $this->assertEmpty($output);
    }

    public function testExtendsCustomPage(): void
    {
        $this->assertInstanceOf(\VCL\Forms\CustomPage::class, $this->dataModule);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->dataModule);
    }
}
