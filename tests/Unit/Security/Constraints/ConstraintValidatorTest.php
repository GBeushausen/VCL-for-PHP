<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Security\Constraints;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use VCL\Security\Constraints\ValidControlName;
use VCL\Security\Constraints\ValidEventName;
use VCL\Security\Constraints\SafeUrl;

/**
 * Tests for VCL Security Constraints.
 */
class ConstraintValidatorTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidator();
    }

    // =========================================================================
    // ValidControlName CONSTRAINT TESTS
    // =========================================================================

    #[DataProvider('validControlNames')]
    public function testValidControlNameAcceptsValid(string $name): void
    {
        $violations = $this->validator->validate($name, [new ValidControlName()]);

        $this->assertCount(0, $violations, "Expected '$name' to be valid, but got violations");
    }

    public static function validControlNames(): array
    {
        return [
            'simple' => ['Button1'],
            'underscore start' => ['_private'],
            'with underscore' => ['my_control'],
            'with hyphen' => ['Control-Name'],
            'camelCase' => ['myButton'],
            'uppercase' => ['BUTTON'],
            'mixed' => ['My_Control_123'],
            'single letter' => ['a'],
            'underscore only start' => ['_'],
        ];
    }

    #[DataProvider('invalidControlNames')]
    public function testValidControlNameRejectsInvalid(string $name): void
    {
        $violations = $this->validator->validate($name, [new ValidControlName()]);

        $this->assertGreaterThan(0, $violations->count(), "Expected '$name' to be invalid");
    }

    public static function invalidControlNames(): array
    {
        return [
            'starts with number' => ['1Button'],
            'starts with hyphen' => ['-Button'],
            'contains space' => ['My Button'],
            'contains dot' => ['my.button'],
            'contains special' => ['button<script>'],
            'contains quote' => ["button'"],
            'contains double quote' => ['button"'],
            'contains semicolon' => ['button;'],
            'contains equals' => ['button=1'],
            'contains slash' => ['button/test'],
            'contains backslash' => ['button\\test'],
            'contains parenthesis' => ['button()'],
            'contains bracket' => ['button[0]'],
            'contains at' => ['button@test'],
            'contains hash' => ['button#1'],
            'contains ampersand' => ['button&test'],
            'contains pipe' => ['button|test'],
        ];
    }

    public function testValidControlNameNullAndEmptyAllowed(): void
    {
        // Null and empty are handled by NotBlank if needed
        $violations = $this->validator->validate(null, [new ValidControlName()]);
        $this->assertCount(0, $violations);

        $violations = $this->validator->validate('', [new ValidControlName()]);
        $this->assertCount(0, $violations);
    }

    public function testValidControlNameStrictMode(): void
    {
        $constraint = new ValidControlName(
            allowedNames: ['Button1', 'Button2'],
            strict: true
        );

        // Valid and in whitelist
        $violations = $this->validator->validate('Button1', [$constraint]);
        $this->assertCount(0, $violations);

        // Valid pattern but not in whitelist
        $violations = $this->validator->validate('Button3', [$constraint]);
        $this->assertCount(1, $violations);
        $this->assertStringContainsString('not in the list', $violations[0]->getMessage());
    }

    public function testValidControlNameCustomMessage(): void
    {
        $constraint = new ValidControlName(message: 'Custom error');

        $violations = $this->validator->validate('1invalid', [$constraint]);
        $this->assertGreaterThan(0, $violations->count());
    }

    // =========================================================================
    // ValidEventName CONSTRAINT TESTS
    // =========================================================================

    #[DataProvider('validEventNames')]
    public function testValidEventNameAcceptsValid(string $name): void
    {
        $violations = $this->validator->validate($name, [new ValidEventName()]);

        $this->assertCount(0, $violations, "Expected '$name' to be valid");
    }

    public static function validEventNames(): array
    {
        return [
            'onClick' => ['onClick'],
            'onChange' => ['onChange'],
            'onSubmit' => ['onSubmit'],
            'onFocus' => ['onFocus'],
            'onBlur' => ['onBlur'],
            'onKeyDown' => ['onKeyDown'],
            'onKeyUp' => ['onKeyUp'],
            'onKeyPress' => ['onKeyPress'],
            'onMouseOver' => ['onMouseOver'],
            'onMouseOut' => ['onMouseOut'],
            'onLoad' => ['onLoad'],
            'customEvent' => ['customEvent'],
            'my_event' => ['my_event'],
            '_privateEvent' => ['_privateEvent'],
        ];
    }

    #[DataProvider('invalidEventNames')]
    public function testValidEventNameRejectsInvalid(string $name): void
    {
        $violations = $this->validator->validate($name, [new ValidEventName()]);

        $this->assertGreaterThan(0, $violations->count(), "Expected '$name' to be invalid");
    }

    public static function invalidEventNames(): array
    {
        return [
            'starts with number' => ['1onClick'],
            'contains hyphen' => ['on-click'],
            'contains space' => ['on click'],
            'contains special' => ['onClick<script>'],
            'contains dot' => ['on.click'],
            'contains slash' => ['on/click'],
        ];
    }

    public function testValidEventNameStrictMode(): void
    {
        $constraint = new ValidEventName(
            allowedEvents: ['onClick', 'onChange'],
            strict: true
        );

        // In whitelist
        $violations = $this->validator->validate('onClick', [$constraint]);
        $this->assertCount(0, $violations);

        // Not in whitelist
        $violations = $this->validator->validate('onCustom', [$constraint]);
        $this->assertCount(1, $violations);
    }

    public function testValidEventNameDefaultEventsUsed(): void
    {
        $constraint = new ValidEventName(strict: true);

        // Default events should be allowed
        $violations = $this->validator->validate('onClick', [$constraint]);
        $this->assertCount(0, $violations);

        $violations = $this->validator->validate('onBeforeAjaxProcess', [$constraint]);
        $this->assertCount(0, $violations);
    }

    // =========================================================================
    // SafeUrl CONSTRAINT TESTS
    // =========================================================================

    #[DataProvider('safeUrls')]
    public function testSafeUrlAcceptsSafe(string $url): void
    {
        $violations = $this->validator->validate($url, [new SafeUrl()]);

        $this->assertCount(0, $violations, "Expected '$url' to be safe");
    }

    public static function safeUrls(): array
    {
        return [
            'https' => ['https://example.com'],
            'https with path' => ['https://example.com/path/to/page'],
            'https with query' => ['https://example.com?foo=bar'],
            'https with fragment' => ['https://example.com#section'],
            'http' => ['http://example.com'],
            'relative root' => ['/path/to/page'],
            'relative current' => ['./page.html'],
            'relative parent' => ['../page.html'],
            'fragment only' => ['#anchor'],
        ];
    }

    #[DataProvider('dangerousUrls')]
    public function testSafeUrlBlocksDangerous(string $url): void
    {
        $violations = $this->validator->validate($url, [new SafeUrl()]);

        $this->assertGreaterThan(0, $violations->count(), "Expected '$url' to be blocked");
    }

    public static function dangerousUrls(): array
    {
        return [
            'javascript' => ['javascript:alert(1)'],
            'javascript uppercase' => ['JAVASCRIPT:alert(1)'],
            'javascript mixed' => ['JaVaScRiPt:alert(1)'],
            'data html' => ['data:text/html,<script>'],
            'data base64' => ['data:text/html;base64,PHNjcmlwdD4='],
            'vbscript' => ['vbscript:msgbox(1)'],
            'file' => ['file:///etc/passwd'],
        ];
    }

    public function testSafeUrlAllowedSchemes(): void
    {
        $constraint = new SafeUrl(allowedSchemes: ['https']);

        // https allowed
        $violations = $this->validator->validate('https://example.com', [$constraint]);
        $this->assertCount(0, $violations);

        // http not allowed
        $violations = $this->validator->validate('http://example.com', [$constraint]);
        $this->assertCount(1, $violations);
    }

    public function testSafeUrlAllowedHosts(): void
    {
        $constraint = new SafeUrl(allowedHosts: ['example.com', '*.trusted.com']);

        // Exact match
        $violations = $this->validator->validate('https://example.com', [$constraint]);
        $this->assertCount(0, $violations);

        // Wildcard subdomain
        $violations = $this->validator->validate('https://sub.trusted.com', [$constraint]);
        $this->assertCount(0, $violations);

        // Base domain for wildcard
        $violations = $this->validator->validate('https://trusted.com', [$constraint]);
        $this->assertCount(0, $violations);

        // Disallowed host
        $violations = $this->validator->validate('https://evil.com', [$constraint]);
        $this->assertCount(1, $violations);
    }

    public function testSafeUrlDisallowRelative(): void
    {
        $constraint = new SafeUrl(allowRelative: false);

        // Relative URL blocked
        $violations = $this->validator->validate('/path/to/page', [$constraint]);
        $this->assertCount(1, $violations);

        // Absolute URL allowed
        $violations = $this->validator->validate('https://example.com', [$constraint]);
        $this->assertCount(0, $violations);
    }

    public function testSafeUrlWithMailto(): void
    {
        $constraint = new SafeUrl(allowedSchemes: ['http', 'https', 'mailto']);

        $violations = $this->validator->validate('mailto:test@example.com', [$constraint]);
        $this->assertCount(0, $violations);
    }

    public function testSafeUrlNullAndEmptyAllowed(): void
    {
        // Null and empty are handled by NotBlank if needed
        $violations = $this->validator->validate(null, [new SafeUrl()]);
        $this->assertCount(0, $violations);

        $violations = $this->validator->validate('', [new SafeUrl()]);
        $this->assertCount(0, $violations);
    }

    // =========================================================================
    // EDGE CASES
    // =========================================================================

    public function testConstraintsWithAttribute(): void
    {
        // Test that constraints can be used as attributes (just instantiation test)
        $control = new ValidControlName();
        $this->assertInstanceOf(ValidControlName::class, $control);

        $event = new ValidEventName();
        $this->assertInstanceOf(ValidEventName::class, $event);

        $url = new SafeUrl();
        $this->assertInstanceOf(SafeUrl::class, $url);
    }

    public function testValidControlNameWithXssPayload(): void
    {
        $xssPayloads = [
            '<script>alert(1)</script>',
            'onclick="alert(1)"',
            'javascript:alert(1)',
            '"><img src=x onerror=alert(1)>',
        ];

        foreach ($xssPayloads as $payload) {
            $violations = $this->validator->validate($payload, [new ValidControlName()]);
            $this->assertGreaterThan(0, $violations->count(), "XSS payload should be rejected: $payload");
        }
    }

    public function testValidEventNameWithXssPayload(): void
    {
        $xssPayloads = [
            '<script>',
            'onclick="alert(1)"',
            'on-error',
        ];

        foreach ($xssPayloads as $payload) {
            $violations = $this->validator->validate($payload, [new ValidEventName()]);
            $this->assertGreaterThan(0, $violations->count(), "XSS payload should be rejected: $payload");
        }
    }

    public function testSafeUrlWithEncodedJavascript(): void
    {
        // These variations of javascript: scheme should be blocked
        $encodedPayloads = [
            'javascript:alert(1)',
            'JAVASCRIPT:alert(1)',
            'jAvAsCrIpT:alert(1)',
        ];

        foreach ($encodedPayloads as $payload) {
            $violations = $this->validator->validate($payload, [new SafeUrl()]);
            $this->assertGreaterThan(
                0,
                $violations->count(),
                "Encoded javascript should be blocked: " . json_encode($payload)
            );
        }
    }

    public function testConstraintMessagesContainValue(): void
    {
        $violations = $this->validator->validate('1invalid', [new ValidControlName()]);
        $this->assertStringContainsString('1invalid', $violations[0]->getMessage());

        $violations = $this->validator->validate('on-click', [new ValidEventName()]);
        $this->assertStringContainsString('on-click', $violations[0]->getMessage());

        $violations = $this->validator->validate('javascript:alert(1)', [new SafeUrl()]);
        $this->assertStringContainsString('javascript', $violations[0]->getMessage());
    }
}
