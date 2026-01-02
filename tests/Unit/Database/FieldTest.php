<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Database;

use PHPUnit\Framework\TestCase;
use VCL\Database\Field;

class FieldTest extends TestCase
{
    private Field $field;

    protected function setUp(): void
    {
        $this->field = new Field();
    }

    public function testFieldNameProperty(): void
    {
        $this->field->FieldName = 'user_id';
        $this->assertSame('user_id', $this->field->FieldName);
    }

    public function testDefaultFieldNameIsEmpty(): void
    {
        $this->assertSame('', $this->field->FieldName);
    }

    public function testDisplayLabelProperty(): void
    {
        $this->field->DisplayLabel = 'User ID';
        $this->assertSame('User ID', $this->field->DisplayLabel);
    }

    public function testDefaultDisplayLabelIsEmpty(): void
    {
        $this->assertSame('', $this->field->DisplayLabel);
    }

    public function testGetDisplayNameReturnsDisplayLabel(): void
    {
        $this->field->FieldName = 'user_id';
        $this->field->DisplayLabel = 'User Identifier';
        $this->assertSame('User Identifier', $this->field->getDisplayName());
    }

    public function testGetDisplayNameFallsBackToFieldName(): void
    {
        $this->field->FieldName = 'user_id';
        $this->assertSame('user_id', $this->field->getDisplayName());
    }

    public function testIsVCLObject(): void
    {
        $this->assertInstanceOf(\VCL\Core\VCLObject::class, $this->field);
    }
}
