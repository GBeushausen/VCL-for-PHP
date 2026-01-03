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
class ConnectionFactoryTest extends TestCase
{
    public function testFromArrayWithMySQLConfig(): void
    {
        $conn = ConnectionFactory::FromArray([
            'driver' => 'mysql',
            'host' => 'db.example.com',
            'database' => 'mydb',
            'username' => 'user',
            'password' => 'secret',
            'port' => 3307,
            'charset' => 'utf8mb4',
        ]);

        $this->assertInstanceOf(Connection::class, $conn);
        $this->assertEquals(DriverType::MySQL, $conn->Driver);
        $this->assertEquals('db.example.com', $conn->Host);
        $this->assertEquals('mydb', $conn->DatabaseName);
        $this->assertEquals('user', $conn->UserName);
        $this->assertEquals('secret', $conn->UserPassword);
        $this->assertEquals(3307, $conn->Port);
        $this->assertEquals('utf8mb4', $conn->Charset);
    }

    public function testFromArrayWithAlternativeKeys(): void
    {
        $conn = ConnectionFactory::FromArray([
            'drivername' => 'pgsql',
            'hostname' => 'localhost',
            'dbname' => 'testdb',
            'user' => 'pguser',
            'userpassword' => 'pgpass',
        ]);

        $this->assertEquals(DriverType::PostgreSQL, $conn->Driver);
        $this->assertEquals('localhost', $conn->Host);
        $this->assertEquals('testdb', $conn->DatabaseName);
        $this->assertEquals('pguser', $conn->UserName);
        $this->assertEquals('pgpass', $conn->UserPassword);
    }

    public function testFromArrayWithUnixSocket(): void
    {
        $conn = ConnectionFactory::FromArray([
            'driver' => 'mysql',
            'socket' => '/var/run/mysql.sock',
            'database' => 'mydb',
            'username' => 'root',
            'password' => '',
        ]);

        $this->assertEquals('/var/run/mysql.sock', $conn->UnixSocket);
    }

    public function testFromArrayWithPersistent(): void
    {
        $conn = ConnectionFactory::FromArray([
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'mydb',
            'username' => 'user',
            'password' => 'pass',
            'persistent' => true,
        ]);

        $this->assertTrue($conn->Persistent);
    }

    public function testFromArrayWithDebug(): void
    {
        $conn = ConnectionFactory::FromArray([
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'mydb',
            'username' => 'user',
            'password' => 'pass',
            'debug' => true,
        ]);

        $this->assertTrue($conn->Debug);
    }

    public function testFromDsnMySQL(): void
    {
        $conn = ConnectionFactory::FromDsn('mysql://user:pass@localhost:3306/mydb');

        $this->assertEquals(DriverType::MySQL, $conn->Driver);
        $this->assertEquals('localhost', $conn->Host);
        $this->assertEquals('mydb', $conn->DatabaseName);
        $this->assertEquals('user', $conn->UserName);
        $this->assertEquals('pass', $conn->UserPassword);
        $this->assertEquals(3306, $conn->Port);
    }

    public function testFromDsnPostgreSQL(): void
    {
        $conn = ConnectionFactory::FromDsn('pgsql://pguser:pgpass@db.example.com:5432/pgdb');

        $this->assertEquals(DriverType::PostgreSQL, $conn->Driver);
        $this->assertEquals('db.example.com', $conn->Host);
        $this->assertEquals('pgdb', $conn->DatabaseName);
        $this->assertEquals('pguser', $conn->UserName);
        $this->assertEquals('pgpass', $conn->UserPassword);
        $this->assertEquals(5432, $conn->Port);
    }

    public function testFromDsnWithQueryParams(): void
    {
        $conn = ConnectionFactory::FromDsn('mysql://user:pass@localhost/mydb?charset=latin1');

        $this->assertEquals('latin1', $conn->Charset);
    }

    public function testFromDsnWithEncodedCredentials(): void
    {
        $conn = ConnectionFactory::FromDsn('mysql://user%40domain:p%40ss%3Aword@localhost/mydb');

        $this->assertEquals('user@domain', $conn->UserName);
        $this->assertEquals('p@ss:word', $conn->UserPassword);
    }

    public function testFromDsnSQLite(): void
    {
        $conn = ConnectionFactory::FromDsn('sqlite:///path/to/database.db');

        $this->assertEquals(DriverType::SQLite, $conn->Driver);
        $this->assertEquals('path/to/database.db', $conn->DatabaseName);
    }

    public function testFromDsnSQLiteMemory(): void
    {
        $conn = ConnectionFactory::FromDsn('sqlite:///:memory:');

        $this->assertEquals(DriverType::SQLite, $conn->Driver);
        $this->assertEquals(':memory:', $conn->DatabaseName);
    }

    public function testMySQLFactory(): void
    {
        $conn = ConnectionFactory::MySQL('localhost', 'testdb', 'user', 'pass');

        $this->assertEquals(DriverType::MySQL, $conn->Driver);
        $this->assertEquals('localhost', $conn->Host);
        $this->assertEquals('testdb', $conn->DatabaseName);
        $this->assertEquals('user', $conn->UserName);
        $this->assertEquals('pass', $conn->UserPassword);
        $this->assertEquals(3306, $conn->Port);
        $this->assertEquals('utf8mb4', $conn->Charset);
    }

    public function testMySQLFactoryWithCustomPort(): void
    {
        $conn = ConnectionFactory::MySQL('localhost', 'testdb', 'user', 'pass', 3307);

        $this->assertEquals(3307, $conn->Port);
    }

    public function testPostgreSQLFactory(): void
    {
        $conn = ConnectionFactory::PostgreSQL('localhost', 'testdb', 'user', 'pass');

        $this->assertEquals(DriverType::PostgreSQL, $conn->Driver);
        $this->assertEquals('localhost', $conn->Host);
        $this->assertEquals('testdb', $conn->DatabaseName);
        $this->assertEquals('user', $conn->UserName);
        $this->assertEquals('pass', $conn->UserPassword);
        $this->assertEquals(5432, $conn->Port);
        $this->assertEquals('UTF8', $conn->Charset);
    }

    public function testSQLiteFactory(): void
    {
        $conn = ConnectionFactory::SQLite('/tmp/test.db');

        $this->assertEquals(DriverType::SQLite, $conn->Driver);
        $this->assertEquals('/tmp/test.db', $conn->DatabaseName);
    }

    public function testSQLiteMemoryFactory(): void
    {
        $conn = ConnectionFactory::SQLiteMemory();

        $this->assertEquals(DriverType::SQLite, $conn->Driver);
        $this->assertEquals(':memory:', $conn->DatabaseName);
    }

    public function testSQLiteMemoryCanConnect(): void
    {
        $conn = ConnectionFactory::SQLiteMemory();
        $conn->Open();

        $this->assertTrue($conn->Connected);

        $conn->Close();
    }

    public function testSQLServerFactory(): void
    {
        $conn = ConnectionFactory::SQLServer('localhost', 'testdb', 'sa', 'pass');

        $this->assertEquals(DriverType::SQLServer, $conn->Driver);
        $this->assertEquals('localhost', $conn->Host);
        $this->assertEquals('testdb', $conn->DatabaseName);
        $this->assertEquals('sa', $conn->UserName);
        $this->assertEquals('pass', $conn->UserPassword);
        $this->assertEquals(1433, $conn->Port);
    }

    public function testOracleFactory(): void
    {
        $conn = ConnectionFactory::Oracle('localhost', 'ORCL', 'system', 'oracle');

        $this->assertEquals(DriverType::Oracle, $conn->Driver);
        $this->assertEquals('localhost', $conn->Host);
        $this->assertEquals('ORCL', $conn->DatabaseName);
        $this->assertEquals('system', $conn->UserName);
        $this->assertEquals('oracle', $conn->UserPassword);
        $this->assertEquals(1521, $conn->Port);
        $this->assertEquals('AL32UTF8', $conn->Charset);
    }

    public function testFromEnvironmentWithDatabaseUrl(): void
    {
        // Set environment variable
        putenv('DATABASE_URL=mysql://envuser:envpass@envhost/envdb');

        try {
            $conn = ConnectionFactory::FromEnvironment();

            $this->assertEquals(DriverType::MySQL, $conn->Driver);
            $this->assertEquals('envhost', $conn->Host);
            $this->assertEquals('envdb', $conn->DatabaseName);
            $this->assertEquals('envuser', $conn->UserName);
            $this->assertEquals('envpass', $conn->UserPassword);
        } finally {
            // Clean up
            putenv('DATABASE_URL');
        }
    }

    public function testFromEnvironmentWithIndividualVars(): void
    {
        // Set environment variables
        putenv('DB_DRIVER=pgsql');
        putenv('DB_HOST=pghost');
        putenv('DB_DATABASE=pgdb');
        putenv('DB_USERNAME=pguser');
        putenv('DB_PASSWORD=pgpass');
        putenv('DB_PORT=5433');
        putenv('DB_CHARSET=UTF8');

        try {
            $conn = ConnectionFactory::FromEnvironment();

            $this->assertEquals(DriverType::PostgreSQL, $conn->Driver);
            $this->assertEquals('pghost', $conn->Host);
            $this->assertEquals('pgdb', $conn->DatabaseName);
            $this->assertEquals('pguser', $conn->UserName);
            $this->assertEquals('pgpass', $conn->UserPassword);
            $this->assertEquals(5433, $conn->Port);
            $this->assertEquals('UTF8', $conn->Charset);
        } finally {
            // Clean up
            putenv('DB_DRIVER');
            putenv('DB_HOST');
            putenv('DB_DATABASE');
            putenv('DB_USERNAME');
            putenv('DB_PASSWORD');
            putenv('DB_PORT');
            putenv('DB_CHARSET');
        }
    }

    public function testFromEnvironmentWithCustomPrefix(): void
    {
        putenv('MYAPP_DRIVER=mysql');
        putenv('MYAPP_HOST=myhost');
        putenv('MYAPP_DATABASE=mydb');
        putenv('MYAPP_USERNAME=myuser');
        putenv('MYAPP_PASSWORD=mypass');

        try {
            $conn = ConnectionFactory::FromEnvironment(null, 'MYAPP_');

            $this->assertEquals(DriverType::MySQL, $conn->Driver);
            $this->assertEquals('myhost', $conn->Host);
            $this->assertEquals('mydb', $conn->DatabaseName);
        } finally {
            putenv('MYAPP_DRIVER');
            putenv('MYAPP_HOST');
            putenv('MYAPP_DATABASE');
            putenv('MYAPP_USERNAME');
            putenv('MYAPP_PASSWORD');
        }
    }

    public function testDefaultDriverIsMySQL(): void
    {
        $conn = ConnectionFactory::FromArray([
            'host' => 'localhost',
            'database' => 'mydb',
        ]);

        $this->assertEquals(DriverType::MySQL, $conn->Driver);
    }

    public function testDefaultHostIsLocalhost(): void
    {
        $conn = ConnectionFactory::FromArray([
            'database' => 'mydb',
        ]);

        $this->assertEquals('localhost', $conn->Host);
    }
}
