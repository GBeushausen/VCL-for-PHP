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

use VCL\Database\Query;

/**
 * MySQLQuery represents a dataset with a result set based on an SQL statement.
 *
 * This class extends the DBAL-based Query class for MySQL-specific use cases.
 * It exists primarily for backward compatibility.
 *
 * New code should use VCL\Database\Query directly.
 *
 * Example:
 * ```php
 * // Legacy way (still works)
 * $query = new MySQLQuery();
 * $query->Database = $mysqlDb;
 * $query->SQL = ['SELECT * FROM users WHERE status = ?'];
 * $query->Params = ['active'];
 * $query->Active = true;
 *
 * // Modern way (recommended)
 * use VCL\Database\Query;
 * $query = new Query();
 * $query->Database = $connection;
 * $query->SQL = ['SELECT * FROM users WHERE status = ?'];
 * $query->Params = ['active'];
 * $query->Active = true;
 * ```
 */
class MySQLQuery extends Query
{
    // Legacy getters/setters for published properties
    public function getSQL(): array { return $this->_sql; }
    public function setSQL(array|string $value): void { $this->SQL = is_array($value) ? $value : [$value]; }

    public function getParams(): array { return $this->_params; }
    public function setParams(array $value): void { $this->Params = $value; }

    public function getTableName(): string { return ''; }
    public function setTableName(string $value): void { /* Not used in Query */ }

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

    // Legacy read/write methods for compatibility
    public function readSQL(): array { return $this->_sql; }
    public function writeSQL(array|string $value): void { $this->SQL = is_array($value) ? $value : [$value]; }

    public function readParams(): array { return $this->_params; }
    public function writeParams(array $value): void { $this->Params = $value; }

    public function readTableName(): string { return ''; }
    public function writeTableName(string $value): void { /* Not used in Query */ }

    public function readDatabase(): mixed { return $this->_database; }
    public function writeDatabase(mixed $value): void { $this->Database = $value; }

    public function readFilter(): string { return $this->_filter; }
    public function writeFilter(string $value): void { $this->Filter = $value; }

    public function readOrderField(): string { return $this->_orderfield; }
    public function writeOrderField(string $value): void { $this->OrderField = $value; }

    public function readOrder(): string { return $this->_order; }
    public function writeOrder(string $value): void { $this->Order = $value; }
}
