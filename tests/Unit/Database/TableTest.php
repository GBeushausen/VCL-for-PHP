<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Database;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use VCL\Database\Connection;
use VCL\Database\ConnectionFactory;
use VCL\Database\Table;
use VCL\Database\EDatabaseError;
use VCL\Database\Enums\DatasetState;

#[RequiresPhpExtension('pdo_sqlite')]
class TableTest extends TestCase
{
    private Connection $connection;
    private Table $table;

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

        $this->table = new Table();
        $this->table->Database = $this->connection;
        $this->table->TableName = 'users';
    }

    protected function tearDown(): void
    {
        if ($this->table->Active) {
            $this->table->Close();
        }
        $this->connection->Close();
    }

    public function testDefaultState(): void
    {
        $this->assertFalse($this->table->Active);
        $this->assertEquals(DatasetState::Inactive, $this->table->State);
    }

    public function testTableNameProperty(): void
    {
        $this->table->TableName = 'products';
        $this->assertEquals('products', $this->table->TableName);
    }

    public function testFilterProperty(): void
    {
        $this->table->Filter = "status = 'active'";
        $this->assertEquals("status = 'active'", $this->table->Filter);
    }

    public function testOrderFieldProperty(): void
    {
        $this->table->OrderField = 'username';
        $this->assertEquals('username', $this->table->OrderField);
    }

    public function testOrderProperty(): void
    {
        $this->table->Order = 'DESC';
        $this->assertEquals('DESC', $this->table->Order);

        $this->table->Order = 'asc';
        $this->assertEquals('ASC', $this->table->Order);
    }

    public function testHasAutoIncProperty(): void
    {
        $this->table->HasAutoInc = '0';
        $this->assertEquals('0', $this->table->HasAutoInc);
    }

    public function testOpenAndClose(): void
    {
        $this->table->Open();

        $this->assertTrue($this->table->Active);
        $this->assertEquals(DatasetState::Browse, $this->table->State);
        $this->assertEquals(3, $this->table->ReadRecordCount());

        $this->table->Close();
        $this->assertFalse($this->table->Active);
        $this->assertEquals(DatasetState::Inactive, $this->table->State);
    }

    public function testActivePropertySetter(): void
    {
        $this->table->Active = true;
        $this->assertTrue($this->table->Active);

        $this->table->Active = false;
        $this->assertFalse($this->table->Active);
    }

    public function testNavigation(): void
    {
        $this->table->OrderField = 'id';
        $this->table->Open();

        // Should be at first record
        $this->assertTrue($this->table->BOF());
        $this->assertFalse($this->table->EOF());
        $this->assertEquals('alice', $this->table->username);

        // Move to next
        $this->table->Next();
        $this->assertEquals('bob', $this->table->username);

        // Move to last
        $this->table->Last();
        $this->assertEquals('charlie', $this->table->username);

        // Move to first
        $this->table->First();
        $this->assertEquals('alice', $this->table->username);
    }

    public function testFieldAccess(): void
    {
        $this->table->OrderField = 'id';
        $this->table->Open();

        $this->assertEquals('alice', $this->table->username);
        $this->assertEquals('alice@example.com', $this->table->email);
        $this->assertEquals('active', $this->table->status);
    }

    public function testReadFields(): void
    {
        $this->table->Open();

        $fields = $this->table->ReadFields();
        $this->assertArrayHasKey('id', $fields);
        $this->assertArrayHasKey('username', $fields);
        $this->assertArrayHasKey('email', $fields);
        $this->assertArrayHasKey('status', $fields);
    }

    public function testReadKeyFields(): void
    {
        $this->table->Open();

        $keyFields = $this->table->ReadKeyFields();
        $this->assertContains('id', $keyFields);
    }

    public function testInsert(): void
    {
        $this->table->Open();
        $initialCount = $this->table->ReadRecordCount();

        $this->table->Insert();
        $this->assertEquals(DatasetState::Insert, $this->table->State);

        $this->table->username = 'dave';
        $this->table->email = 'dave@example.com';
        $this->table->status = 'active';
        $this->table->Post();

        $this->assertEquals(DatasetState::Browse, $this->table->State);
        $this->assertEquals($initialCount + 1, $this->table->ReadRecordCount());

        // Verify the insert
        $result = $this->connection->Execute("SELECT * FROM users WHERE username = 'dave'");
        $row = $result->fetchAssociative();
        $this->assertEquals('dave', $row['username']);
        $this->assertEquals('dave@example.com', $row['email']);
    }

    public function testInsertWithAutoIncrement(): void
    {
        $this->table->Open();

        $this->table->Insert();
        $this->table->username = 'eve';
        $this->table->email = 'eve@example.com';
        $this->table->Post();

        // Should have received an auto-increment ID
        $this->assertNotEmpty($this->table->id);
        $this->assertIsInt($this->table->id + 0); // coerce to int for comparison
    }

    public function testEdit(): void
    {
        $this->table->OrderField = 'id';
        $this->table->Open();

        // Navigate to alice
        $this->assertEquals('alice', $this->table->username);

        $this->table->Edit();
        $this->assertEquals(DatasetState::Edit, $this->table->State);

        $this->table->email = 'alice.updated@example.com';
        $this->table->Post();

        $this->assertEquals(DatasetState::Browse, $this->table->State);

        // Verify the update
        $result = $this->connection->Execute("SELECT email FROM users WHERE username = 'alice'");
        $row = $result->fetchAssociative();
        $this->assertEquals('alice.updated@example.com', $row['email']);
    }

    public function testEditWithDirectFieldSet(): void
    {
        $this->table->OrderField = 'id';
        $this->table->Open();

        // Setting a field in Browse mode should automatically enter Edit mode
        $this->table->status = 'suspended';
        $this->assertEquals(DatasetState::Edit, $this->table->State);

        $this->table->Post();

        // Verify the update
        $result = $this->connection->Execute("SELECT status FROM users WHERE username = 'alice'");
        $row = $result->fetchAssociative();
        $this->assertEquals('suspended', $row['status']);
    }

    public function testCancel(): void
    {
        $this->table->OrderField = 'id';
        $this->table->Open();

        $originalEmail = $this->table->email;

        $this->table->Edit();
        $this->table->email = 'changed@example.com';
        $this->table->Cancel();

        $this->assertEquals(DatasetState::Browse, $this->table->State);
        $this->assertEquals($originalEmail, $this->table->email);
    }

    public function testDelete(): void
    {
        $this->table->OrderField = 'id';
        $this->table->Open();

        $initialCount = $this->table->ReadRecordCount();

        // Delete alice
        $this->table->Delete();

        $this->assertEquals($initialCount - 1, $this->table->ReadRecordCount());

        // Verify deletion in database
        $result = $this->connection->Execute("SELECT * FROM users WHERE username = 'alice'");
        $this->assertFalse($result->fetchAssociative());
    }

    public function testDeleteMovesToNextRecord(): void
    {
        $this->table->OrderField = 'id';
        $this->table->Open();

        // Should be at alice
        $this->assertEquals('alice', $this->table->username);

        // Delete alice, should move to bob
        $this->table->Delete();
        $this->assertEquals('bob', $this->table->username);
    }

    public function testFilter(): void
    {
        $this->table->Filter = "status = 'active'";
        $this->table->Open();

        $this->assertEquals(2, $this->table->ReadRecordCount());

        // Iterate to verify all records match filter
        while (!$this->table->EOF()) {
            $this->assertEquals('active', $this->table->status);
            $this->table->Next();
        }
    }

    public function testOrderBy(): void
    {
        $this->table->OrderField = 'username';
        $this->table->Order = 'DESC';
        $this->table->Open();

        // charlie should be first (DESC order)
        $this->assertEquals('charlie', $this->table->username);

        $this->table->Next();
        $this->assertEquals('bob', $this->table->username);

        $this->table->Next();
        $this->assertEquals('alice', $this->table->username);
    }

    public function testBuildQuery(): void
    {
        $this->table->TableName = 'users';
        $this->table->Open();

        $sql = $this->table->BuildQuery();
        $this->assertStringContainsString('SELECT * FROM', $sql);
        $this->assertStringContainsString('users', $sql);
    }

    public function testBuildQueryWithFilter(): void
    {
        $this->table->Filter = "status = 'active'";
        $this->table->Open();

        $sql = $this->table->BuildQuery();
        $this->assertStringContainsString("status = 'active'", $sql);
    }

    public function testBuildQueryWithOrder(): void
    {
        $this->table->OrderField = 'username';
        $this->table->Order = 'DESC';
        $this->table->Open();

        $sql = $this->table->BuildQuery();
        $this->assertStringContainsString('ORDER BY username DESC', $sql);
    }

    public function testReadAssociativeFieldValues(): void
    {
        $this->table->Open();

        $values = $this->table->ReadAssociativeFieldValues();
        $this->assertIsArray($values);
        $this->assertArrayHasKey('username', $values);
    }

    public function testRefresh(): void
    {
        $this->table->Open();

        $this->assertEquals(3, $this->table->ReadRecordCount());

        // Add a new user directly
        $this->connection->ExecuteStatement("INSERT INTO users (username) VALUES ('frank')");

        // Refresh should pick up the new record
        $this->table->Refresh();

        $this->assertEquals(4, $this->table->ReadRecordCount());
    }

    public function testOpenWithoutTableNameReturnsEmpty(): void
    {
        $table = new Table();
        $table->Database = $this->connection;
        // TableName not set

        $table->Open();
        $this->assertEquals(0, $table->ReadRecordCount());
    }

    public function testOpenWithoutDatabaseThrows(): void
    {
        $table = new Table();
        $table->TableName = 'users';

        $this->expectException(EDatabaseError::class);
        $this->expectExceptionMessage('No Database assigned');
        $table->Open();
    }

    public function testEOFWithEmptyTable(): void
    {
        // Create an empty table
        $this->connection->ExecuteStatement('CREATE TABLE empty_table (id INTEGER PRIMARY KEY)');

        $table = new Table();
        $table->Database = $this->connection;
        $table->TableName = 'empty_table';
        $table->Open();

        $this->assertTrue($table->EOF());
        $this->assertEquals(0, $table->ReadRecordCount());
    }

    public function testEventProperties(): void
    {
        $this->table->OnBeforeOpen = 'handleBeforeOpen';
        $this->assertEquals('handleBeforeOpen', $this->table->OnBeforeOpen);

        $this->table->OnAfterOpen = 'handleAfterOpen';
        $this->assertEquals('handleAfterOpen', $this->table->OnAfterOpen);

        $this->table->OnBeforeInsert = 'handleBeforeInsert';
        $this->assertEquals('handleBeforeInsert', $this->table->OnBeforeInsert);

        $this->table->OnBeforeEdit = 'handleBeforeEdit';
        $this->assertEquals('handleBeforeEdit', $this->table->OnBeforeEdit);

        $this->table->OnBeforePost = 'handleBeforePost';
        $this->assertEquals('handleBeforePost', $this->table->OnBeforePost);

        $this->table->OnBeforeDelete = 'handleBeforeDelete';
        $this->assertEquals('handleBeforeDelete', $this->table->OnBeforeDelete);
    }

    public function testDatabaseProperty(): void
    {
        $table = new Table();
        $table->Database = $this->connection;

        $this->assertSame($this->connection, $table->Database);
    }

    public function testIsDataSetSubclass(): void
    {
        $this->assertInstanceOf(\VCL\Database\DataSet::class, $this->table);
    }

    public function testLimitStart(): void
    {
        $this->table->OrderField = 'id';
        $this->table->LimitStart = 1;
        $this->table->LimitCount = 10;
        $this->table->Open();

        // Should skip first record (alice)
        $this->assertEquals('bob', $this->table->username);
    }

    public function testLimitCount(): void
    {
        $this->table->OrderField = 'id';
        $this->table->LimitStart = 0;
        $this->table->LimitCount = 2;
        $this->table->Open();

        $this->assertEquals(2, $this->table->ReadRecordCount());
    }

    public function testMultipleUpdates(): void
    {
        $this->table->OrderField = 'id';
        $this->table->Open();

        // Update first record
        $this->table->Edit();
        $this->table->status = 'updated1';
        $this->table->Post();

        // Navigate to second and update
        $this->table->Next();
        $this->table->Edit();
        $this->table->status = 'updated2';
        $this->table->Post();

        // Verify updates
        $result = $this->connection->Execute("SELECT COUNT(*) as cnt FROM users WHERE status LIKE 'updated%'");
        $row = $result->fetchAssociative();
        $this->assertEquals(2, $row['cnt']);
    }

    public function testModifiedProperty(): void
    {
        $this->table->Open();

        $this->assertFalse($this->table->Modified);

        $this->table->Edit();
        $this->table->username = 'modified';
        $this->assertTrue($this->table->Modified);

        $this->table->Post();
        $this->assertFalse($this->table->Modified);
    }
}
