<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;
use VCL\Security\InputValidator;
use VCL\Security\Exception\SecurityException;

class InputValidatorTest extends TestCase
{
    private InputValidator $validator;

    protected function setUp(): void
    {
        InputValidator::resetInstance();
        $this->validator = new InputValidator();
    }

    // =========================================================================
    // CONTROL NAME VALIDATION TESTS
    // =========================================================================

    public function testValidateControlNameAcceptsValidNames(): void
    {
        $this->assertSame('Button1', $this->validator->validateControlName('Button1'));
        $this->assertSame('_private', $this->validator->validateControlName('_private'));
        $this->assertSame('my_control', $this->validator->validateControlName('my_control'));
        $this->assertSame('Control-Name', $this->validator->validateControlName('Control-Name'));
    }

    public function testValidateControlNameRejectsEmpty(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateControlName('');
    }

    public function testValidateControlNameRejectsNull(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateControlName(null);
    }

    public function testValidateControlNameRejectsInvalidCharacters(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateControlName('Button<script>');
    }

    public function testValidateControlNameRejectsStartingWithNumber(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateControlName('1Button');
    }

    public function testIsValidControlNameReturnsBoolean(): void
    {
        $this->assertTrue($this->validator->isValidControlName('Button1'));
        $this->assertFalse($this->validator->isValidControlName(''));
        $this->assertFalse($this->validator->isValidControlName(null));
        $this->assertFalse($this->validator->isValidControlName('1invalid'));
    }

    // =========================================================================
    // EVENT NAME VALIDATION TESTS
    // =========================================================================

    public function testValidateEventNameAcceptsValidNames(): void
    {
        $this->assertSame('onClick', $this->validator->validateEventName('onClick'));
        $this->assertSame('onChange', $this->validator->validateEventName('onChange'));
        $this->assertSame('onSubmit', $this->validator->validateEventName('onSubmit'));
    }

    public function testValidateEventNameRejectsEmpty(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateEventName('');
    }

    public function testValidateEventNameRejectsInvalidCharacters(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateEventName('on-click');
    }

    public function testIsValidEventNameReturnsBoolean(): void
    {
        $this->assertTrue($this->validator->isValidEventName('onClick'));
        $this->assertFalse($this->validator->isValidEventName(''));
        $this->assertFalse($this->validator->isValidEventName('invalid-event'));
    }

    // =========================================================================
    // URL VALIDATION TESTS
    // =========================================================================

    public function testValidateUrlAcceptsSafeUrls(): void
    {
        $this->assertSame('https://example.com', $this->validator->validateUrl('https://example.com'));
        $this->assertSame('http://example.com', $this->validator->validateUrl('http://example.com'));
    }

    public function testValidateUrlRejectsJavascript(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateUrl('javascript:alert(1)');
    }

    public function testValidateUrlRejectsDataUrls(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateUrl('data:text/html,<script>');
    }

    public function testIsValidUrlReturnsBoolean(): void
    {
        $this->assertTrue($this->validator->isValidUrl('https://example.com'));
        $this->assertFalse($this->validator->isValidUrl('javascript:void(0)'));
        $this->assertFalse($this->validator->isValidUrl(''));
    }

    // =========================================================================
    // EMAIL VALIDATION TESTS
    // =========================================================================

    public function testValidateEmailAcceptsValid(): void
    {
        $this->assertSame('test@example.com', $this->validator->validateEmail('test@example.com'));
    }

    public function testValidateEmailRejectsInvalid(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateEmail('not-an-email');
    }

    public function testIsValidEmailReturnsBoolean(): void
    {
        $this->assertTrue($this->validator->isValidEmail('test@example.com'));
        $this->assertFalse($this->validator->isValidEmail('invalid'));
        $this->assertFalse($this->validator->isValidEmail(''));
    }

    // =========================================================================
    // INTEGER VALIDATION TESTS
    // =========================================================================

    public function testValidateIntegerAcceptsValid(): void
    {
        $this->assertSame(42, $this->validator->validateInteger(42));
        $this->assertSame(42, $this->validator->validateInteger('42'));
    }

    public function testValidateIntegerRespectsMinMax(): void
    {
        $this->assertSame(50, $this->validator->validateInteger(50, min: 0, max: 100));
    }

    public function testValidateIntegerRejectsBelowMin(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateInteger(-5, min: 0);
    }

    public function testValidateIntegerRejectsAboveMax(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateInteger(150, max: 100);
    }

    // =========================================================================
    // STRING VALIDATION TESTS
    // =========================================================================

    public function testValidateStringAcceptsValid(): void
    {
        $this->assertSame('hello', $this->validator->validateString('hello', minLength: 1, maxLength: 10));
    }

    public function testValidateStringRejectsTooShort(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateString('ab', minLength: 5);
    }

    public function testValidateStringRejectsTooLong(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validateString('this is a very long string', maxLength: 10);
    }

    // =========================================================================
    // PATTERN VALIDATION TESTS
    // =========================================================================

    public function testValidatePatternAcceptsMatching(): void
    {
        $this->assertSame('ABC123', $this->validator->validatePattern('ABC123', '/^[A-Z0-9]+$/'));
    }

    public function testValidatePatternRejectsNonMatching(): void
    {
        $this->expectException(SecurityException::class);
        $this->validator->validatePattern('abc', '/^[A-Z]+$/');
    }

    // =========================================================================
    // GENERIC VALIDATION TESTS
    // =========================================================================

    public function testValidateReturnsViolations(): void
    {
        $violations = $this->validator->validate('', [
            new \Symfony\Component\Validator\Constraints\NotBlank(),
        ]);

        $this->assertGreaterThan(0, $violations->count());
    }

    public function testIsValidReturnsBooleanForConstraints(): void
    {
        $this->assertTrue($this->validator->isValid('hello', [
            new \Symfony\Component\Validator\Constraints\NotBlank(),
        ]));

        $this->assertFalse($this->validator->isValid('', [
            new \Symfony\Component\Validator\Constraints\NotBlank(),
        ]));
    }

    public function testGetErrorsReturnsMessages(): void
    {
        $errors = $this->validator->getErrors('', [
            new \Symfony\Component\Validator\Constraints\NotBlank(),
        ]);

        $this->assertNotEmpty($errors);
        $this->assertIsString($errors[0]);
    }

    // =========================================================================
    // INSTANCE MANAGEMENT TESTS
    // =========================================================================

    public function testGetInstanceReturnsSingleton(): void
    {
        $instance1 = InputValidator::getInstance();
        $instance2 = InputValidator::getInstance();

        $this->assertSame($instance1, $instance2);
    }

    public function testSetInstanceAllowsCustomInstance(): void
    {
        $custom = new InputValidator();
        InputValidator::setInstance($custom);

        $this->assertSame($custom, InputValidator::getInstance());

        InputValidator::resetInstance();
    }
}
