<?php

declare(strict_types=1);

namespace VCL\Database\MySQL;

/**
 * MySQLQuery represents a dataset with a result set that is based on an SQL statement.
 *
 * Use MySQLQuery to access one or more tables in a MySQL database using SQL statements.
 *
 * Query components are useful because they can:
 * - Access more than one table at a time (called a "join" in SQL).
 * - Automatically access a subset of rows and columns in its underlying table(s),
 *   rather than always returning all rows and columns.
 *
 * PHP 8.4 version with Property Hooks.
 */
class MySQLQuery extends CustomMySQLQuery
{
    // Legacy getters/setters for published properties
    public function getSQL(): array|string { return $this->readSQL(); }
    public function setSQL(array|string $value): void { $this->writeSQL($value); }

    public function getParams(): array { return $this->readParams(); }
    public function setParams(array $value): void { $this->writeParams($value); }

    public function getTableName(): string { return $this->readTableName(); }
    public function setTableName(string $value): void { $this->writeTableName($value); }

    public function getActive(): bool { return $this->readActive(); }
    public function setActive(bool $value): void { $this->writeActive($value); }

    public function getDatabase(): mixed { return $this->readDatabase(); }
    public function setDatabase(mixed $value): void { $this->writeDatabase($value); }

    public function getFilter(): string { return $this->readFilter(); }
    public function setFilter(string $value): void { $this->writeFilter($value); }

    public function getOrderField(): string { return $this->readOrderField(); }
    public function setOrderField(string $value): void { $this->writeOrderField($value); }

    public function getOrder(): string { return $this->readOrder(); }
    public function setOrder(string $value): void { $this->writeOrder($value); }
}
