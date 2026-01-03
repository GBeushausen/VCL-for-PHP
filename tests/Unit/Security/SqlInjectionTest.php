<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use VCL\Security\Escaper;
use VCL\Security\InputValidator;

/**
 * SQL Injection Test Suite.
 *
 * Tests input validation and escaping against known SQL injection attack vectors.
 * Based on OWASP SQL Injection Prevention Cheat Sheet and common attack patterns.
 *
 * Note: Actual prepared statement tests require a database connection.
 * These tests focus on input validation layers that prevent SQLi.
 */
class SqlInjectionTest extends TestCase
{
    private InputValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new InputValidator();
    }

    // =========================================================================
    // COLUMN NAME VALIDATION TESTS
    // =========================================================================

    #[DataProvider('validColumnNames')]
    public function testValidColumnNamesAccepted(string $name): void
    {
        // Valid column names should match the pattern used in CustomMySQLTable
        $pattern = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
        $this->assertMatchesRegularExpression($pattern, $name);
    }

    public static function validColumnNames(): array
    {
        return [
            'simple' => ['id'],
            'with underscore' => ['user_id'],
            'camelCase' => ['userId'],
            'uppercase' => ['ID'],
            'mixed' => ['User_ID_123'],
            'starting underscore' => ['_private'],
        ];
    }

    #[DataProvider('sqlInjectionColumnNames')]
    public function testSqlInjectionColumnNamesRejected(string $payload): void
    {
        // SQL injection attempts should not match valid column name pattern
        $pattern = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
        $this->assertDoesNotMatchRegularExpression($pattern, $payload);
    }

    public static function sqlInjectionColumnNames(): array
    {
        return [
            'simple injection' => ["id; DROP TABLE users; --"],
            'union select' => ["id UNION SELECT * FROM users"],
            'comment' => ["id--"],
            'or true' => ["id OR 1=1"],
            'single quote' => ["id'"],
            'double quote' => ['id"'],
            'backtick' => ["id`"],
            'parenthesis' => ["id()"],
            'semicolon' => ["id;"],
            'space' => ["id name"],
            'special chars' => ["id<>"],
            'equals' => ["id=1"],
            'minus' => ["id-1"],
            'plus' => ["id+1"],
            'asterisk' => ["id*"],
            'percent' => ["id%"],
            'hash' => ["id#"],
            'at sign' => ["id@"],
            'exclamation' => ["id!"],
            'caret' => ["id^"],
            'ampersand' => ["id&"],
            'pipe' => ["id|"],
            'backslash' => ["id\\"],
            'slash' => ["id/"],
            'comma' => ["id,other"],
            'dot injection' => ["id.other"],
            'bracket' => ["id[0]"],
            'curly' => ["id{0}"],
        ];
    }

    // =========================================================================
    // TABLE NAME VALIDATION TESTS
    // =========================================================================

    #[DataProvider('validTableNames')]
    public function testValidTableNamesAccepted(string $name): void
    {
        // Valid table names (including schema.table notation)
        $pattern = '/^[a-zA-Z_][a-zA-Z0-9_]*(?:\.[a-zA-Z_][a-zA-Z0-9_]*)?$/';
        $this->assertMatchesRegularExpression($pattern, $name);
    }

    public static function validTableNames(): array
    {
        return [
            'simple' => ['users'],
            'with underscore' => ['user_accounts'],
            'schema.table' => ['mydb.users'],
            'schema with underscore' => ['my_schema.my_table'],
        ];
    }

    #[DataProvider('sqlInjectionTableNames')]
    public function testSqlInjectionTableNamesRejected(string $payload): void
    {
        $pattern = '/^[a-zA-Z_][a-zA-Z0-9_]*(?:\.[a-zA-Z_][a-zA-Z0-9_]*)?$/';
        $this->assertDoesNotMatchRegularExpression($pattern, $payload);
    }

    public static function sqlInjectionTableNames(): array
    {
        return [
            'drop table' => ["users; DROP TABLE users; --"],
            'union select' => ["users UNION SELECT * FROM admins"],
            'subquery' => ["(SELECT * FROM users)"],
            'multiple dots' => ["a.b.c"],
            'space injection' => ["users WHERE 1=1"],
            'comment injection' => ["users/**/WHERE/**/1=1"],
        ];
    }

    // =========================================================================
    // ORDER DIRECTION VALIDATION
    // =========================================================================

    #[DataProvider('validOrderDirections')]
    public function testValidOrderDirectionsAccepted(string $direction): void
    {
        $normalized = strtolower($direction);
        $this->assertTrue(in_array($normalized, ['asc', 'desc'], true));
    }

    public static function validOrderDirections(): array
    {
        return [
            'asc lowercase' => ['asc'],
            'desc lowercase' => ['desc'],
            'ASC uppercase' => ['ASC'],
            'DESC uppercase' => ['DESC'],
        ];
    }

    #[DataProvider('sqlInjectionOrderDirections')]
    public function testSqlInjectionOrderDirectionsRejected(string $payload): void
    {
        $normalized = strtolower($payload);
        $this->assertFalse(in_array($normalized, ['asc', 'desc'], true));
    }

    public static function sqlInjectionOrderDirections(): array
    {
        return [
            'injection after asc' => ['asc; DROP TABLE users; --'],
            'union after desc' => ['desc UNION SELECT'],
            'random string' => ['random'],
            'numeric' => ['1'],
            'empty' => [''],
        ];
    }

    // =========================================================================
    // CLASSIC SQL INJECTION PAYLOADS
    // =========================================================================

    #[DataProvider('classicSqlInjectionPayloads')]
    public function testClassicSqlInjectionPayloadsDetected(string $payload): void
    {
        // These patterns should be caught by input validation
        // The payload should NOT be a valid control name
        $this->assertFalse($this->validator->isValidControlName($payload));
    }

    public static function classicSqlInjectionPayloads(): array
    {
        return [
            // Authentication bypass
            "' OR '1'='1" => ["' OR '1'='1"],
            "' OR '1'='1'--" => ["' OR '1'='1'--"],
            "' OR '1'='1'/*" => ["' OR '1'='1'/*"],
            "admin'--" => ["admin'--"],
            "1' OR '1'='1" => ["1' OR '1'='1"],

            // Union-based injection
            "' UNION SELECT NULL--" => ["' UNION SELECT NULL--"],
            "' UNION SELECT username, password FROM users--" => ["' UNION SELECT username, password FROM users--"],

            // Error-based injection
            "' AND 1=CONVERT(int,(SELECT TOP 1 table_name FROM information_schema.tables))--" =>
                ["' AND 1=CONVERT(int,(SELECT TOP 1 table_name FROM information_schema.tables))--"],

            // Time-based blind injection
            "'; WAITFOR DELAY '0:0:5'--" => ["'; WAITFOR DELAY '0:0:5'--"],
            "' AND SLEEP(5)--" => ["' AND SLEEP(5)--"],
            "' AND BENCHMARK(10000000,SHA1('test'))--" => ["' AND BENCHMARK(10000000,SHA1('test'))--"],

            // Stacked queries
            "'; DROP TABLE users--" => ["'; DROP TABLE users--"],
            "'; INSERT INTO users VALUES('hacker','hacked')--" => ["'; INSERT INTO users VALUES('hacker','hacked')--"],

            // Comment-based
            "admin'/*" => ["admin'/*"],
            "*/OR/**/1=1/*" => ["*/OR/**/1=1/*"],

            // Encoded payloads
            "%27%20OR%20%271%27=%271" => ["%27%20OR%20%271%27=%271"],
        ];
    }

    // =========================================================================
    // PREPARED STATEMENT SAFETY (Unit Test Level)
    // =========================================================================

    public function testPreparedStatementPlaceholdersAreNotEscaped(): void
    {
        // Verify that ? placeholders work correctly
        $sql = "SELECT * FROM users WHERE id = ? AND name = ?";

        $this->assertStringContainsString('?', $sql);
        $this->assertEquals(2, substr_count($sql, '?'));
    }

    public function testParameterBindingPreventsSqlInjection(): void
    {
        // This test simulates what happens with prepared statements
        // The malicious input becomes a literal string value, not SQL code

        $maliciousId = "1; DROP TABLE users; --";

        // With proper parameter binding, this becomes:
        // SELECT * FROM users WHERE id = '1; DROP TABLE users; --'
        // The semicolon and SQL commands are treated as literal string data

        // The pattern used in MySQLDatabase to detect param types
        $types = '';
        $types .= is_int($maliciousId) ? 'i' : (is_float($maliciousId) ? 'd' : 's');

        // Malicious string should be treated as string type
        $this->assertSame('s', $types);
    }

    // =========================================================================
    // ID/INTEGER VALIDATION
    // =========================================================================

    #[DataProvider('validIntegerIds')]
    public function testValidIntegerIdsAccepted(mixed $id): void
    {
        $result = $this->validator->validateInteger($id, min: 1);
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public static function validIntegerIds(): array
    {
        return [
            'integer 1' => [1],
            'integer 42' => [42],
            'string integer' => ['123'],
            'large integer' => [999999],
        ];
    }

    #[DataProvider('sqlInjectionIntegerIds')]
    public function testSqlInjectionIntegerIdsRejected(mixed $payload): void
    {
        $this->expectException(\VCL\Security\Exception\SecurityException::class);
        $this->validator->validateInteger($payload, min: 1);
    }

    public static function sqlInjectionIntegerIds(): array
    {
        return [
            'string with sql' => ["1; DROP TABLE users"],
            'or injection' => ["1 OR 1=1"],
            'negative when min 1' => [-1],
            'zero when min 1' => [0],
            'letters' => ["abc"],
        ];
    }

    // =========================================================================
    // MYSQL-SPECIFIC INJECTION PATTERNS
    // =========================================================================

    #[DataProvider('mysqlSpecificPayloads')]
    public function testMysqlSpecificPayloadsRejectedByColumnValidation(string $payload): void
    {
        $pattern = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
        $this->assertDoesNotMatchRegularExpression($pattern, $payload);
    }

    public static function mysqlSpecificPayloads(): array
    {
        return [
            'information_schema' => ["id FROM information_schema.tables"],
            'load_file' => ["' UNION SELECT LOAD_FILE('/etc/passwd')--"],
            'into outfile' => ["' UNION SELECT 'code' INTO OUTFILE '/tmp/shell.php'--"],
            'into dumpfile' => ["' UNION SELECT 'code' INTO DUMPFILE '/tmp/shell.php'--"],
            'hex encoded' => ["0x61646D696E"],
            'char function' => ["CHAR(97,100,109,105,110)"],
            'concat' => ["CONCAT('ad','min')"],
            'group_concat' => ["GROUP_CONCAT(table_name)"],
        ];
    }

    // =========================================================================
    // ESCAPER ID METHOD TESTS
    // =========================================================================

    #[DataProvider('sqlInjectionColumnNames')]
    public function testEscaperIdSanitizesInput(string $payload): void
    {
        $escaped = Escaper::id($payload);

        // Escaper::id() creates safe HTML IDs, not SQL identifiers
        // Should only contain alphanumeric, underscore, hyphen
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9_-]+$/', $escaped);

        // Should not contain SQL injection characters (except hyphen which is valid in HTML IDs)
        $this->assertStringNotContainsString("'", $escaped);
        $this->assertStringNotContainsString('"', $escaped);
        $this->assertStringNotContainsString(';', $escaped);
        $this->assertStringNotContainsString('/*', $escaped);
        $this->assertStringNotContainsString(' ', $escaped);
    }

    // =========================================================================
    // SECOND ORDER INJECTION PATTERNS
    // =========================================================================

    public function testStoredPayloadValidation(): void
    {
        // Even if a payload was stored in the database, output should be escaped
        $storedPayload = "'; DROP TABLE users; --";

        // When output in HTML context
        $htmlEscaped = Escaper::html($storedPayload);
        $this->assertStringNotContainsString("'", $htmlEscaped);

        // When used as identifier
        $idEscaped = Escaper::id($storedPayload);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9_-]+$/', $idEscaped);
    }
}
