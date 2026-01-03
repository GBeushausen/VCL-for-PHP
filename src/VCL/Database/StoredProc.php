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

/**
 * StoredProc encapsulates a stored procedure in an application.
 *
 * Use a StoredProc object to execute a stored procedure on a database server.
 *
 * Example:
 * ```php
 * $proc = new StoredProc();
 * $proc->Database = $connection;
 * $proc->StoredProcName = 'GetUserById';
 * $proc->Params = [123];
 * $proc->Open();
 * ```
 */
class StoredProc extends Query
{
    protected string $_storedprocname = '';
    protected string $_fetchquery = '';

    protected array $_noFetchDBs = ['oracle', 'oci8'];
    protected array $_useCall = ['mysql', 'mariadb'];

    public string $StoredProcName {
        get => $this->_storedprocname;
        set => $this->_storedprocname = $value;
    }

    public string $FetchQuery {
        get => $this->_fetchquery;
        set => $this->_fetchquery = $value;
    }

    /**
     * Prepare the stored procedure.
     */
    public function Prepare(): void
    {
        // Doctrine DBAL handles this differently
    }

    /**
     * Execute the stored procedure.
     */
    public function ExecuteProc(): void
    {
        if ($this->_database === null) {
            throw new EDatabaseError("No Database assigned");
        }

        $driverName = $this->_database->Driver->value;

        if (in_array($driverName, $this->_noFetchDBs)) {
            $this->_database->Execute($this->BuildQuery());
        } else {
            $this->Close();
            $this->Open();
        }
    }

    /**
     * Build the stored procedure query.
     */
    public function BuildQuery(): string
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return '';
        }

        if ($this->_database === null) {
            return '';
        }

        $pars = '';
        foreach ($this->_params as $val) {
            if ($pars !== '') {
                $pars .= ', ';
            }
            $pars .= $this->_database->QuoteStr((string)$val);
        }

        if ($pars !== '') {
            $pars = "({$pars})";
        }

        $driverName = $this->_database->Driver->value;

        if (in_array($driverName, $this->_noFetchDBs)) {
            return "BEGIN {$this->_storedprocname}{$pars}; END;";
        } elseif (in_array($driverName, $this->_useCall)) {
            $result = "CALL {$this->_storedprocname}{$pars}";
            if ($this->_fetchquery !== '') {
                $result .= "; {$this->_fetchquery}";
            }
            return $result;
        } else {
            return "SELECT * FROM {$this->_storedprocname}{$pars}";
        }
    }

    // Legacy accessors
    public function getStoredProcName(): string { return $this->_storedprocname; }
    public function setStoredProcName(string $value): void { $this->StoredProcName = $value; }
    public function defaultStoredProcName(): string { return ''; }

    public function getFetchQuery(): string { return $this->_fetchquery; }
    public function setFetchQuery(string $value): void { $this->FetchQuery = $value; }
    public function defaultFetchQuery(): string { return ''; }
}
