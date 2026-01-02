<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use VCL\Core\InputParam;
use VCL\Core\InputSource;

class InputParamTest extends TestCase
{
    protected function setUp(): void
    {
        // Set up test values in superglobals
        $_GET['test_string'] = 'test_value';
        $_GET['test_int'] = '42';
        $_GET['test_float'] = '3.14';
        $_GET['test_bool_true'] = '1';
        $_GET['test_bool_false'] = '0';
        $_POST['post_value'] = 'from_post';
    }

    protected function tearDown(): void
    {
        // Clean up superglobals
        unset($_GET['test_string']);
        unset($_GET['test_int']);
        unset($_GET['test_float']);
        unset($_GET['test_bool_true']);
        unset($_GET['test_bool_false']);
        unset($_POST['post_value']);
    }

    public function testAsStringReturnsString(): void
    {
        $param = new InputParam('test_string', InputSource::GET);
        // Note: asString() returns HTML-escaped string
        $this->assertSame('test_value', $param->asString());
    }

    public function testAsIntegerReturnsInteger(): void
    {
        $param = new InputParam('test_int', InputSource::GET);
        $this->assertSame(42, $param->asInteger());
    }

    public function testAsFloatReturnsFloat(): void
    {
        $param = new InputParam('test_float', InputSource::GET);
        $this->assertSame(3.14, $param->asFloat());
    }

    public function testAsBooleanReturnsTrueForTrueValues(): void
    {
        $param = new InputParam('test_bool_true', InputSource::GET);
        $this->assertTrue($param->asBoolean());
    }

    public function testAsBooleanReturnsFalseForFalseValues(): void
    {
        $param = new InputParam('test_bool_false', InputSource::GET);
        $this->assertFalse($param->asBoolean());
    }

    public function testExistsReturnsTrueWhenValueExists(): void
    {
        $param = new InputParam('test_string', InputSource::GET);
        $this->assertTrue($param->exists());
    }

    public function testExistsReturnsFalseWhenValueMissing(): void
    {
        $param = new InputParam('nonexistent', InputSource::GET);
        $this->assertFalse($param->exists());
    }

    public function testPostSource(): void
    {
        $param = new InputParam('post_value', InputSource::POST);
        $this->assertSame('from_post', $param->asString());
    }
}
