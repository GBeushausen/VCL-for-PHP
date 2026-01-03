<?php
/**
 * VCL Database Example: Query Component
 *
 * This example demonstrates how to use the Query component
 * for executing SQL queries and navigating result sets.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use VCL\Database\ConnectionFactory;
use VCL\Database\Query;

// Create an in-memory SQLite database for this demo
$db = ConnectionFactory::SQLite(':memory:');
$db->Open();

// Create sample table and data
$db->ExecuteStatement("
    CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        email TEXT NOT NULL,
        status TEXT DEFAULT 'active',
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )
");

$db->ExecuteStatement("INSERT INTO users (username, email, status) VALUES ('alice', 'alice@example.com', 'active')");
$db->ExecuteStatement("INSERT INTO users (username, email, status) VALUES ('bob', 'bob@example.com', 'active')");
$db->ExecuteStatement("INSERT INTO users (username, email, status) VALUES ('charlie', 'charlie@example.com', 'inactive')");
$db->ExecuteStatement("INSERT INTO users (username, email, status) VALUES ('diana', 'diana@example.com', 'active')");
$db->ExecuteStatement("INSERT INTO users (username, email, status) VALUES ('eve', 'eve@example.com', 'pending')");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCL Database Example: Query</title>
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
        ul { margin: 10px 0; }
        li { margin: 5px 0; }
    </style>
</head>
<body>
    <h1>VCL Database Example: Query</h1>
    <p>This example demonstrates how to use the Query component for executing SQL queries and navigating result sets.</p>

    <h2>Basic Query</h2>
    <div class="section">
<?php
$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT * FROM users'];
$query->Open();

echo "<table>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Status</th></tr>";
while (!$query->EOF()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars((string)$query->id) . "</td>";
    echo "<td>" . htmlspecialchars((string)$query->username) . "</td>";
    echo "<td>" . htmlspecialchars((string)$query->email) . "</td>";
    echo "<td>" . htmlspecialchars((string)$query->status) . "</td>";
    echo "</tr>";
    $query->Next();
}
echo "</table>";
$query->Close();
?>
        <pre>&lt;?php
$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT * FROM users'];
$query->Open();

while (!$query->EOF()) {
    echo $query->username . " - " . $query->email;
    $query->Next();
}
$query->Close();</pre>
    </div>

    <h2>Query with Parameters</h2>
    <div class="section">
<?php
$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT * FROM users WHERE status = ?'];
$query->Params = ['active'];
$query->Open();

echo "<p><strong>Active users:</strong></p><ul>";
while (!$query->EOF()) {
    echo "<li>" . htmlspecialchars((string)$query->username) . "</li>";
    $query->Next();
}
echo "</ul>";
$query->Close();
?>
        <pre>&lt;?php
$query->SQL = ['SELECT * FROM users WHERE status = ?'];
$query->Params = ['active'];
$query->Open();</pre>
    </div>

    <h2>Using Filter Property</h2>
    <div class="section">
<?php
$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT * FROM users'];
$query->Filter = "status = 'active'";
$query->Open();

echo "<p>Filter: <code>status = 'active'</code></p>";
echo "<p><strong>Filtered users:</strong></p><ul>";
while (!$query->EOF()) {
    echo "<li>" . htmlspecialchars((string)$query->username) . "</li>";
    $query->Next();
}
echo "</ul>";
$query->Close();
?>
        <pre>&lt;?php
$query->SQL = ['SELECT * FROM users'];
$query->Filter = "status = 'active'";
$query->Open();</pre>
    </div>

    <h2>Ordering Results</h2>
    <div class="section">
<?php
$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT * FROM users'];
$query->OrderField = 'username';
$query->Order = 'DESC';
$query->Open();

echo "<p>Order: <code>username DESC</code></p>";
echo "<p><strong>Users:</strong></p><ul>";
while (!$query->EOF()) {
    echo "<li>" . htmlspecialchars((string)$query->username) . "</li>";
    $query->Next();
}
echo "</ul>";
$query->Close();
?>
        <pre>&lt;?php
$query->OrderField = 'username';
$query->Order = 'DESC';
$query->Open();</pre>
    </div>

    <h2>Limiting Results</h2>
    <div class="section">
<?php
$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT * FROM users ORDER BY id'];
$query->LimitStart = 1;
$query->LimitCount = 2;
$query->Open();

echo "<p>Offset: <code>1</code>, Limit: <code>2</code></p>";
echo "<p><strong>Users:</strong></p><ul>";
while (!$query->EOF()) {
    echo "<li>" . htmlspecialchars((string)$query->username) . "</li>";
    $query->Next();
}
echo "</ul>";
$query->Close();
?>
        <pre>&lt;?php
$query->LimitStart = 1;  // Skip first record
$query->LimitCount = 2;  // Get 2 records
$query->Open();</pre>
    </div>

    <h2>Navigation Methods</h2>
    <div class="section">
<?php
$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT * FROM users ORDER BY id'];
$query->Open();

echo "<table>";
echo "<tr><th>Method</th><th>Result</th></tr>";
echo "<tr><td><code>First()</code></td><td>" . htmlspecialchars((string)$query->username) . "</td></tr>";

$query->Last();
echo "<tr><td><code>Last()</code></td><td>" . htmlspecialchars((string)$query->username) . "</td></tr>";

$query->First();
$query->Next();
echo "<tr><td><code>First()</code> then <code>Next()</code></td><td>" . htmlspecialchars((string)$query->username) . "</td></tr>";

$query->Prior();
echo "<tr><td><code>Prior()</code></td><td>" . htmlspecialchars((string)$query->username) . "</td></tr>";

$query->MoveTo(2);
echo "<tr><td><code>MoveTo(2)</code></td><td>" . htmlspecialchars((string)$query->username) . "</td></tr>";

$query->MoveBy(2);
echo "<tr><td><code>MoveBy(2)</code></td><td>" . htmlspecialchars((string)$query->username) . "</td></tr>";
echo "</table>";

echo "<p><strong>Record count:</strong> " . $query->ReadRecordCount() . "</p>";
echo "<p><strong>Current position:</strong> " . $query->RecNo . "</p>";

$query->Close();
?>
    </div>

    <h2>Field Access Methods</h2>
    <div class="section">
<?php
$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT * FROM users WHERE id = ?'];
$query->Params = [1];
$query->Open();

echo "<table>";
echo "<tr><th>Method</th><th>Result</th></tr>";
echo "<tr><td>Property access: <code>\$query->username</code></td><td>" . htmlspecialchars((string)$query->username) . "</td></tr>";
echo "<tr><td>Fields array: <code>\$query->Fields['email']</code></td><td>" . htmlspecialchars((string)$query->Fields['email']) . "</td></tr>";
echo "<tr><td>FieldByName: <code>\$query->FieldByName('status')</code></td><td>" . htmlspecialchars((string)$query->FieldByName('status')) . "</td></tr>";
echo "<tr><td><code>\$query->FieldNames()</code></td><td>" . htmlspecialchars(implode(', ', $query->FieldNames())) . "</td></tr>";
echo "</table>";
$query->Close();
?>
    </div>

    <h2>Using Active Property</h2>
    <div class="section">
<?php
$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT COUNT(*) as total FROM users'];
$query->Active = true;

echo "<p>Setting <code>\$query->Active = true</code> opens the query.</p>";
echo "<p><strong>Total users:</strong> " . $query->total . "</p>";

$query->Active = false;
echo "<p>Setting <code>\$query->Active = false</code> closes the query.</p>";
?>
        <pre>&lt;?php
$query->SQL = ['SELECT COUNT(*) as total FROM users'];
$query->Active = true;  // Opens the query
echo $query->total;
$query->Active = false; // Closes the query</pre>
    </div>

<?php $db->Close(); ?>

    <p><a href="index.php">&larr; Back to Examples</a></p>
</body>
</html>
