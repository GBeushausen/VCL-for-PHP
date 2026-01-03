<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Database;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use VCL\Database\Connection;
use VCL\Database\ConnectionFactory;
use VCL\Database\EDatabaseError;
use VCL\Database\Enums\DriverType;

#[RequiresPhpExtension('pdo_sqlite')]
class ConnectionTest extends TestCase
{
    private ?Connection $connection = null;

    protected function setUp(): void
    {
        $this->connection = ConnectionFactory::SQLiteMemory();
    }

    protected function tearDown(): void
    {
        if ($this->connection !== null && $this->connection->Connected) {
            $this->connection->Close();
        }
    }

    public function testDefaultDriverIsMySQL(): void
    {
        $conn = new Connection();
        $this->assertEquals(DriverType::MySQL, $conn->Driver);
    }

    public function testDriverProperty(): void
    {
        $this->connection->Driver = DriverType::PostgreSQL;
        $this->assertEquals(DriverType::PostgreSQL, $this->connection->Driver);
    }

    public function testDriverNameProperty(): void
    {
        $conn = new Connection();
        $conn->DriverName = 'pgsql';
        $this->assertEquals('pgsql', $conn->DriverName);
        $this->assertEquals(DriverType::PostgreSQL, $conn->Driver);
    }

    public function testHostProperty(): void
    {
        $this->connection->Host = 'db.example.com';
        $this->assertEquals('db.example.com', $this->connection->Host);
    }

    public function testDatabaseNameProperty(): void
    {
        $this->connection->DatabaseName = 'testdb';
        $this->assertEquals('testdb', $this->connection->DatabaseName);
    }

    public function testUserNameProperty(): void
    {
        $this->connection->UserName = 'testuser';
        $this->assertEquals('testuser', $this->connection->UserName);
    }

    public function testUserPasswordProperty(): void
    {
        $this->connection->UserPassword = 'secret';
        $this->assertEquals('secret', $this->connection->UserPassword);
    }

    public function testPortProperty(): void
    {
        $this->connection->Port = 3307;
        $this->assertEquals(3307, $this->connection->Port);
    }

    public function testPortMinimumIsZero(): void
    {
        $this->connection->Port = -100;
        $this->assertEquals(0, $this->connection->Port);
    }

    public function testCharsetProperty(): void
    {
        $this->connection->Charset = 'latin1';
        $this->assertEquals('latin1', $this->connection->Charset);
    }

    public function testDebugProperty(): void
    {
        $this->connection->Debug = true;
        $this->assertTrue($this->connection->Debug);
    }

    public function testPersistentProperty(): void
    {
        $this->connection->Persistent = true;
        $this->assertTrue($this->connection->Persistent);
    }

    public function testUnixSocketProperty(): void
    {
        $this->connection->UnixSocket = '/var/run/mysql.sock';
        $this->assertEquals('/var/run/mysql.sock', $this->connection->UnixSocket);
    }

    public function testConnectAndDisconnect(): void
    {
        $this->assertFalse($this->connection->Connected);

        $this->connection->Open();
        $this->assertTrue($this->connection->Connected);

        $this->connection->Close();
        $this->assertFalse($this->connection->Connected);
    }

    public function testConnectedPropertySetter(): void
    {
        $this->connection->Connected = true;
        $this->assertTrue($this->connection->Connected);

        $this->connection->Connected = false;
        $this->assertFalse($this->connection->Connected);
    }

    public function testDbalReturnsConnectionAfterOpen(): void
    {
        $this->assertNull($this->connection->Dbal());

        $this->connection->Open();
        $this->assertNotNull($this->connection->Dbal());
        $this->assertInstanceOf(\Doctrine\DBAL\Connection::class, $this->connection->Dbal());
    }

    public function testExecuteQuery(): void
    {
        $this->connection->Open();

        // Create a test table
        $this->connection->ExecuteStatement('CREATE TABLE test_users (id INTEGER PRIMARY KEY, name TEXT)');
        $this->connection->ExecuteStatement("INSERT INTO test_users (id, name) VALUES (1, 'Alice')");
        $this->connection->ExecuteStatement("INSERT INTO test_users (id, name) VALUES (2, 'Bob')");

        $result = $this->connection->Execute('SELECT * FROM test_users ORDER BY id');
        $rows = $result->fetchAllAssociative();

        $this->assertCount(2, $rows);
        $this->assertEquals('Alice', $rows[0]['name']);
        $this->assertEquals('Bob', $rows[1]['name']);
    }

    public function testExecuteWithParameters(): void
    {
        $this->connection->Open();

        $this->connection->ExecuteStatement('CREATE TABLE test_params (id INTEGER PRIMARY KEY, name TEXT)');
        $this->connection->ExecuteStatement('INSERT INTO test_params (id, name) VALUES (?, ?)', [1, 'Test']);

        $result = $this->connection->Execute('SELECT name FROM test_params WHERE id = ?', [1]);
        $row = $result->fetchAssociative();

        $this->assertEquals('Test', $row['name']);
    }

    public function testExecuteStatementReturnsAffectedRows(): void
    {
        $this->connection->Open();

        $this->connection->ExecuteStatement('CREATE TABLE test_affected (id INTEGER PRIMARY KEY, value TEXT)');
        $this->connection->ExecuteStatement("INSERT INTO test_affected (id, value) VALUES (1, 'a')");
        $this->connection->ExecuteStatement("INSERT INTO test_affected (id, value) VALUES (2, 'b')");
        $this->connection->ExecuteStatement("INSERT INTO test_affected (id, value) VALUES (3, 'c')");

        $affected = $this->connection->ExecuteStatement("UPDATE test_affected SET value = 'updated' WHERE id > 1");
        $this->assertEquals(2, $affected);
    }

    public function testExecuteLimit(): void
    {
        $this->connection->Open();

        $this->connection->ExecuteStatement('CREATE TABLE test_limit (id INTEGER PRIMARY KEY, name TEXT)');
        for ($i = 1; $i <= 10; $i++) {
            $this->connection->ExecuteStatement('INSERT INTO test_limit (id, name) VALUES (?, ?)', [$i, "Item $i"]);
        }

        $result = $this->connection->ExecuteLimit('SELECT * FROM test_limit ORDER BY id', 3, 2);
        $rows = $result->fetchAllAssociative();

        $this->assertCount(3, $rows);
        $this->assertEquals(3, $rows[0]['id']); // Offset 2, so starts at id=3
    }

    public function testTransaction(): void
    {
        $this->connection->Open();

        $this->connection->ExecuteStatement('CREATE TABLE test_trans (id INTEGER PRIMARY KEY, name TEXT)');

        $this->connection->BeginTrans();
        $this->connection->ExecuteStatement("INSERT INTO test_trans (id, name) VALUES (1, 'Test')");
        $this->connection->Commit();

        $result = $this->connection->Execute('SELECT * FROM test_trans');
        $this->assertCount(1, $result->fetchAllAssociative());
    }

    public function testTransactionRollback(): void
    {
        $this->connection->Open();

        $this->connection->ExecuteStatement('CREATE TABLE test_rollback (id INTEGER PRIMARY KEY, name TEXT)');

        $this->connection->BeginTrans();
        $this->connection->ExecuteStatement("INSERT INTO test_rollback (id, name) VALUES (1, 'Test')");
        $this->connection->Rollback();

        $result = $this->connection->Execute('SELECT * FROM test_rollback');
        $this->assertCount(0, $result->fetchAllAssociative());
    }

    public function testCompleteTrans(): void
    {
        $this->connection->Open();

        $this->connection->ExecuteStatement('CREATE TABLE test_complete (id INTEGER PRIMARY KEY, name TEXT)');

        $this->connection->BeginTrans();
        $this->connection->ExecuteStatement("INSERT INTO test_complete (id, name) VALUES (1, 'Test')");
        $result = $this->connection->CompleteTrans(true);

        $this->assertTrue($result);
    }

    public function testCompleteTransWithRollback(): void
    {
        $this->connection->Open();

        $this->connection->ExecuteStatement('CREATE TABLE test_complete2 (id INTEGER PRIMARY KEY, name TEXT)');

        $this->connection->BeginTrans();
        $this->connection->ExecuteStatement("INSERT INTO test_complete2 (id, name) VALUES (1, 'Test')");
        $this->connection->CompleteTrans(false);

        $result = $this->connection->Execute('SELECT * FROM test_complete2');
        $this->assertCount(0, $result->fetchAllAssociative());
    }

    public function testLastInsertId(): void
    {
        $this->connection->Open();

        $this->connection->ExecuteStatement('CREATE TABLE test_lastid (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT)');
        $this->connection->ExecuteStatement("INSERT INTO test_lastid (name) VALUES ('Test')");

        $lastId = $this->connection->LastInsertId();
        $this->assertEquals(1, $lastId);
    }

    public function testQuoteStr(): void
    {
        $this->connection->Open();

        $quoted = $this->connection->QuoteStr("O'Reilly");
        $this->assertStringContainsString("O''Reilly", $quoted);
    }

    public function testQuoteIdentifier(): void
    {
        $this->connection->Open();

        $quoted = $this->connection->QuoteIdentifier('table_name');
        $this->assertNotEmpty($quoted);
    }

    public function testDbDate(): void
    {
        $formatted = $this->connection->DBDate('2024-12-25');
        $this->assertEquals('2024-12-25', $formatted);

        $formatted = $this->connection->DBDate('December 25, 2024');
        $this->assertEquals('2024-12-25', $formatted);
    }

    public function testMetaFields(): void
    {
        $this->connection->Open();

        $this->connection->ExecuteStatement('CREATE TABLE test_meta (id INTEGER, name TEXT, active INTEGER)');

        $fields = $this->connection->MetaFields('test_meta');

        $this->assertArrayHasKey('id', $fields);
        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('active', $fields);
    }

    public function testTables(): void
    {
        $this->connection->Open();

        $this->connection->ExecuteStatement('CREATE TABLE table_one (id INTEGER)');
        $this->connection->ExecuteStatement('CREATE TABLE table_two (id INTEGER)');

        $tables = $this->connection->Tables();

        $this->assertContains('table_one', $tables);
        $this->assertContains('table_two', $tables);
    }

    public function testExtractIndexes(): void
    {
        $this->connection->Open();

        $this->connection->ExecuteStatement('CREATE TABLE test_idx (id INTEGER PRIMARY KEY, email TEXT UNIQUE)');

        $primaryIndexes = $this->connection->ExtractIndexes('test_idx', true);
        $this->assertNotEmpty($primaryIndexes);

        $nonPrimaryIndexes = $this->connection->ExtractIndexes('test_idx', false);
        // SQLite creates a unique index for UNIQUE columns
        $this->assertNotEmpty($nonPrimaryIndexes);
    }

    public function testCreateQueryBuilder(): void
    {
        $this->connection->Open();

        $qb = $this->connection->CreateQueryBuilder();
        $this->assertInstanceOf(\Doctrine\DBAL\Query\QueryBuilder::class, $qb);
    }

    public function testCreateSchemaManager(): void
    {
        $this->connection->Open();

        $sm = $this->connection->CreateSchemaManager();
        $this->assertInstanceOf(\Doctrine\DBAL\Schema\AbstractSchemaManager::class, $sm);
    }

    public function testIntrospectSchema(): void
    {
        $this->connection->Open();

        $this->connection->ExecuteStatement('CREATE TABLE schema_test (id INTEGER PRIMARY KEY)');

        $schema = $this->connection->IntrospectSchema();
        $this->assertInstanceOf(\Doctrine\DBAL\Schema\Schema::class, $schema);
        $this->assertTrue($schema->hasTable('schema_test'));
    }

    public function testExecuteWithoutConnectionThrows(): void
    {
        $conn = new Connection();
        $conn->DatabaseName = '/nonexistent/path/to/db.sqlite';
        $conn->Driver = DriverType::SQLite;

        $this->expectException(EDatabaseError::class);
        $conn->Execute('SELECT 1');
    }
}
