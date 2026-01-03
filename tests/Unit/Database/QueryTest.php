<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Database;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use VCL\Database\Connection;
use VCL\Database\ConnectionFactory;
use VCL\Database\Query;
use VCL\Database\EDatabaseError;
use VCL\Database\Enums\DatasetState;

#[RequiresPhpExtension('pdo_sqlite')]
class QueryTest extends TestCase
{
    private Connection $connection;
    private Query $query;

    protected function setUp(): void
    {
        $this->connection = ConnectionFactory::SQLiteMemory();
        $this->connection->Open();

        // Create test table
        $this->connection->ExecuteStatement('
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                email TEXT,
                status TEXT DEFAULT "active"
            )
        ');

        // Insert test data
        $this->connection->ExecuteStatement("INSERT INTO users (username, email, status) VALUES ('alice', 'alice@example.com', 'active')");
        $this->connection->ExecuteStatement("INSERT INTO users (username, email, status) VALUES ('bob', 'bob@example.com', 'active')");
        $this->connection->ExecuteStatement("INSERT INTO users (username, email, status) VALUES ('charlie', 'charlie@example.com', 'inactive')");

        $this->query = new Query();
        $this->query->Database = $this->connection;
    }

    protected function tearDown(): void
    {
        if ($this->query->Active) {
            $this->query->Close();
        }
        $this->connection->Close();
    }

    public function testDefaultState(): void
    {
        $this->assertFalse($this->query->Active);
        $this->assertEquals(DatasetState::Inactive, $this->query->State);
    }

    public function testSQLProperty(): void
    {
        $this->query->SQL = ['SELECT * FROM users'];
        $this->assertEquals(['SELECT * FROM users'], $this->query->SQL);
    }

    public function testSQLPropertyWithString(): void
    {
        $this->query->SQL = 'SELECT * FROM users';
        $this->assertEquals(['SELECT * FROM users'], $this->query->SQL);
    }

    public function testParamsProperty(): void
    {
        $this->query->Params = ['active'];
        $this->assertEquals(['active'], $this->query->Params);
    }

    public function testFilterProperty(): void
    {
        $this->query->Filter = "status = 'active'";
        $this->assertEquals("status = 'active'", $this->query->Filter);
    }

    public function testOrderFieldProperty(): void
    {
        $this->query->OrderField = 'username';
        $this->assertEquals('username', $this->query->OrderField);
    }

    public function testOrderProperty(): void
    {
        $this->query->Order = 'DESC';
        $this->assertEquals('DESC', $this->query->Order);

        $this->query->Order = 'asc';
        $this->assertEquals('ASC', $this->query->Order);

        $this->query->Order = 'invalid';
        $this->assertEquals('ASC', $this->query->Order);
    }

    public function testBuildQuery(): void
    {
        $this->query->SQL = ['SELECT * FROM users'];
        $this->assertEquals('SELECT * FROM users', $this->query->BuildQuery());
    }

    public function testBuildQueryWithFilter(): void
    {
        $this->query->SQL = ['SELECT * FROM users'];
        $this->query->Filter = "status = 'active'";

        $sql = $this->query->BuildQuery();
        $this->assertStringContainsString("status = 'active'", $sql);
    }

    public function testBuildQueryWithOrder(): void
    {
        $this->query->SQL = ['SELECT * FROM users'];
        $this->query->OrderField = 'username';
        $this->query->Order = 'DESC';

        $sql = $this->query->BuildQuery();
        $this->assertStringContainsString('ORDER BY username DESC', $sql);
    }

    public function testOpenAndClose(): void
    {
        $this->query->SQL = ['SELECT * FROM users'];
        $this->query->Open();

        $this->assertTrue($this->query->Active);
        $this->assertEquals(DatasetState::Browse, $this->query->State);
        $this->assertEquals(3, $this->query->ReadRecordCount());

        $this->query->Close();
        $this->assertFalse($this->query->Active);
        $this->assertEquals(DatasetState::Inactive, $this->query->State);
    }

    public function testActivePropertySetter(): void
    {
        $this->query->SQL = ['SELECT * FROM users'];
        $this->query->Active = true;

        $this->assertTrue($this->query->Active);

        $this->query->Active = false;
        $this->assertFalse($this->query->Active);
    }

    public function testFetchAll(): void
    {
        $this->query->SQL = ['SELECT * FROM users ORDER BY id'];
        $rows = $this->query->FetchAll();

        $this->assertCount(3, $rows);
        $this->assertEquals('alice', $rows[0]['username']);
        $this->assertEquals('bob', $rows[1]['username']);
        $this->assertEquals('charlie', $rows[2]['username']);
    }

    public function testFetchOne(): void
    {
        $this->query->SQL = ['SELECT * FROM users ORDER BY id'];
        $row = $this->query->FetchOne();

        $this->assertIsArray($row);
        $this->assertEquals('alice', $row['username']);
    }

    public function testFetchOneReturnsFirstRow(): void
    {
        $this->query->SQL = ['SELECT * FROM users WHERE username = ?'];
        $this->query->Params = ['bob'];
        $row = $this->query->FetchOne();

        $this->assertEquals('bob', $row['username']);
    }

    public function testFetchOneWithEmptyResult(): void
    {
        $this->query->SQL = ['SELECT * FROM users WHERE username = ?'];
        $this->query->Params = ['nonexistent'];
        $row = $this->query->FetchOne();

        $this->assertFalse($row);
    }

    public function testNavigation(): void
    {
        $this->query->SQL = ['SELECT * FROM users ORDER BY id'];
        $this->query->Open();

        // Should be at first record
        $this->assertTrue($this->query->BOF());
        $this->assertFalse($this->query->EOF());
        $this->assertEquals('alice', $this->query->username);

        // Move to next
        $this->query->Next();
        $this->assertEquals('bob', $this->query->username);

        // Move to next
        $this->query->Next();
        $this->assertEquals('charlie', $this->query->username);

        // Move to next - should be at EOF
        $this->query->Next();
        $this->assertTrue($this->query->EOF());
    }

    public function testFirst(): void
    {
        $this->query->SQL = ['SELECT * FROM users ORDER BY id'];
        $this->query->Open();

        $this->query->Last();
        $this->assertEquals('charlie', $this->query->username);

        $this->query->First();
        $this->assertEquals('alice', $this->query->username);
        $this->assertTrue($this->query->BOF());
    }

    public function testLast(): void
    {
        $this->query->SQL = ['SELECT * FROM users ORDER BY id'];
        $this->query->Open();

        $this->query->Last();
        $this->assertEquals('charlie', $this->query->username);
    }

    public function testPrior(): void
    {
        $this->query->SQL = ['SELECT * FROM users ORDER BY id'];
        $this->query->Open();

        $this->query->Last();
        $this->query->Prior();
        $this->assertEquals('bob', $this->query->username);
    }

    public function testMoveBy(): void
    {
        $this->query->SQL = ['SELECT * FROM users ORDER BY id'];
        $this->query->Open();

        $this->query->MoveBy(2);
        $this->assertEquals('charlie', $this->query->username);

        $this->query->MoveBy(-1);
        $this->assertEquals('bob', $this->query->username);
    }

    public function testEOFWithEmptyResultSet(): void
    {
        $this->query->SQL = ['SELECT * FROM users WHERE 1 = 0'];
        $this->query->Open();

        $this->assertTrue($this->query->EOF());
        $this->assertEquals(0, $this->query->ReadRecordCount());
    }

    public function testFieldAccess(): void
    {
        $this->query->SQL = ['SELECT * FROM users ORDER BY id'];
        $this->query->Open();

        $this->assertEquals('alice', $this->query->username);
        $this->assertEquals('alice@example.com', $this->query->email);
        $this->assertEquals('active', $this->query->status);
    }

    public function testReadFields(): void
    {
        $this->query->SQL = ['SELECT * FROM users ORDER BY id'];
        $this->query->Open();

        $fields = $this->query->ReadFields();
        $this->assertArrayHasKey('id', $fields);
        $this->assertArrayHasKey('username', $fields);
        $this->assertArrayHasKey('email', $fields);
        $this->assertArrayHasKey('status', $fields);
    }

    public function testReadFieldCount(): void
    {
        $this->query->SQL = ['SELECT id, username FROM users'];
        $this->query->Open();

        $this->assertEquals(2, $this->query->ReadFieldCount());
    }

    public function testWithParameters(): void
    {
        $this->query->SQL = ['SELECT * FROM users WHERE status = ?'];
        $this->query->Params = ['active'];
        $rows = $this->query->FetchAll();

        $this->assertCount(2, $rows);
    }

    public function testWithMultipleParameters(): void
    {
        $this->query->SQL = ['SELECT * FROM users WHERE status = ? AND username = ?'];
        $this->query->Params = ['active', 'alice'];
        $row = $this->query->FetchOne();

        $this->assertEquals('alice', $row['username']);
    }

    public function testExecSQL(): void
    {
        $this->query->SQL = ['UPDATE users SET status = ? WHERE username = ?'];
        $this->query->Params = ['suspended', 'alice'];
        $affected = $this->query->ExecSQL();

        $this->assertEquals(1, $affected);

        // Verify the update
        $this->query->SQL = ['SELECT status FROM users WHERE username = ?'];
        $this->query->Params = ['alice'];
        $row = $this->query->FetchOne();

        $this->assertEquals('suspended', $row['status']);
    }

    public function testRefresh(): void
    {
        $this->query->SQL = ['SELECT * FROM users ORDER BY id'];
        $this->query->Open();

        $this->assertEquals(3, $this->query->ReadRecordCount());

        // Add a new user
        $this->connection->ExecuteStatement("INSERT INTO users (username) VALUES ('dave')");

        // Refresh should pick up the new record
        $this->query->Refresh();

        $this->assertEquals(4, $this->query->ReadRecordCount());
    }

    public function testOpenWithoutSQLThrows(): void
    {
        $this->query->SQL = [];

        $this->expectException(EDatabaseError::class);
        $this->expectExceptionMessage('Missing SQL query');
        $this->query->Open();
    }

    public function testOpenWithoutDatabaseThrows(): void
    {
        $query = new Query();
        $query->SQL = ['SELECT 1'];

        $this->expectException(EDatabaseError::class);
        $this->expectExceptionMessage('No Database assigned');
        $query->Open();
    }

    public function testLimitStart(): void
    {
        $this->query->SQL = ['SELECT * FROM users ORDER BY id'];
        $this->query->LimitStart = 1;
        $this->query->LimitCount = 10;
        $this->query->Open();

        // Should skip first record
        $this->assertEquals('bob', $this->query->username);
    }

    public function testLimitCount(): void
    {
        $this->query->SQL = ['SELECT * FROM users ORDER BY id'];
        $this->query->LimitStart = 0;
        $this->query->LimitCount = 2;
        $this->query->Open();

        $this->assertEquals(2, $this->query->ReadRecordCount());
    }

    public function testEventProperties(): void
    {
        $this->query->OnBeforeOpen = 'handleBeforeOpen';
        $this->assertEquals('handleBeforeOpen', $this->query->OnBeforeOpen);

        $this->query->OnAfterOpen = 'handleAfterOpen';
        $this->assertEquals('handleAfterOpen', $this->query->OnAfterOpen);

        $this->query->OnBeforeClose = 'handleBeforeClose';
        $this->assertEquals('handleBeforeClose', $this->query->OnBeforeClose);

        $this->query->OnAfterClose = 'handleAfterClose';
        $this->assertEquals('handleAfterClose', $this->query->OnAfterClose);
    }

    public function testDatabaseProperty(): void
    {
        $query = new Query();
        $query->Database = $this->connection;

        $this->assertSame($this->connection, $query->Database);
    }

    public function testIsDataSetSubclass(): void
    {
        $this->assertInstanceOf(\VCL\Database\DataSet::class, $this->query);
    }
}
