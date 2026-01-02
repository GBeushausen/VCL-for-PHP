<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Database;

use PHPUnit\Framework\TestCase;
use VCL\Database\Datasource;

class DatasourceTest extends TestCase
{
    private Datasource $datasource;

    protected function setUp(): void
    {
        $this->datasource = new Datasource();
        $this->datasource->Name = 'TestDatasource';
    }

    public function testDefaultDataSetIsNull(): void
    {
        $this->assertNull($this->datasource->DataSet);
    }

    public function testDataSetProperty(): void
    {
        $mockDataSet = new \stdClass();
        $this->datasource->DataSet = $mockDataSet;
        $this->assertSame($mockDataSet, $this->datasource->DataSet);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->datasource);
    }
}
