<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Database;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use VCL\Database\Connection;
use VCL\Database\ConnectionFactory;
use VCL\Database\Schema\SchemaManager;
use VCL\Database\EDatabaseError;

#[RequiresPhpExtension('pdo_sqlite')]
class SchemaManagerTest extends TestCase
{
    private Connection $connection;
    private SchemaManager $schema;

    protected function setUp(): void
    {
        $this->connection = ConnectionFactory::SQLiteMemory();
        $this->connection->Open();
        $this->schema = new SchemaManager($this->connection);
    }

    protected function tearDown(): void
    {
        $this->connection->Close();
    }

    public function testGetTablesEmpty(): void
    {
        $tables = $this->schema->GetTables();
        $this->assertIsArray($tables);
    }

    public function testCreateTable(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer', 'autoincrement' => true],
            'username' => ['type' => 'string', 'length' => 255, 'notnull' => true],
            'email' => ['type' => 'string', 'length' => 255],
        ], [
            'primary' => 'id',
        ]);

        $this->assertTrue($this->schema->TableExists('users'));
    }

    public function testCreateTableWithIndexes(): void
    {
        $this->schema->CreateTable('products', [
            'id' => ['type' => 'integer', 'autoincrement' => true],
            'name' => ['type' => 'string', 'length' => 255],
            'sku' => ['type' => 'string', 'length' => 50],
            'category' => ['type' => 'string', 'length' => 100],
        ], [
            'primary' => 'id',
            'unique' => [
                'idx_sku' => 'sku',
            ],
            'indexes' => [
                'idx_category' => 'category',
            ],
        ]);

        $this->assertTrue($this->schema->TableExists('products'));
        $this->assertTrue($this->schema->IndexExists('products', 'idx_sku'));
        $this->assertTrue($this->schema->IndexExists('products', 'idx_category'));
    }

    public function testTableExists(): void
    {
        $this->assertFalse($this->schema->TableExists('nonexistent'));

        $this->schema->CreateTable('test_table', [
            'id' => ['type' => 'integer'],
        ]);

        $this->assertTrue($this->schema->TableExists('test_table'));
    }

    public function testGetTables(): void
    {
        $this->schema->CreateTable('table1', ['id' => ['type' => 'integer']]);
        $this->schema->CreateTable('table2', ['id' => ['type' => 'integer']]);

        $tables = $this->schema->GetTables();

        $this->assertContains('table1', $tables);
        $this->assertContains('table2', $tables);
    }

    public function testDropTable(): void
    {
        $this->schema->CreateTable('to_drop', ['id' => ['type' => 'integer']]);
        $this->assertTrue($this->schema->TableExists('to_drop'));

        $this->schema->DropTable('to_drop');
        $this->assertFalse($this->schema->TableExists('to_drop'));
    }

    public function testRenameTable(): void
    {
        $this->schema->CreateTable('old_name', ['id' => ['type' => 'integer']]);
        $this->assertTrue($this->schema->TableExists('old_name'));

        $this->schema->RenameTable('old_name', 'new_name');

        $this->assertFalse($this->schema->TableExists('old_name'));
        $this->assertTrue($this->schema->TableExists('new_name'));
    }

    public function testGetTable(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer', 'autoincrement' => true],
            'username' => ['type' => 'string', 'length' => 255],
        ], [
            'primary' => 'id',
        ]);

        $table = $this->schema->GetTable('users');

        $this->assertEquals('users', $table['name']);
        $this->assertArrayHasKey('columns', $table);
        $this->assertArrayHasKey('indexes', $table);
        $this->assertArrayHasKey('primaryKey', $table);
    }

    public function testGetTableThrowsForNonexistent(): void
    {
        $this->expectException(EDatabaseError::class);
        $this->expectExceptionMessage("does not exist");
        $this->schema->GetTable('nonexistent');
    }

    public function testGetColumns(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer'],
            'username' => ['type' => 'string', 'length' => 255],
            'email' => ['type' => 'string', 'length' => 255],
        ]);

        $columns = $this->schema->GetColumns('users');

        $this->assertCount(3, $columns);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('username', $columns);
        $this->assertArrayHasKey('email', $columns);
    }

    public function testGetColumnNames(): void
    {
        $this->schema->CreateTable('test', [
            'col1' => ['type' => 'integer'],
            'col2' => ['type' => 'string', 'length' => 100],
        ]);

        $names = $this->schema->GetColumnNames('test');

        $this->assertContains('col1', $names);
        $this->assertContains('col2', $names);
    }

    public function testColumnExists(): void
    {
        $this->schema->CreateTable('test', [
            'id' => ['type' => 'integer'],
            'name' => ['type' => 'string', 'length' => 100],
        ]);

        $this->assertTrue($this->schema->ColumnExists('test', 'id'));
        $this->assertTrue($this->schema->ColumnExists('test', 'name'));
        $this->assertFalse($this->schema->ColumnExists('test', 'nonexistent'));
    }

    public function testColumnDetails(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer', 'autoincrement' => true],
            'username' => ['type' => 'string', 'length' => 100, 'notnull' => true],
        ], [
            'primary' => 'id',
        ]);

        $columns = $this->schema->GetColumns('users');

        $this->assertEquals('integer', $columns['id']['type']);
        $this->assertTrue($columns['id']['autoincrement']);

        $this->assertEquals('string', $columns['username']['type']);
        $this->assertTrue($columns['username']['notnull']);
    }

    public function testAddColumn(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer'],
        ]);

        $this->assertFalse($this->schema->ColumnExists('users', 'email'));

        $this->schema->AddColumn('users', 'email', [
            'type' => 'string',
            'length' => 255,
        ]);

        $this->assertTrue($this->schema->ColumnExists('users', 'email'));
    }

    public function testDropColumn(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer'],
            'name' => ['type' => 'string', 'length' => 100],
            'email' => ['type' => 'string', 'length' => 255],
        ]);

        $this->assertTrue($this->schema->ColumnExists('users', 'email'));

        $this->schema->DropColumn('users', 'email');

        $this->assertFalse($this->schema->ColumnExists('users', 'email'));
    }

    public function testGetIndexes(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer', 'autoincrement' => true],
            'email' => ['type' => 'string', 'length' => 255],
        ], [
            'primary' => 'id',
            'unique' => [
                'idx_email' => 'email',
            ],
        ]);

        $indexes = $this->schema->GetIndexes('users');

        $this->assertNotEmpty($indexes);
        $this->assertArrayHasKey('idx_email', $indexes);
        $this->assertTrue($indexes['idx_email']['unique']);
    }

    public function testIndexExists(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer'],
            'email' => ['type' => 'string', 'length' => 255],
        ], [
            'indexes' => [
                'idx_email' => 'email',
            ],
        ]);

        $this->assertTrue($this->schema->IndexExists('users', 'idx_email'));
        $this->assertFalse($this->schema->IndexExists('users', 'nonexistent'));
    }

    public function testAddIndex(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer'],
            'username' => ['type' => 'string', 'length' => 100],
        ]);

        $this->assertFalse($this->schema->IndexExists('users', 'idx_username'));

        $this->schema->AddIndex('users', 'idx_username', ['username']);

        $this->assertTrue($this->schema->IndexExists('users', 'idx_username'));
    }

    public function testAddUniqueIndex(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer'],
            'email' => ['type' => 'string', 'length' => 255],
        ]);

        $this->schema->AddIndex('users', 'idx_email_unique', ['email'], true);

        $indexes = $this->schema->GetIndexes('users');
        $this->assertTrue($indexes['idx_email_unique']['unique']);
    }

    public function testDropIndex(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer'],
            'username' => ['type' => 'string', 'length' => 100],
        ], [
            'indexes' => [
                'idx_username' => 'username',
            ],
        ]);

        $this->assertTrue($this->schema->IndexExists('users', 'idx_username'));

        $this->schema->DropIndex('users', 'idx_username');

        $this->assertFalse($this->schema->IndexExists('users', 'idx_username'));
    }

    public function testTruncateTable(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer', 'autoincrement' => true],
            'name' => ['type' => 'string', 'length' => 100],
        ], [
            'primary' => 'id',
        ]);

        // Insert some data
        $this->connection->ExecuteStatement("INSERT INTO users (name) VALUES ('Alice')");
        $this->connection->ExecuteStatement("INSERT INTO users (name) VALUES ('Bob')");

        $result = $this->connection->Execute("SELECT COUNT(*) as cnt FROM users");
        $this->assertEquals(2, $result->fetchAssociative()['cnt']);

        // Truncate
        $this->schema->TruncateTable('users');

        $result = $this->connection->Execute("SELECT COUNT(*) as cnt FROM users");
        $this->assertEquals(0, $result->fetchAssociative()['cnt']);
    }

    public function testGetTableDetails(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer'],
        ]);
        $this->schema->CreateTable('posts', [
            'id' => ['type' => 'integer'],
        ]);

        $details = $this->schema->GetTableDetails();

        $this->assertArrayHasKey('users', $details);
        $this->assertArrayHasKey('posts', $details);
        $this->assertArrayHasKey('columns', $details['users']);
    }

    public function testIntrospectSchema(): void
    {
        $this->schema->CreateTable('test', [
            'id' => ['type' => 'integer'],
        ]);

        $dbSchema = $this->schema->IntrospectSchema();

        $this->assertInstanceOf(\Doctrine\DBAL\Schema\Schema::class, $dbSchema);
        $this->assertTrue($dbSchema->hasTable('test'));
    }

    public function testCompareSchemas(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer'],
        ]);

        $fromSchema = $this->schema->IntrospectSchema();

        // Create a modified schema
        $toSchema = clone $fromSchema;
        $table = $toSchema->getTable('users');
        $table->addColumn('email', 'string', ['length' => 255]);

        $sql = $this->schema->CompareSchemas($fromSchema, $toSchema);

        $this->assertNotEmpty($sql);
        $this->assertIsArray($sql);
    }

    public function testGetDbalSchemaManager(): void
    {
        $sm = $this->schema->GetDbalSchemaManager();

        $this->assertInstanceOf(\Doctrine\DBAL\Schema\AbstractSchemaManager::class, $sm);
    }

    public function testMultiColumnIndex(): void
    {
        $this->schema->CreateTable('orders', [
            'id' => ['type' => 'integer'],
            'customer_id' => ['type' => 'integer'],
            'product_id' => ['type' => 'integer'],
        ], [
            'indexes' => [
                'idx_customer_product' => ['customer_id', 'product_id'],
            ],
        ]);

        $indexes = $this->schema->GetIndexes('orders');
        $this->assertArrayHasKey('idx_customer_product', $indexes);
        $this->assertCount(2, $indexes['idx_customer_product']['columns']);
    }

    public function testPrimaryKeyDetails(): void
    {
        $this->schema->CreateTable('users', [
            'id' => ['type' => 'integer', 'autoincrement' => true],
            'name' => ['type' => 'string', 'length' => 100],
        ], [
            'primary' => 'id',
        ]);

        $table = $this->schema->GetTable('users');

        $this->assertNotNull($table['primaryKey']);
        $this->assertContains('id', $table['primaryKey']);
    }

    public function testCompositePrimaryKey(): void
    {
        $this->schema->CreateTable('order_items', [
            'order_id' => ['type' => 'integer', 'notnull' => true],
            'product_id' => ['type' => 'integer', 'notnull' => true],
            'quantity' => ['type' => 'integer'],
        ], [
            'primary' => ['order_id', 'product_id'],
        ]);

        $table = $this->schema->GetTable('order_items');

        $this->assertCount(2, $table['primaryKey']);
        $this->assertContains('order_id', $table['primaryKey']);
        $this->assertContains('product_id', $table['primaryKey']);
    }
}
