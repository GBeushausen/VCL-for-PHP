<?php
/**
 * This file is part of the VCL for PHP project
 *
 * Copyright (c) 2004-2008 qadram software S.L.
 * Copyright (c) 2024-2025 Gunnar Beushausen
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 */

declare(strict_types=1);

namespace VCL\Database\Enums;

/**
 * DriverType defines the available database driver types.
 *
 * Use this enum to specify which database driver to use when creating
 * a database connection. Each driver type maps to a Doctrine DBAL driver.
 */
enum DriverType: string
{
    case MySQL = 'mysql';
    case PostgreSQL = 'pgsql';
    case SQLite = 'sqlite';
    case SQLServer = 'sqlsrv';
    case Oracle = 'oci8';
    case MariaDB = 'mariadb';

    /**
     * Returns the Doctrine DBAL driver name for this driver type.
     */
    public function ToDbalDriver(): string
    {
        return match($this) {
            self::MySQL => 'pdo_mysql',
            self::MariaDB => 'pdo_mysql',
            self::PostgreSQL => 'pdo_pgsql',
            self::SQLite => 'pdo_sqlite',
            self::SQLServer => 'pdo_sqlsrv',
            self::Oracle => 'oci8',
        };
    }

    /**
     * Returns the default port for this database type.
     */
    public function DefaultPort(): int
    {
        return match($this) {
            self::MySQL, self::MariaDB => 3306,
            self::PostgreSQL => 5432,
            self::SQLite => 0,
            self::SQLServer => 1433,
            self::Oracle => 1521,
        };
    }

    /**
     * Returns the default charset for this database type.
     */
    public function DefaultCharset(): string
    {
        return match($this) {
            self::MySQL, self::MariaDB => 'utf8mb4',
            self::PostgreSQL => 'UTF8',
            self::SQLite => 'UTF-8',
            self::SQLServer => 'UTF-8',
            self::Oracle => 'AL32UTF8',
        };
    }

    /**
     * Creates a DriverType from a legacy ADOdb driver name.
     */
    public static function FromAdodbDriver(string $driver): self
    {
        return match(strtolower($driver)) {
            'mysql', 'mysqli', 'pdo_mysql' => self::MySQL,
            'mariadb' => self::MariaDB,
            'pgsql', 'postgres', 'postgres7', 'postgres8', 'postgres9', 'pdo_pgsql' => self::PostgreSQL,
            'sqlite', 'sqlite3', 'pdo_sqlite' => self::SQLite,
            'mssql', 'mssqlnative', 'sqlsrv', 'pdo_sqlsrv' => self::SQLServer,
            'oracle', 'oci8', 'oci8po' => self::Oracle,
            default => self::MySQL,
        };
    }
}
