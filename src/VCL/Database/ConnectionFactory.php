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

namespace VCL\Database;

use VCL\Database\Enums\DriverType;

/**
 * ConnectionFactory creates database connections with simplified configuration.
 *
 * Use this factory to create Connection instances from various configuration
 * formats including arrays, DSN strings, and environment variables.
 */
class ConnectionFactory
{
    /**
     * Create a connection from an array configuration.
     *
     * Supported keys:
     * - driver: mysql, pgsql, sqlite, sqlsrv, oci8 (default: mysql)
     * - host: Database host (default: localhost)
     * - port: Database port (optional, uses driver default)
     * - database/dbname: Database name
     * - username/user: Username
     * - password: Password
     * - charset: Character set (optional, uses driver default)
     * - socket: Unix socket path (MySQL/MariaDB only)
     * - persistent: Use persistent connections (default: false)
     */
    public static function FromArray(array $config, ?object $owner = null): Connection
    {
        $connection = new Connection($owner);

        // Driver
        $driver = $config['driver'] ?? $config['drivername'] ?? 'mysql';
        $connection->Driver = DriverType::FromAdodbDriver($driver);

        // Host
        $connection->Host = $config['host'] ?? $config['hostname'] ?? 'localhost';

        // Database name
        $connection->DatabaseName = $config['database'] ?? $config['dbname'] ?? $config['databasename'] ?? '';

        // Credentials
        $connection->UserName = $config['username'] ?? $config['user'] ?? '';
        $connection->UserPassword = $config['password'] ?? $config['userpassword'] ?? '';

        // Port
        if (isset($config['port'])) {
            $connection->Port = (int)$config['port'];
        }

        // Charset
        if (isset($config['charset'])) {
            $connection->Charset = $config['charset'];
        }

        // Unix socket
        if (isset($config['socket']) || isset($config['unix_socket'])) {
            $connection->UnixSocket = $config['socket'] ?? $config['unix_socket'];
        }

        // Persistent connections
        if (isset($config['persistent'])) {
            $connection->Persistent = (bool)$config['persistent'];
        }

        // Debug mode
        if (isset($config['debug'])) {
            $connection->Debug = (bool)$config['debug'];
        }

        return $connection;
    }

    /**
     * Create a connection from a DSN string.
     *
     * Format: driver://user:password@host:port/database?options
     *
     * Examples:
     * - mysql://root:secret@localhost/mydb
     * - pgsql://user:pass@localhost:5432/mydb?charset=UTF8
     * - sqlite:///path/to/database.db
     * - sqlite:///:memory:
     */
    public static function FromDsn(string $dsn, ?object $owner = null): Connection
    {
        $parsed = parse_url($dsn);

        if ($parsed === false) {
            throw new EDatabaseError("Invalid DSN format: {$dsn}");
        }

        $config = [
            'driver' => $parsed['scheme'] ?? 'mysql',
            'host' => $parsed['host'] ?? 'localhost',
            'username' => isset($parsed['user']) ? urldecode($parsed['user']) : '',
            'password' => isset($parsed['pass']) ? urldecode($parsed['pass']) : '',
            'database' => isset($parsed['path']) ? ltrim($parsed['path'], '/') : '',
        ];

        if (isset($parsed['port'])) {
            $config['port'] = $parsed['port'];
        }

        // Parse query string options
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $options);
            $config = array_merge($config, $options);
        }

        return self::FromArray($config, $owner);
    }

    /**
     * Create a connection from environment variables.
     *
     * Environment variables:
     * - DB_DRIVER (default: mysql)
     * - DB_HOST (default: localhost)
     * - DB_PORT (optional)
     * - DB_DATABASE
     * - DB_USERNAME
     * - DB_PASSWORD
     * - DB_CHARSET (optional)
     * - DATABASE_URL (alternative: full DSN string)
     */
    public static function FromEnvironment(?object $owner = null, string $prefix = 'DB_'): Connection
    {
        // Check for DATABASE_URL first (common in PaaS)
        $databaseUrl = getenv('DATABASE_URL');
        if ($databaseUrl !== false && $databaseUrl !== '') {
            return self::FromDsn($databaseUrl, $owner);
        }

        $config = [
            'driver' => getenv($prefix . 'DRIVER') ?: 'mysql',
            'host' => getenv($prefix . 'HOST') ?: 'localhost',
            'database' => getenv($prefix . 'DATABASE') ?: getenv($prefix . 'NAME') ?: '',
            'username' => getenv($prefix . 'USERNAME') ?: getenv($prefix . 'USER') ?: '',
            'password' => getenv($prefix . 'PASSWORD') ?: '',
        ];

        $port = getenv($prefix . 'PORT');
        if ($port !== false && $port !== '') {
            $config['port'] = (int)$port;
        }

        $charset = getenv($prefix . 'CHARSET');
        if ($charset !== false && $charset !== '') {
            $config['charset'] = $charset;
        }

        return self::FromArray($config, $owner);
    }

    /**
     * Create a MySQL connection.
     */
    public static function MySQL(
        string $host,
        string $database,
        string $username,
        string $password,
        int $port = 3306,
        string $charset = 'utf8mb4',
        ?object $owner = null
    ): Connection {
        return self::FromArray([
            'driver' => 'mysql',
            'host' => $host,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'port' => $port,
            'charset' => $charset,
        ], $owner);
    }

    /**
     * Create a PostgreSQL connection.
     */
    public static function PostgreSQL(
        string $host,
        string $database,
        string $username,
        string $password,
        int $port = 5432,
        string $charset = 'UTF8',
        ?object $owner = null
    ): Connection {
        return self::FromArray([
            'driver' => 'pgsql',
            'host' => $host,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'port' => $port,
            'charset' => $charset,
        ], $owner);
    }

    /**
     * Create a SQLite connection.
     */
    public static function SQLite(string $path, ?object $owner = null): Connection
    {
        return self::FromArray([
            'driver' => 'sqlite',
            'database' => $path,
        ], $owner);
    }

    /**
     * Create an in-memory SQLite connection.
     */
    public static function SQLiteMemory(?object $owner = null): Connection
    {
        return self::FromArray([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ], $owner);
    }

    /**
     * Create a SQL Server connection.
     */
    public static function SQLServer(
        string $host,
        string $database,
        string $username,
        string $password,
        int $port = 1433,
        ?object $owner = null
    ): Connection {
        return self::FromArray([
            'driver' => 'sqlsrv',
            'host' => $host,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'port' => $port,
        ], $owner);
    }

    /**
     * Create an Oracle connection.
     */
    public static function Oracle(
        string $host,
        string $database,
        string $username,
        string $password,
        int $port = 1521,
        string $charset = 'AL32UTF8',
        ?object $owner = null
    ): Connection {
        return self::FromArray([
            'driver' => 'oci8',
            'host' => $host,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'port' => $port,
            'charset' => $charset,
        ], $owner);
    }
}
