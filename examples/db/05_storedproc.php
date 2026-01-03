<?php
/**
 * VCL Database Example: StoredProc Component
 *
 * This example demonstrates how to use the StoredProc component
 * for executing stored procedures across different database systems.
 *
 * Note: SQLite doesn't support stored procedures, so this example
 * shows the SQL that would be generated for different database drivers.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use VCL\Database\Connection;
use VCL\Database\StoredProc;
use VCL\Database\Enums\DriverType;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCL Database Example: StoredProc</title>
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
        .sql { background: #1e1e1e; color: #9cdcfe; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
        .db-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.85em; margin-right: 10px; }
        .mysql { background: #00758f; color: white; }
        .pgsql { background: #336791; color: white; }
        .oracle { background: #f80000; color: white; }
    </style>
</head>
<body>
    <h1>VCL Database Example: StoredProc</h1>
    <p>This example demonstrates how to use the StoredProc component for executing stored procedures across different database systems.</p>

    <h2>SQL Generation by Database Type</h2>
    <div class="section">
        <p>The StoredProc component generates database-specific SQL syntax automatically:</p>

<?php
// MySQL
$mysqlConn = new Connection();
$mysqlConn->Driver = DriverType::MySQL;

$proc = new StoredProc();
$proc->Database = $mysqlConn;
$proc->StoredProcName = 'GetUserById';

echo "<h3><span class='db-badge mysql'>MySQL</span> GetUserById</h3>";
echo "<div class='sql'>" . htmlspecialchars($proc->BuildQuery()) . "</div>";

// PostgreSQL
$pgConn = new Connection();
$pgConn->Driver = DriverType::PostgreSQL;

$proc = new StoredProc();
$proc->Database = $pgConn;
$proc->StoredProcName = 'get_active_users';

echo "<h3><span class='db-badge pgsql'>PostgreSQL</span> get_active_users</h3>";
echo "<div class='sql'>" . htmlspecialchars($proc->BuildQuery()) . "</div>";

// Oracle
$oracleConn = new Connection();
$oracleConn->Driver = DriverType::Oracle;

$proc = new StoredProc();
$proc->Database = $oracleConn;
$proc->StoredProcName = 'PKG_USERS.UPDATE_STATUS';

echo "<h3><span class='db-badge oracle'>Oracle</span> PKG_USERS.UPDATE_STATUS</h3>";
echo "<div class='sql'>" . htmlspecialchars($proc->BuildQuery()) . "</div>";
?>
    </div>

    <h2>MySQL with FetchQuery</h2>
    <div class="section">
<?php
$proc = new StoredProc();
$proc->Database = $mysqlConn;
$proc->StoredProcName = 'CalculateDiscount';
$proc->FetchQuery = 'SELECT @discount_amount, @final_price';

echo "<p>For procedures that set output variables, use <code>FetchQuery</code>:</p>";
echo "<div class='sql'>" . htmlspecialchars($proc->BuildQuery()) . "</div>";
?>
        <pre>&lt;?php
$proc->StoredProcName = 'CalculateDiscount';
$proc->FetchQuery = 'SELECT @discount_amount, @final_price';
$proc->Open();</pre>
    </div>

    <h2>Usage Examples</h2>
    <div class="section">
        <h3>Example 1: Get user by ID (MySQL)</h3>
        <pre>&lt;?php
$proc = new StoredProc();
$proc->Database = $mysqlConnection;
$proc->StoredProcName = 'GetUserById';
$proc->Params = [42];
$proc->Open();

while (!$proc->EOF()) {
    echo $proc->username;
    $proc->Next();
}
$proc->Close();</pre>
    </div>

    <div class="section">
        <h3>Example 2: Execute without result set</h3>
        <pre>&lt;?php
$proc = new StoredProc();
$proc->Database = $mysqlConnection;
$proc->StoredProcName = 'UpdateInventory';
$proc->Params = ['SKU-001', 50];
$proc->ExecuteProc();</pre>
    </div>

    <div class="section">
        <h3>Example 3: Get output variables (MySQL)</h3>
        <pre>&lt;?php
$proc = new StoredProc();
$proc->Database = $mysqlConnection;
$proc->StoredProcName = 'CalculateOrderTotal';
$proc->Params = [1001];
$proc->FetchQuery = 'SELECT @subtotal, @tax, @total';
$proc->Open();

$subtotal = $proc->Fields['@subtotal'];
$tax = $proc->Fields['@tax'];
$total = $proc->Fields['@total'];</pre>
    </div>

    <div class="section">
        <h3>Example 4: PostgreSQL function</h3>
        <pre>&lt;?php
$proc = new StoredProc();
$proc->Database = $pgConnection;
$proc->StoredProcName = 'search_products';
$proc->Params = ['laptop', 100, 1000];
$proc->Open();

// Results from: SELECT * FROM search_products('laptop', 100, 1000)
while (!$proc->EOF()) {
    echo $proc->product_name . ": $" . $proc->price;
    $proc->Next();
}</pre>
    </div>

    <div class="section">
        <h3>Example 5: Oracle package procedure</h3>
        <pre>&lt;?php
$proc = new StoredProc();
$proc->Database = $oracleConnection;
$proc->StoredProcName = 'HR_PKG.GIVE_RAISE';
$proc->Params = [101, 5.5];  // employee_id, percent
$proc->ExecuteProc();
// Executes: BEGIN HR_PKG.GIVE_RAISE('101', '5.5'); END;</pre>
    </div>

    <h2>StoredProc Properties</h2>
    <div class="section">
        <table>
            <tr><th>Property</th><th>Type</th><th>Description</th></tr>
            <tr><td><code>StoredProcName</code></td><td>string</td><td>Name of the stored procedure to execute</td></tr>
            <tr><td><code>FetchQuery</code></td><td>string</td><td>Additional query after procedure (MySQL only)</td></tr>
            <tr><td><code>Params</code></td><td>array</td><td>Parameters to pass to the procedure</td></tr>
            <tr><td><code>Database</code></td><td>Connection</td><td>Database connection to use</td></tr>
            <tr><td><code>Active</code></td><td>bool</td><td>Opens/closes the procedure result set</td></tr>
        </table>
    </div>

    <h2>StoredProc Methods</h2>
    <div class="section">
        <table>
            <tr><th>Method</th><th>Description</th></tr>
            <tr><td><code>Open()</code></td><td>Execute procedure and fetch results</td></tr>
            <tr><td><code>Close()</code></td><td>Close the result set</td></tr>
            <tr><td><code>ExecuteProc()</code></td><td>Execute procedure without expecting results</td></tr>
            <tr><td><code>BuildQuery()</code></td><td>Get the generated SQL for the procedure call</td></tr>
            <tr><td><code>Prepare()</code></td><td>Prepare the procedure (no-op in DBAL)</td></tr>
        </table>
    </div>

    <p><a href="index.php">&larr; Back to Examples</a></p>
</body>
</html>
