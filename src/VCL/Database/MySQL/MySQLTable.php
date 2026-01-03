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

use VCL\Database\Table;

/**
 * MySQLTable encapsulates a MySQL database table.
 *
 * This class extends the DBAL-based Table class for MySQL-specific use cases.
 * It exists primarily for backward compatibility.
 *
 * New code should use VCL\Database\Table directly.
 *
 * Example:
 * ```php
 * // Legacy way (still works)
 * $table = new MySQLTable();
 * $table->Database = $mysqlDb;
 * $table->TableName = 'users';
 * $table->Active = true;
 *
 * // Modern way (recommended)
 * use VCL\Database\Table;
 * $table = new Table();
 * $table->Database = $connection;
 * $table->TableName = 'users';
 * $table->Active = true;
 * ```
 */
class MySQLTable extends Table
{
    // Legacy getters/setters for published properties
    public function getMasterSource(): mixed { return $this->MasterSource; }
    public function setMasterSource(mixed $value): void { $this->MasterSource = $value; }

    public function getMasterFields(): array { return $this->MasterFields; }
    public function setMasterFields(array $value): void { $this->MasterFields = $value; }

    public function getTableName(): string { return $this->_tablename; }
    public function setTableName(string $value): void { $this->TableName = $value; }

    public function getActive(): bool { return $this->Active; }
    public function setActive(bool $value): void { $this->Active = $value; }

    public function getDatabase(): ?\VCL\Database\Connection { return $this->_database; }
    public function setDatabase(mixed $value): void { $this->Database = $value; }

    public function getFilter(): string { return $this->_filter; }
    public function setFilter(string $value): void { $this->Filter = $value; }

    public function getOrderField(): string { return $this->_orderfield; }
    public function setOrderField(string $value): void { $this->OrderField = $value; }

    public function getOrder(): string { return $this->_order; }
    public function setOrder(string $value): void { $this->Order = $value; }

    public function getHasAutoInc(): string { return $this->_hasautoinc; }
    public function setHasAutoInc(string $value): void { $this->HasAutoInc = $value; }

    // Legacy read/write methods for compatibility
    public function readMasterSource(): mixed { return $this->MasterSource; }
    public function writeMasterSource(mixed $value): void { $this->MasterSource = $value; }

    public function readMasterFields(): array { return $this->MasterFields; }
    public function writeMasterFields(array $value): void { $this->MasterFields = $value; }

    public function readTableName(): string { return $this->_tablename; }
    public function writeTableName(string $value): void { $this->TableName = $value; }

    public function readDatabase(): mixed { return $this->_database; }
    public function writeDatabase(mixed $value): void { $this->Database = $value; }

    public function readFilter(): string { return $this->_filter; }
    public function writeFilter(string $value): void { $this->Filter = $value; }

    public function readOrderField(): string { return $this->_orderfield; }
    public function writeOrderField(string $value): void { $this->OrderField = $value; }

    public function readOrder(): string { return $this->_order; }
    public function writeOrder(string $value): void { $this->Order = $value; }

    public function readHasAutoInc(): string { return $this->_hasautoinc; }
    public function writeHasAutoInc(string $value): void { $this->HasAutoInc = $value; }
}
