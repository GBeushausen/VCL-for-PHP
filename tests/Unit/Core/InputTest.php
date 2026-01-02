<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use VCL\Core\Input;
use VCL\Core\InputParam;

class InputTest extends TestCase
{
    protected function setUp(): void
    {
        // Clear superglobals for testing
        $_GET = [];
        $_POST = [];
        $_REQUEST = [];
    }

    public function testGetReturnsInputParamForGetVariable(): void
    {
        $_GET['test'] = 'value';

        $input = new Input();
        $param = $input->test;

        $this->assertInstanceOf(InputParam::class, $param);
    }

    public function testPostReturnsInputParamForPostVariable(): void
    {
        $_POST['test'] = 'value';

        $input = new Input();
        $param = $input->test;

        $this->assertInstanceOf(InputParam::class, $param);
    }
}
