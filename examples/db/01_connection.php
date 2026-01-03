<?php
/**
 * VCL Database Example: Connection
 *
 * This example demonstrates how to establish database connections
 * using VCL's Connection class and ConnectionFactory.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use VCL\Database\Connection;
use VCL\Database\ConnectionFactory;
use VCL\Database\Enums\DriverType;
use VCL\Database\EDatabaseError;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCL Database Example: Connection</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 900px; margin: 40px auto; padding: 0 20px; line-height: 1.6; }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        .section { background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0; }
        code { background: #e9ecef; padding: 2px 6px; border-radius: 4px; font-family: 'Fira Code', monospace; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 8px; overflow-x: auto; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        table { border-collapse: collapse; width: 100%; margin: 15px 0; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        th { background: #e9ecef; }
    </style>
</head>
<body>
    <h1>VCL Database Example: Connection</h1>
    <p>This example demonstrates how to establish database connections using VCL's Connection class and ConnectionFactory.</p>

    <h2>Method 1: Using ConnectionFactory (Recommended)</h2>
    <div class="section">
        <p>The <code>ConnectionFactory</code> provides static methods for quick connection setup:</p>
        <pre>&lt;?php
// MySQL
$mysql = ConnectionFactory::MySQL(
    host: 'localhost',
    database: 'myapp',
    username: 'user',
    password: 'secret'
);

// PostgreSQL
$pgsql = ConnectionFactory::PostgreSQL(
    host: 'localhost',
    database: 'myapp',
    username: 'user',
    password: 'secret'
);

// SQLite (file-based)
$sqlite = ConnectionFactory::SQLite('/path/to/database.sqlite');

// SQLite (in-memory)
$sqliteMemory = ConnectionFactory::SQLite(':memory:');

// SQL Server
$sqlserver = ConnectionFactory::SQLServer(
    host: 'localhost',
    database: 'myapp',
    username: 'sa',
    password: 'secret'
);</pre>
    </div>

    <h2>Method 2: Manual Configuration</h2>
    <div class="section">
        <pre>&lt;?php
$db = new Connection();

// Using DriverType enum (recommended)
$db->Driver = DriverType::MySQL;

// Connection parameters
$db->Host = 'localhost';
$db->DatabaseName = 'myapp';
$db->UserName = 'user';
$db->UserPassword = 'secret';
$db->Port = 3306;
$db->Charset = 'utf8mb4';

// Optional settings
$db->Debug = true;
$db->Persistent = false;</pre>
    </div>

    <h2>Connection Lifecycle</h2>
    <div class="section">
<?php
$db = ConnectionFactory::SQLite(':memory:');

// Method 1: Explicit Open/Close
$db->Open();
echo "<p><strong>Explicit Open/Close:</strong></p>";
echo "<p>After <code>\$db->Open()</code>: Connection opened = <span class='success'>" . ($db->Connected ? 'Yes' : 'No') . "</span></p>";

$db->Close();
echo "<p>After <code>\$db->Close()</code>: Connection opened = <span class='error'>" . ($db->Connected ? 'Yes' : 'No') . "</span></p>";

// Method 2: Using Connected property
echo "<p><strong>Using Connected property:</strong></p>";
$db->Connected = true;
echo "<p>After <code>\$db->Connected = true</code>: <span class='success'>" . ($db->Connected ? 'Connected' : 'Disconnected') . "</span></p>";

$db->Connected = false;
echo "<p>After <code>\$db->Connected = false</code>: <span class='error'>" . ($db->Connected ? 'Connected' : 'Disconnected') . "</span></p>";
?>
    </div>

    <h2>Error Handling</h2>
    <div class="section">
<?php
try {
    $badDb = new Connection();
    $badDb->Driver = DriverType::MySQL;
    $badDb->Host = 'nonexistent-host';
    $badDb->DatabaseName = 'nonexistent';
    $badDb->Open();
} catch (EDatabaseError $e) {
    echo "<p>Attempting to connect to non-existent host...</p>";
    echo "<p class='error'><strong>EDatabaseError caught:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
        <pre>&lt;?php
try {
    $badDb = new Connection();
    $badDb->Driver = DriverType::MySQL;
    $badDb->Host = 'nonexistent-host';
    $badDb->Open();
} catch (EDatabaseError $e) {
    echo "Database error: " . $e->getMessage();
}</pre>
    </div>

    <h2>Accessing the Underlying DBAL Connection</h2>
    <div class="section">
<?php
$db = ConnectionFactory::SQLite(':memory:');
$db->Open();

$dbal = $db->Dbal();
if ($dbal !== null) {
    echo "<p class='success'>DBAL connection available.</p>";
    echo "<p><strong>Platform:</strong> <code>" . htmlspecialchars($dbal->getDatabasePlatform()::class) . "</code></p>";
}

$db->Close();
?>
        <pre>&lt;?php
$dbal = $db->Dbal();
if ($dbal !== null) {
    // Use Doctrine DBAL directly for advanced operations
    $platform = $dbal->getDatabasePlatform();
}</pre>
    </div>

    <h2>Supported Drivers</h2>
    <div class="section">
        <table>
            <tr><th>DriverType</th><th>Database</th><th>Factory Method</th></tr>
            <tr><td><code>DriverType::MySQL</code></td><td>MySQL / MariaDB</td><td><code>ConnectionFactory::MySQL()</code></td></tr>
            <tr><td><code>DriverType::PostgreSQL</code></td><td>PostgreSQL</td><td><code>ConnectionFactory::PostgreSQL()</code></td></tr>
            <tr><td><code>DriverType::SQLite</code></td><td>SQLite</td><td><code>ConnectionFactory::SQLite()</code></td></tr>
            <tr><td><code>DriverType::SQLServer</code></td><td>SQL Server</td><td><code>ConnectionFactory::SQLServer()</code></td></tr>
            <tr><td><code>DriverType::Oracle</code></td><td>Oracle</td><td><code>ConnectionFactory::Oracle()</code></td></tr>
            <tr><td><code>DriverType::IBMDB2</code></td><td>IBM DB2</td><td>-</td></tr>
        </table>
    </div>

    <p><a href="index.php">&larr; Back to Examples</a></p>
</body>
</html>
