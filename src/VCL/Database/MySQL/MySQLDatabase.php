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

namespace VCL\Database\MySQL;

use VCL\Database\Connection;
use VCL\Database\Enums\DriverType;

/**
 * MySQLDatabase provides a connection to MySQL databases.
 *
 * This class extends the DBAL-based Connection class and pre-configures
 * it for MySQL connections. It exists primarily for backward compatibility.
 *
 * New code should use VCL\Database\Connection or VCL\Database\ConnectionFactory
 * instead.
 *
 * Example:
 * ```php
 * // Legacy way (still works)
 * $db = new MySQLDatabase();
 * $db->Host = 'localhost';
 * $db->DatabaseName = 'mydb';
 * $db->UserName = 'user';
 * $db->UserPassword = 'pass';
 * $db->Connected = true;
 *
 * // Modern way (recommended)
 * use VCL\Database\ConnectionFactory;
 * $db = ConnectionFactory::MySQL('localhost', 'mydb', 'user', 'pass');
 * $db->Open();
 * ```
 */
class MySQLDatabase extends Connection
{
    protected int $_dialect = 3;

    // Property Hooks
    public int $Dialect {
        get => $this->_dialect;
        set => $this->_dialect = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Driver = DriverType::MySQL;
        $this->Charset = 'utf8mb4';
    }

    // -------------------------------------------------------------------------
    // Legacy Accessors for Backward Compatibility
    // -------------------------------------------------------------------------

    public function getDriverName(): string
    {
        return $this->Driver->value;
    }

    public function setDriverName(string $value): void
    {
        $this->Driver = DriverType::FromAdodbDriver($value);
    }

    public function defaultDriverName(): string
    {
        return 'mysql';
    }

    public function getDialect(): int
    {
        return $this->_dialect;
    }

    public function setDialect(int $value): void
    {
        $this->Dialect = $value;
    }

    public function defaultDialect(): int
    {
        return 3;
    }

    /**
     * Legacy getter for DictionaryProperties.
     */
    public function readDictionaryProperties(): array|false
    {
        return $this->DictionaryProperties;
    }

    /**
     * Legacy setter for DictionaryProperties.
     */
    public function writeDictionaryProperties(array|false $value): void
    {
        $this->DictionaryProperties = $value;
    }
}
