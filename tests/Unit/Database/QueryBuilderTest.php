<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Database;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use VCL\Database\Connection;
use VCL\Database\ConnectionFactory;
use VCL\Database\QueryBuilder;

#[RequiresPhpExtension('pdo_sqlite')]
class QueryBuilderTest extends TestCase
{
    private Connection $connection;
    private QueryBuilder $qb;

    protected function setUp(): void
    {
        $this->connection = ConnectionFactory::SQLiteMemory();
        $this->connection->Open();

        // Create test tables
        $this->connection->ExecuteStatement('
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                email TEXT,
                status TEXT DEFAULT "active",
                role TEXT DEFAULT "user"
            )
        ');

        $this->connection->ExecuteStatement('
            CREATE TABLE posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                title TEXT,
                content TEXT,
                created_at TEXT
            )
        ');

        // Insert test data
        $this->connection->ExecuteStatement("INSERT INTO users (username, email, status, role) VALUES ('alice', 'alice@example.com', 'active', 'admin')");
        $this->connection->ExecuteStatement("INSERT INTO users (username, email, status, role) VALUES ('bob', 'bob@example.com', 'active', 'user')");
        $this->connection->ExecuteStatement("INSERT INTO users (username, email, status, role) VALUES ('charlie', 'charlie@example.com', 'inactive', 'user')");

        $this->connection->ExecuteStatement("INSERT INTO posts (user_id, title, content) VALUES (1, 'Hello World', 'Content 1')");
        $this->connection->ExecuteStatement("INSERT INTO posts (user_id, title, content) VALUES (1, 'Second Post', 'Content 2')");
        $this->connection->ExecuteStatement("INSERT INTO posts (user_id, title, content) VALUES (2, 'Bob Post', 'Content 3')");

        $this->qb = new QueryBuilder($this->connection);
    }

    protected function tearDown(): void
    {
        $this->connection->Close();
    }

    public function testSelectAll(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->FetchAll();

        $this->assertCount(3, $rows);
    }

    public function testSelectSpecificColumns(): void
    {
        $rows = $this->qb
            ->Select('id', 'username')
            ->From('users')
            ->FetchAll();

        $this->assertCount(3, $rows);
        $this->assertArrayHasKey('id', $rows[0]);
        $this->assertArrayHasKey('username', $rows[0]);
        $this->assertArrayNotHasKey('email', $rows[0]);
    }

    public function testSelectWithAlias(): void
    {
        $rows = $this->qb
            ->Select('username AS name')
            ->From('users', 'u')
            ->FetchAll();

        $this->assertArrayHasKey('name', $rows[0]);
    }

    public function testAddSelect(): void
    {
        $rows = $this->qb
            ->Select('id')
            ->AddSelect('username', 'email')
            ->From('users')
            ->FetchAll();

        $this->assertArrayHasKey('id', $rows[0]);
        $this->assertArrayHasKey('username', $rows[0]);
        $this->assertArrayHasKey('email', $rows[0]);
    }

    public function testDistinct(): void
    {
        $rows = $this->qb
            ->Select('status')
            ->Distinct()
            ->From('users')
            ->FetchAll();

        $this->assertCount(2, $rows); // 'active' and 'inactive'
    }

    public function testWhereEquals(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->Where('status', '=', 'active')
            ->FetchAll();

        $this->assertCount(2, $rows);
    }

    public function testWhereNotEquals(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->Where('status', '!=', 'active')
            ->FetchAll();

        $this->assertCount(1, $rows);
        $this->assertEquals('charlie', $rows[0]['username']);
    }

    public function testAndWhere(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->Where('status', '=', 'active')
            ->AndWhere('role', '=', 'admin')
            ->FetchAll();

        $this->assertCount(1, $rows);
        $this->assertEquals('alice', $rows[0]['username']);
    }

    public function testOrWhere(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->Where('username', '=', 'alice')
            ->OrWhere('username', '=', 'bob')
            ->FetchAll();

        $this->assertCount(2, $rows);
    }

    public function testWhereIn(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->Where('username', 'IN', ['alice', 'bob'])
            ->FetchAll();

        $this->assertCount(2, $rows);
    }

    public function testWhereNotIn(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->Where('username', 'NOT IN', ['alice', 'bob'])
            ->FetchAll();

        $this->assertCount(1, $rows);
        $this->assertEquals('charlie', $rows[0]['username']);
    }

    public function testWhereLike(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->Where('email', 'LIKE', '%@example.com')
            ->FetchAll();

        $this->assertCount(3, $rows);
    }

    public function testWhereIsNull(): void
    {
        // Add a user with null email
        $this->connection->ExecuteStatement("INSERT INTO users (username, email, status) VALUES ('dave', NULL, 'active')");

        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->Where('email', 'IS NULL')
            ->FetchAll();

        $this->assertCount(1, $rows);
        $this->assertEquals('dave', $rows[0]['username']);
    }

    public function testWhereIsNotNull(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->Where('email', 'IS NOT NULL')
            ->FetchAll();

        $this->assertCount(3, $rows);
    }

    public function testWhereBetween(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->Where('id', 'BETWEEN', [1, 2])
            ->FetchAll();

        $this->assertCount(2, $rows);
    }

    public function testWhereRaw(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->WhereRaw("status = 'active' AND role = 'admin'")
            ->FetchAll();

        $this->assertCount(1, $rows);
    }

    public function testOrderBy(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->OrderBy('username', 'ASC')
            ->FetchAll();

        $this->assertEquals('alice', $rows[0]['username']);
        $this->assertEquals('bob', $rows[1]['username']);
        $this->assertEquals('charlie', $rows[2]['username']);
    }

    public function testOrderByDesc(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->OrderBy('username', 'DESC')
            ->FetchAll();

        $this->assertEquals('charlie', $rows[0]['username']);
    }

    public function testAddOrderBy(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->OrderBy('status', 'ASC')
            ->AddOrderBy('username', 'ASC')
            ->FetchAll();

        // Should be ordered by status first, then username
        $this->assertEquals('active', $rows[0]['status']);
    }

    public function testLimit(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->OrderBy('id')
            ->Limit(2)
            ->FetchAll();

        $this->assertCount(2, $rows);
    }

    public function testOffset(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->OrderBy('id')
            ->Limit(2)
            ->Offset(1)
            ->FetchAll();

        $this->assertCount(2, $rows);
        $this->assertEquals('bob', $rows[0]['username']); // Skipped alice
    }

    public function testGroupBy(): void
    {
        $rows = $this->qb
            ->Select('status', 'COUNT(*) as count')
            ->From('users')
            ->GroupBy('status')
            ->FetchAll();

        $this->assertCount(2, $rows); // 'active' and 'inactive' groups
    }

    public function testHaving(): void
    {
        $rows = $this->qb
            ->Select('status', 'COUNT(*) as count')
            ->From('users')
            ->GroupBy('status')
            ->Having('COUNT(*) > 1')
            ->FetchAll();

        $this->assertCount(1, $rows);
        $this->assertEquals('active', $rows[0]['status']);
    }

    public function testLeftJoin(): void
    {
        $rows = $this->qb
            ->Select('u.username', 'p.title')
            ->From('users', 'u')
            ->LeftJoin('posts', 'p', 'u.id = p.user_id')
            ->OrderBy('u.username')
            ->FetchAll();

        $this->assertGreaterThanOrEqual(3, count($rows));
    }

    public function testInnerJoin(): void
    {
        $rows = $this->qb
            ->Select('u.username', 'p.title')
            ->From('users', 'u')
            ->InnerJoin('posts', 'p', 'u.id = p.user_id')
            ->FetchAll();

        $this->assertCount(3, $rows); // Only users with posts
    }

    public function testFetchOne(): void
    {
        $row = $this->qb
            ->Select('*')
            ->From('users')
            ->OrderBy('id')
            ->FetchOne();

        $this->assertIsArray($row);
        $this->assertEquals('alice', $row['username']);
    }

    public function testFetchColumn(): void
    {
        $usernames = $this->qb
            ->Select('username')
            ->From('users')
            ->OrderBy('username')
            ->FetchColumn();

        $this->assertEquals(['alice', 'bob', 'charlie'], $usernames);
    }

    public function testFetchScalar(): void
    {
        $count = $this->qb
            ->Select('COUNT(*)')
            ->From('users')
            ->FetchScalar();

        $this->assertEquals(3, $count);
    }

    public function testInsert(): void
    {
        $affected = $this->qb
            ->Insert('users')
            ->SetValue('username', 'dave')
            ->SetValue('email', 'dave@example.com')
            ->SetValue('status', 'active')
            ->ExecuteStatement();

        $this->assertEquals(1, $affected);

        // Verify insert
        $this->qb->Reset();
        $row = $this->qb
            ->Select('*')
            ->From('users')
            ->Where('username', '=', 'dave')
            ->FetchOne();

        $this->assertEquals('dave', $row['username']);
    }

    public function testInsertWithSetValues(): void
    {
        $affected = $this->qb
            ->Insert('users')
            ->SetValues([
                'username' => 'eve',
                'email' => 'eve@example.com',
                'status' => 'active',
            ])
            ->ExecuteStatement();

        $this->assertEquals(1, $affected);
    }

    public function testUpdate(): void
    {
        $affected = $this->qb
            ->Update('users')
            ->Set('status', 'suspended')
            ->Where('username', '=', 'alice')
            ->ExecuteStatement();

        $this->assertEquals(1, $affected);

        // Verify update
        $this->qb->Reset();
        $row = $this->qb
            ->Select('status')
            ->From('users')
            ->Where('username', '=', 'alice')
            ->FetchOne();

        $this->assertEquals('suspended', $row['status']);
    }

    public function testUpdateMultipleRows(): void
    {
        $affected = $this->qb
            ->Update('users')
            ->Set('role', 'member')
            ->Where('role', '=', 'user')
            ->ExecuteStatement();

        $this->assertEquals(2, $affected);
    }

    public function testDelete(): void
    {
        $affected = $this->qb
            ->Delete('users')
            ->Where('username', '=', 'charlie')
            ->ExecuteStatement();

        $this->assertEquals(1, $affected);

        // Verify deletion
        $this->qb->Reset();
        $row = $this->qb
            ->Select('*')
            ->From('users')
            ->Where('username', '=', 'charlie')
            ->FetchOne();

        $this->assertFalse($row);
    }

    public function testGetSQL(): void
    {
        $this->qb
            ->Select('*')
            ->From('users')
            ->Where('status', '=', 'active');

        $sql = $this->qb->GetSQL();

        $this->assertStringContainsString('SELECT', $sql);
        $this->assertStringContainsString('FROM', $sql);
        $this->assertStringContainsString('users', $sql);
        $this->assertStringContainsString('WHERE', $sql);
    }

    public function testGetParameters(): void
    {
        $this->qb
            ->Select('*')
            ->From('users')
            ->Where('status', '=', 'active')
            ->AndWhere('role', '=', 'admin');

        $params = $this->qb->GetParameters();

        $this->assertCount(2, $params);
        $this->assertContains('active', $params);
        $this->assertContains('admin', $params);
    }

    public function testReset(): void
    {
        $this->qb
            ->Select('*')
            ->From('users')
            ->Where('status', '=', 'active');

        $this->qb->Reset();

        $this->qb
            ->Select('*')
            ->From('posts');

        $sql = $this->qb->GetSQL();

        $this->assertStringContainsString('posts', $sql);
        $this->assertStringNotContainsString('WHERE', $sql);
    }

    public function testCreateSubQuery(): void
    {
        $subQb = $this->qb->CreateSubQuery();

        $this->assertInstanceOf(QueryBuilder::class, $subQb);
        $this->assertNotSame($this->qb, $subQb);
    }

    public function testGetDbalQueryBuilder(): void
    {
        $dbalQb = $this->qb->GetDbalQueryBuilder();

        $this->assertInstanceOf(\Doctrine\DBAL\Query\QueryBuilder::class, $dbalQb);
    }

    public function testSetParameter(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->WhereRaw('status = :status')
            ->SetParameter('status', 'active')
            ->FetchAll();

        $this->assertCount(2, $rows);
    }

    public function testSetParameters(): void
    {
        $rows = $this->qb
            ->Select('*')
            ->From('users')
            ->WhereRaw('status = :status AND role = :role')
            ->SetParameters(['status' => 'active', 'role' => 'admin'])
            ->FetchAll();

        $this->assertCount(1, $rows);
    }

    public function testChaining(): void
    {
        $rows = $this->qb
            ->Select('u.username', 'COUNT(p.id) as post_count')
            ->From('users', 'u')
            ->LeftJoin('posts', 'p', 'u.id = p.user_id')
            ->Where('u.status', '=', 'active')
            ->GroupBy('u.id', 'u.username')
            ->Having('COUNT(p.id) > 0')
            ->OrderBy('post_count', 'DESC')
            ->Limit(10)
            ->FetchAll();

        $this->assertNotEmpty($rows);
        $this->assertArrayHasKey('username', $rows[0]);
        $this->assertArrayHasKey('post_count', $rows[0]);
    }
}
