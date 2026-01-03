<?php

declare(strict_types=1);

namespace VCL\Database\MySQL;

/**
 * MySQLStoredProc encapsulates a stored procedure in an application.
 *
 * Use a MySQLStoredProc object in applications to use a stored procedure on a MySQL database server.
 * A stored procedure is a grouped set of statements, stored as part of a database server's
 * metadata (just like tables, indexes, and domains), that performs a frequently repeated,
 * database-related task on the server and passes results to the client.
 *
 * Note: Not all MySQL versions support stored procedures. See a specific server's
 * documentation to determine if it supports stored procedures.
 *
 * Many stored procedures require a series of input arguments, or parameters, that are used
 * during processing. MySQLStoredProc provides a Params property that enables an application
 * to set these parameters before executing the stored procedure.
 *
 * PHP 8.4 version with Property Hooks.
 */
class MySQLStoredProc extends CustomMySQLQuery
{
    protected string $_storedprocname = '';
    protected string $_fetchquery = '';

    // Property Hooks
    public string $StoredProcName {
        get => $this->_storedprocname;
        set => $this->_storedprocname = $value;
    }

    public string $FetchQuery {
        get => $this->_fetchquery;
        set => $this->_fetchquery = $value;
    }

    /**
     * Prepares the stored procedure for execution.
     */
    public function Prepare(): void
    {
        if ($this->_database !== null && method_exists($this->_database, 'PrepareSP')) {
            $this->_database->PrepareSP($this->buildQuery());
        }
    }

    /**
     * Build the query to send to the server.
     *
     * Now uses prepared statement placeholders for security.
     *
     * @return string The query with ? placeholders
     */
    protected function buildQuery(): string
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return '';
        }

        $paramCount = count($this->_params);

        if ($paramCount > 0) {
            $placeholders = implode(', ', array_fill(0, $paramCount, '?'));
            $pars = "({$placeholders})";
        } else {
            $pars = '';
        }

        // Validate stored procedure name (alphanumeric, underscore, dot only)
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $this->_storedprocname)) {
            throw new \VCL\Database\EDatabaseError("Invalid stored procedure name: {$this->_storedprocname}");
        }

        $result = "CALL {$this->_storedprocname}{$pars}";

        // Add fetch query if specified (should be a simple SELECT without user input)
        if ($this->_fetchquery !== '') {
            $result .= ";{$this->_fetchquery}";
        }

        return $result;
    }

    /**
     * Get the parameters for prepared statement binding.
     *
     * @return array The parameter values
     */
    public function getBindParams(): array
    {
        return array_values($this->_params);
    }

    /**
     * Execute the stored procedure using prepared statements.
     *
     * @return \mysqli_result|bool
     */
    public function executePrepared(): \mysqli_result|bool
    {
        if ($this->_database === null) {
            throw new \VCL\Database\EDatabaseError("No database connection");
        }

        $query = $this->buildQuery();
        $params = $this->getBindParams();

        return $this->_database->Execute($query, $params);
    }

    // Legacy getters/setters
    public function getStoredProcName(): string { return $this->_storedprocname; }
    public function setStoredProcName(string $value): void { $this->StoredProcName = $value; }
    public function defaultStoredProcName(): string { return ''; }

    public function getFetchQuery(): string { return $this->_fetchquery; }
    public function setFetchQuery(string $value): void { $this->FetchQuery = $value; }
    public function defaultFetchQuery(): string { return ''; }

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

    public function getParams(): array { return $this->readParams(); }
    public function setParams(array $value): void { $this->writeParams($value); }
}
