<?php
/**
 * VCL Database Examples Index
 */

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCL Database Examples</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 900px; margin: 40px auto; padding: 0 20px; line-height: 1.6; }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .examples { display: grid; gap: 20px; margin-top: 30px; }
        .example { background: #f8f9fa; border-radius: 8px; padding: 20px; border-left: 4px solid #007bff; }
        .example h2 { margin: 0 0 10px 0; color: #333; font-size: 1.2em; }
        .example h2 a { color: #007bff; text-decoration: none; }
        .example h2 a:hover { text-decoration: underline; }
        .example p { margin: 0; color: #666; }
        .example .topics { margin-top: 10px; }
        .example .topics span { display: inline-block; background: #e9ecef; padding: 2px 8px; border-radius: 4px; font-size: 0.85em; margin: 2px; }
        .back { margin-top: 30px; }
        .back a { color: #007bff; text-decoration: none; }
        .back a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>VCL Database Examples</h1>
    <p>These examples demonstrate the VCL Database layer, which is built on <a href="https://www.doctrine-project.org/projects/dbal.html" target="_blank">Doctrine DBAL</a>.</p>

    <div class="examples">
        <div class="example">
            <h2><a href="01_connection.php">01. Connection</a></h2>
            <p>Database connection setup using Connection and ConnectionFactory.</p>
            <div class="topics">
                <span>ConnectionFactory</span>
                <span>DriverType</span>
                <span>Open/Close</span>
                <span>Error Handling</span>
            </div>
        </div>

        <div class="example">
            <h2><a href="02_query.php">02. Query</a></h2>
            <p>Using the Query component for SQL queries and result navigation.</p>
            <div class="topics">
                <span>SQL Execution</span>
                <span>Parameters</span>
                <span>Navigation</span>
                <span>Field Access</span>
            </div>
        </div>

        <div class="example">
            <h2><a href="03_table.php">03. Table</a></h2>
            <p>Using the Table component for CRUD operations and master-detail relationships.</p>
            <div class="topics">
                <span>Insert/Update/Delete</span>
                <span>Filter/Order</span>
                <span>Master-Detail</span>
                <span>Record State</span>
            </div>
        </div>

        <div class="example">
            <h2><a href="04_querybuilder.php">04. QueryBuilder</a></h2>
            <p>Fluent query building with SELECT, INSERT, UPDATE, DELETE.</p>
            <div class="topics">
                <span>Fluent API</span>
                <span>JOINs</span>
                <span>GROUP BY</span>
                <span>Aggregates</span>
            </div>
        </div>

        <div class="example">
            <h2><a href="05_storedproc.php">05. StoredProc</a></h2>
            <p>Executing stored procedures across different database systems.</p>
            <div class="topics">
                <span>MySQL CALL</span>
                <span>PostgreSQL Functions</span>
                <span>Oracle PL/SQL</span>
                <span>Output Variables</span>
            </div>
        </div>

        <div class="example">
            <h2><a href="06_transactions.php">06. Transactions</a></h2>
            <p>Transaction handling for data integrity.</p>
            <div class="topics">
                <span>BeginTrans</span>
                <span>Commit/Rollback</span>
                <span>CompleteTrans</span>
                <span>Error Recovery</span>
            </div>
        </div>

        <div class="example">
            <h2><a href="07_schemamanager.php">07. SchemaManager</a></h2>
            <p>Schema introspection and manipulation.</p>
            <div class="topics">
                <span>Create Tables</span>
                <span>Columns/Indexes</span>
                <span>Introspection</span>
                <span>Alterations</span>
            </div>
        </div>

        <div class="example">
            <h2><a href="08_migrations.php">08. Migrations</a></h2>
            <p>Versioned schema changes with up/down migrations.</p>
            <div class="topics">
                <span>Migration Manager</span>
                <span>Up/Down</span>
                <span>Rollback</span>
                <span>Generator</span>
            </div>
        </div>
    </div>

    <div class="back">
        <a href="../">&larr; Back to All Examples</a>
    </div>
</body>
</html>
