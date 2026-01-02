<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Database;

use PHPUnit\Framework\TestCase;
use VCL\Database\DataSet;
use VCL\Database\Enums\DatasetState;

class DataSetTest extends TestCase
{
    private DataSet $dataset;

    protected function setUp(): void
    {
        $this->dataset = new DataSet();
        $this->dataset->Name = 'TestDataSet';
    }

    public function testDefaultLimitStart(): void
    {
        $this->assertSame(0, $this->dataset->LimitStart);
    }

    public function testLimitStartProperty(): void
    {
        $this->dataset->LimitStart = 10;
        $this->assertSame(10, $this->dataset->LimitStart);
    }

    public function testLimitStartMinimumIsZero(): void
    {
        $this->dataset->LimitStart = -5;
        $this->assertSame(0, $this->dataset->LimitStart);
    }

    public function testDefaultLimitCount(): void
    {
        $this->assertSame(10, $this->dataset->LimitCount);
    }

    public function testLimitCountProperty(): void
    {
        $this->dataset->LimitCount = 50;
        $this->assertSame(50, $this->dataset->LimitCount);
    }

    public function testLimitCountMinimumIsOne(): void
    {
        $this->dataset->LimitCount = 0;
        $this->assertSame(1, $this->dataset->LimitCount);
    }

    public function testDefaultState(): void
    {
        $this->assertEquals(DatasetState::Inactive, $this->dataset->State);
    }

    public function testStateProperty(): void
    {
        $this->dataset->State = DatasetState::Browse;
        $this->assertEquals(DatasetState::Browse, $this->dataset->State);
    }

    public function testDefaultModified(): void
    {
        $this->assertFalse($this->dataset->Modified);
    }

    public function testModifiedProperty(): void
    {
        $this->dataset->Modified = true;
        $this->assertTrue($this->dataset->Modified);
    }

    public function testDefaultCanModify(): void
    {
        $this->assertTrue($this->dataset->CanModify);
    }

    public function testCanModifyProperty(): void
    {
        $this->dataset->CanModify = false;
        $this->assertFalse($this->dataset->CanModify);
    }

    public function testRecNoProperty(): void
    {
        $this->assertSame(0, $this->dataset->RecNo);
    }

    public function testMasterFieldsProperty(): void
    {
        $fields = ['id', 'name'];
        $this->dataset->MasterFields = $fields;
        $this->assertSame($fields, $this->dataset->MasterFields);
    }

    public function testRecKeyProperty(): void
    {
        $key = ['id' => 1];
        $this->dataset->RecKey = $key;
        $this->assertSame($key, $this->dataset->RecKey);
    }

    public function testFieldBuffer(): void
    {
        $this->assertIsArray($this->dataset->fieldbuffer);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->dataset);
    }
}
