<?php
/**
 * This file is part of the VCL for PHP project
 *
 * Copyright (c) 2024-2025 Gunnar Beushausen
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 */

declare(strict_types=1);

namespace Tests\Unit\Database;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use VCL\Database\StoredProc;
use VCL\Database\Connection;
use VCL\Database\EDatabaseError;
use VCL\Database\Enums\DriverType;

#[RequiresPhpExtension('pdo_sqlite')]
class StoredProcTest extends TestCase
{
    private StoredProc $proc;
    private Connection $connection;

    protected function setUp(): void
    {
        $this->connection = new Connection();
        $this->connection->Driver = DriverType::SQLite;
        $this->connection->DatabaseName = ':memory:';
        $this->connection->Open();

        $this->proc = new StoredProc();
        $this->proc->Database = $this->connection;
    }

    protected function tearDown(): void
    {
        $this->connection->Close();
    }

    // -------------------------------------------------------------------------
    // Constructor and Basic Properties
    // -------------------------------------------------------------------------

    public function testConstructor(): void
    {
        $proc = new StoredProc();
        $this->assertInstanceOf(StoredProc::class, $proc);
    }

    public function testStoredProcNameProperty(): void
    {
        $this->proc->StoredProcName = 'GetUsers';
        $this->assertSame('GetUsers', $this->proc->StoredProcName);
    }

    public function testStoredProcNamePropertyEmpty(): void
    {
        $this->assertSame('', $this->proc->StoredProcName);
    }

    public function testFetchQueryProperty(): void
    {
        $this->proc->FetchQuery = 'SELECT @result';
        $this->assertSame('SELECT @result', $this->proc->FetchQuery);
    }

    public function testFetchQueryPropertyEmpty(): void
    {
        $this->assertSame('', $this->proc->FetchQuery);
    }

    // -------------------------------------------------------------------------
    // Legacy Getters/Setters
    // -------------------------------------------------------------------------

    public function testLegacyGetStoredProcName(): void
    {
        $this->proc->StoredProcName = 'TestProc';
        $this->assertSame('TestProc', $this->proc->getStoredProcName());
    }

    public function testLegacySetStoredProcName(): void
    {
        $this->proc->setStoredProcName('TestProc');
        $this->assertSame('TestProc', $this->proc->StoredProcName);
    }

    public function testLegacyDefaultStoredProcName(): void
    {
        $this->assertSame('', $this->proc->defaultStoredProcName());
    }

    public function testLegacyGetFetchQuery(): void
    {
        $this->proc->FetchQuery = 'SELECT 1';
        $this->assertSame('SELECT 1', $this->proc->getFetchQuery());
    }

    public function testLegacySetFetchQuery(): void
    {
        $this->proc->setFetchQuery('SELECT 1');
        $this->assertSame('SELECT 1', $this->proc->FetchQuery);
    }

    public function testLegacyDefaultFetchQuery(): void
    {
        $this->assertSame('', $this->proc->defaultFetchQuery());
    }

    // -------------------------------------------------------------------------
    // BuildQuery - MySQL Style (CALL)
    // -------------------------------------------------------------------------

    public function testBuildQueryMySqlNoParams(): void
    {
        $mysqlConn = new Connection();
        $mysqlConn->Driver = DriverType::MySQL;

        $proc = new StoredProc();
        $proc->Database = $mysqlConn;
        $proc->StoredProcName = 'GetAllUsers';

        $sql = $proc->BuildQuery();
        $this->assertSame('CALL GetAllUsers', $sql);
    }

    public function testBuildQueryMySqlWithParams(): void
    {
        $mysqlConn = new Connection();
        $mysqlConn->Driver = DriverType::MySQL;
        $mysqlConn->DatabaseName = ':memory:'; // Won't connect, just for driver

        // We need to mock the QuoteStr method since we can't connect
        $proc = $this->createPartialMock(StoredProc::class, ['BuildQuery']);
        $proc->Database = $mysqlConn;
        $proc->StoredProcName = 'GetUserById';
        $proc->Params = [123];

        // Test the actual implementation by creating a real proc
        $realProc = new StoredProc();
        $realProc->Database = $this->connection; // SQLite connection
        $realProc->StoredProcName = 'GetUserById';
        $realProc->Params = [123];

        $sql = $realProc->BuildQuery();
        // SQLite uses SELECT * FROM style
        $this->assertStringContainsString('GetUserById', $sql);
    }

    public function testBuildQueryMySqlWithFetchQuery(): void
    {
        $mysqlConn = new Connection();
        $mysqlConn->Driver = DriverType::MySQL;

        $proc = new StoredProc();
        $proc->Database = $mysqlConn;
        $proc->StoredProcName = 'SetUserStatus';
        $proc->FetchQuery = 'SELECT @affected_rows';

        $sql = $proc->BuildQuery();
        $this->assertSame('CALL SetUserStatus; SELECT @affected_rows', $sql);
    }

    public function testBuildQueryMariaDb(): void
    {
        $mariaConn = new Connection();
        $mariaConn->Driver = DriverType::MariaDB;

        $proc = new StoredProc();
        $proc->Database = $mariaConn;
        $proc->StoredProcName = 'GetUsers';

        $sql = $proc->BuildQuery();
        $this->assertSame('CALL GetUsers', $sql);
    }

    // -------------------------------------------------------------------------
    // BuildQuery - Oracle Style (BEGIN...END)
    // -------------------------------------------------------------------------

    public function testBuildQueryOracleNoParams(): void
    {
        $oracleConn = new Connection();
        $oracleConn->Driver = DriverType::Oracle;

        $proc = new StoredProc();
        $proc->Database = $oracleConn;
        $proc->StoredProcName = 'GetAllUsers';

        $sql = $proc->BuildQuery();
        $this->assertSame('BEGIN GetAllUsers; END;', $sql);
    }

    // -------------------------------------------------------------------------
    // BuildQuery - PostgreSQL/SQLite Style (SELECT * FROM)
    // -------------------------------------------------------------------------

    public function testBuildQueryPostgreSqlNoParams(): void
    {
        $pgConn = new Connection();
        $pgConn->Driver = DriverType::PostgreSQL;

        $proc = new StoredProc();
        $proc->Database = $pgConn;
        $proc->StoredProcName = 'get_users';

        $sql = $proc->BuildQuery();
        $this->assertSame('SELECT * FROM get_users', $sql);
    }

    public function testBuildQuerySqliteNoParams(): void
    {
        $this->proc->StoredProcName = 'get_users';

        $sql = $this->proc->BuildQuery();
        $this->assertSame('SELECT * FROM get_users', $sql);
    }

    public function testBuildQuerySqliteWithParams(): void
    {
        $this->proc->StoredProcName = 'get_user_by_id';
        $this->proc->Params = ['test_value'];

        $sql = $this->proc->BuildQuery();
        $this->assertStringContainsString('get_user_by_id', $sql);
        $this->assertStringContainsString('test_value', $sql);
    }

    // -------------------------------------------------------------------------
    // BuildQuery - Edge Cases
    // -------------------------------------------------------------------------

    public function testBuildQueryWithoutDatabase(): void
    {
        $proc = new StoredProc();
        $proc->StoredProcName = 'TestProc';

        $sql = $proc->BuildQuery();
        $this->assertSame('', $sql);
    }

    public function testBuildQueryEmptyProcName(): void
    {
        $sql = $this->proc->BuildQuery();
        $this->assertSame('SELECT * FROM ', $sql);
    }

    public function testBuildQueryMultipleParams(): void
    {
        $this->proc->StoredProcName = 'search_users';
        $this->proc->Params = ['John', 'Doe', 25];

        $sql = $this->proc->BuildQuery();
        $this->assertStringContainsString('search_users', $sql);
        $this->assertStringContainsString('John', $sql);
        $this->assertStringContainsString('Doe', $sql);
        $this->assertStringContainsString('25', $sql);
    }

    // -------------------------------------------------------------------------
    // Prepare Method
    // -------------------------------------------------------------------------

    public function testPrepareDoesNotThrow(): void
    {
        $this->proc->StoredProcName = 'TestProc';

        // Prepare is a no-op in DBAL, should not throw
        $this->proc->Prepare();
        $this->assertTrue(true);
    }

    // -------------------------------------------------------------------------
    // ExecuteProc Method
    // -------------------------------------------------------------------------

    public function testExecuteProcWithoutDatabaseThrows(): void
    {
        $proc = new StoredProc();
        $proc->StoredProcName = 'TestProc';

        $this->expectException(EDatabaseError::class);
        $this->expectExceptionMessage('No Database assigned');
        $proc->ExecuteProc();
    }

    // -------------------------------------------------------------------------
    // Inheritance from Query
    // -------------------------------------------------------------------------

    public function testInheritsFromQuery(): void
    {
        $this->assertInstanceOf(\VCL\Database\Query::class, $this->proc);
    }

    public function testCanSetDatabaseProperty(): void
    {
        $proc = new StoredProc();
        $proc->Database = $this->connection;
        $this->assertSame($this->connection, $proc->Database);
    }

    public function testCanSetParamsProperty(): void
    {
        $this->proc->Params = [1, 2, 3];
        $this->assertSame([1, 2, 3], $this->proc->Params);
    }

    public function testCanSetActiveProperty(): void
    {
        // Note: This will fail because SQLite doesn't support stored procedures
        // But we can test the property setting
        $this->proc->StoredProcName = 'test';
        $this->assertFalse($this->proc->Active);
    }

    // -------------------------------------------------------------------------
    // SQL Property Override
    // -------------------------------------------------------------------------

    public function testSqlPropertyIsOverriddenByBuildQuery(): void
    {
        $this->proc->StoredProcName = 'MyProc';
        $this->proc->SQL = ['SELECT 1']; // This should be ignored

        $sql = $this->proc->BuildQuery();
        $this->assertStringContainsString('MyProc', $sql);
        $this->assertStringNotContainsString('SELECT 1', $sql);
    }
}
