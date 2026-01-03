<?php

declare(strict_types=1);

namespace VCL\Database\MySQL;

/**
 * CustomMySQLQuery is the base class for MySQLQuery.
 *
 * Query components are useful because they can:
 * - Access more than one table at a time (called a "join" in SQL).
 * - Automatically access a subset of rows and columns in its underlying table(s),
 *   rather than always returning all rows and columns.
 *
 * PHP 8.4 version with Property Hooks.
 */
class CustomMySQLQuery extends CustomMySQLTable
{
    protected array|string $_sql = [];

    // Property Hooks
    public array|string $SQL {
        get => $this->_sql;
        set {
            if (!is_array($value)) {
                // Check for a JSON-encoded array (safe alternative to unserialize)
                $decoded = @json_decode($value, true);
                if (is_array($decoded)) {
                    $this->_sql = $decoded;
                } else {
                    $this->_sql = $value;
                }
            } else {
                $this->_sql = $value;
            }
        }
    }

    /**
     * Sends a query to the server for optimization prior to execution.
     *
     * Call Prepare to have a remote database server allocate resources for
     * the query and to perform additional optimizations.
     *
     * If the query will only be executed once, the application does not need
     * to explicitly call Prepare. Executing an unprepared query generates
     * these calls automatically. However, if the same query is to be executed
     * repeatedly, it is more efficient to prevent these automatic calls by
     * calling Prepare explicitly.
     */
    public function Prepare(): void
    {
        if ($this->_database !== null) {
            $this->_database->Prepare($this->buildQuery());
        }
    }

    /**
     * Build the query to send to the server.
     */
    protected function buildQuery(): string
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return '';
        }

        $qu = '';

        if (is_array($this->_sql)) {
            if (!empty($this->_sql)) {
                $qu = implode(' ', $this->_sql);
            }
        } else {
            if ($this->_sql !== '') {
                $qu = $this->_sql;
            }
        }

        $order = '';
        if ($this->_orderfield !== '') {
            $order = "ORDER BY {$this->_orderfield} {$this->_order}";
        }

        $filter = '';
        if ($this->_filter !== '') {
            $filter = " WHERE {$this->_filter} ";
        }

        $result = "{$qu} {$filter} {$order}";

        return $result;
    }

    // Legacy getters/setters
    public function readSQL(): array|string { return $this->_sql; }
    public function writeSQL(array|string $value): void { $this->SQL = $value; }
    public function defaultSQL(): array { return []; }
}
