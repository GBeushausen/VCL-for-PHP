<?php

declare(strict_types=1);

namespace VCL\Auth;

/**
 * DatabaseUser can be used to authenticate a user against a database table.
 *
 * Use this class to authenticate a user against a database, in which are stored all
 * user details, like username and password.
 *
 * To make it work, set DriverName, Host, User, Password, FieldName and TableName to
 * allow the component to find the information to authenticate.
 *
 * PHP 8.4 version with Property Hooks.
 */
class DatabaseUser extends User
{
    protected string $_drivername = '';
    protected string $_databasename = '';
    protected string $_host = '';
    protected string $_user = '';
    protected string $_password = '';
    protected string $_userstable = '';
    protected string $_usernamefieldname = '';
    protected string $_passwordfieldname = '';

    // Property Hooks
    public string $DriverName {
        get => $this->_drivername;
        set => $this->_drivername = $value;
    }

    public string $DatabaseName {
        get => $this->_databasename;
        set => $this->_databasename = $value;
    }

    public string $Host {
        get => $this->_host;
        set => $this->_host = $value;
    }

    public string $User {
        get => $this->_user;
        set => $this->_user = $value;
    }

    public string $Password {
        get => $this->_password;
        set => $this->_password = $value;
    }

    public string $UsersTable {
        get => $this->_userstable;
        set => $this->_userstable = $value;
    }

    public string $UserNameFieldName {
        get => $this->_usernamefieldname;
        set => $this->_usernamefieldname = $value;
    }

    public string $PasswordFieldName {
        get => $this->_passwordfieldname;
        set => $this->_passwordfieldname = $value;
    }

    /**
     * Authenticate a user against the database table.
     *
     * After calling this method, check Logged property to know
     * if the operation was successful or not.
     *
     * @param string $username Username to authenticate
     * @param string $password Password of the user
     * @return bool True if authentication was successful
     */
    public function authenticate(string $username, string $password): bool
    {
        $this->Logged = false;

        // Build DSN based on driver
        $dsn = $this->buildDsn();
        if ($dsn === '') {
            return false;
        }

        try {
            $pdo = new \PDO($dsn, $this->_user, $this->_password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Prepare query with parameterized statements to prevent SQL injection
            $sql = sprintf(
                'SELECT %s, %s FROM %s WHERE %s = :username LIMIT 1',
                $this->escapeIdentifier($this->_usernamefieldname),
                $this->escapeIdentifier($this->_passwordfieldname),
                $this->escapeIdentifier($this->_userstable),
                $this->escapeIdentifier($this->_usernamefieldname)
            );

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['username' => $username]);

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($row !== false) {
                $storedUsername = $row[$this->_usernamefieldname] ?? '';
                $storedPassword = $row[$this->_passwordfieldname] ?? '';

                if ($storedUsername === $username && $storedPassword === $password) {
                    $this->Logged = true;
                }
            }

            $this->serialize();

        } catch (\PDOException $e) {
            // Log error or handle as needed
            $this->Logged = false;
        }

        return $this->Logged;
    }

    /**
     * Build PDO DSN string based on driver.
     */
    protected function buildDsn(): string
    {
        return match (strtolower($this->_drivername)) {
            'mysql', 'mysqli' => "mysql:host={$this->_host};dbname={$this->_databasename}",
            'pgsql', 'postgresql' => "pgsql:host={$this->_host};dbname={$this->_databasename}",
            'sqlite' => "sqlite:{$this->_databasename}",
            'sqlsrv', 'mssql' => "sqlsrv:Server={$this->_host};Database={$this->_databasename}",
            default => '',
        };
    }

    /**
     * Escape an identifier (table/column name).
     *
     * Note: This is a basic implementation. For production use,
     * consider using driver-specific escaping or an ORM.
     */
    protected function escapeIdentifier(string $identifier): string
    {
        // Only allow alphanumeric and underscore
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
            throw new \InvalidArgumentException("Invalid identifier: {$identifier}");
        }
        return $identifier;
    }

    // Legacy getters/setters
    public function getDriverName(): string { return $this->_drivername; }
    public function setDriverName(string $value): void { $this->DriverName = $value; }

    public function getDatabaseName(): string { return $this->_databasename; }
    public function setDatabaseName(string $value): void { $this->DatabaseName = $value; }

    public function getHost(): string { return $this->_host; }
    public function setHost(string $value): void { $this->Host = $value; }

    public function getUser(): string { return $this->_user; }
    public function setUser(string $value): void { $this->User = $value; }

    public function getPassword(): string { return $this->_password; }
    public function setPassword(string $value): void { $this->Password = $value; }

    public function getUsersTable(): string { return $this->_userstable; }
    public function setUsersTable(string $value): void { $this->UsersTable = $value; }

    public function getUserNameFieldName(): string { return $this->_usernamefieldname; }
    public function setUserNameFieldName(string $value): void { $this->UserNameFieldName = $value; }

    public function getPasswordFieldName(): string { return $this->_passwordfieldname; }
    public function setPasswordFieldName(string $value): void { $this->PasswordFieldName = $value; }
}
