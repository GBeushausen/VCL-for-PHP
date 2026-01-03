<?php
/**
 * VCL Database Example: QueryBuilder
 *
 * This example demonstrates how to use the QueryBuilder
 * for constructing SQL queries using a fluent interface.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use VCL\Database\ConnectionFactory;
use VCL\Database\QueryBuilder;

// Create an in-memory SQLite database for this demo
$db = ConnectionFactory::SQLite(':memory:');
$db->Open();

// Create sample tables
$db->ExecuteStatement("
    CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        email TEXT NOT NULL,
        role TEXT DEFAULT 'user',
        status TEXT DEFAULT 'active'
    )
");

$db->ExecuteStatement("
    CREATE TABLE orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        total REAL,
        status TEXT DEFAULT 'pending'
    )
");

// Insert sample data
$db->ExecuteStatement("INSERT INTO users (username, email, role, status) VALUES ('alice', 'alice@example.com', 'admin', 'active')");
$db->ExecuteStatement("INSERT INTO users (username, email, role, status) VALUES ('bob', 'bob@example.com', 'user', 'active')");
$db->ExecuteStatement("INSERT INTO users (username, email, role, status) VALUES ('charlie', 'charlie@example.com', 'user', 'inactive')");
$db->ExecuteStatement("INSERT INTO users (username, email, role, status) VALUES ('diana', 'diana@example.com', 'moderator', 'active')");

$db->ExecuteStatement("INSERT INTO orders (user_id, total, status) VALUES (1, 150.00, 'completed')");
$db->ExecuteStatement("INSERT INTO orders (user_id, total, status) VALUES (1, 75.50, 'pending')");
$db->ExecuteStatement("INSERT INTO orders (user_id, total, status) VALUES (2, 200.00, 'completed')");
$db->ExecuteStatement("INSERT INTO orders (user_id, total, status) VALUES (2, 50.00, 'cancelled')");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCL Database Example: QueryBuilder</title>
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
        .sql { background: #1e1e1e; color: #9cdcfe; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>VCL Database Example: QueryBuilder</h1>
    <p>This example demonstrates how to use the QueryBuilder for constructing SQL queries using a fluent interface.</p>

    <h2>Basic SELECT</h2>
    <div class="section">
<?php
$qb = new QueryBuilder($db);

$users = $qb
    ->Select('id', 'username', 'email')
    ->From('users')
    ->FetchAll();

echo "<table>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th></tr>";
foreach ($users as $user) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars((string)$user['id']) . "</td>";
    echo "<td>" . htmlspecialchars((string)$user['username']) . "</td>";
    echo "<td>" . htmlspecialchars((string)$user['email']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
        <pre>&lt;?php
$qb = new QueryBuilder($db);
$users = $qb
    ->Select('id', 'username', 'email')
    ->From('users')
    ->FetchAll();</pre>
    </div>

    <h2>SELECT with WHERE</h2>
    <div class="section">
<?php
$qb->Reset();

$activeUsers = $qb
    ->Select('*')
    ->From('users')
    ->Where('status', '=', 'active')
    ->FetchAll();

echo "<p><strong>Active users:</strong></p><ul>";
foreach ($activeUsers as $user) {
    echo "<li>" . htmlspecialchars((string)$user['username']) . "</li>";
}
echo "</ul>";
?>
        <pre>&lt;?php
$users = $qb
    ->Select('*')
    ->From('users')
    ->Where('status', '=', 'active')
    ->FetchAll();</pre>
    </div>

    <h2>Multiple WHERE Conditions</h2>
    <div class="section">
<?php
$qb->Reset();

$result = $qb
    ->Select('username', 'role')
    ->From('users')
    ->Where('status', '=', 'active')
    ->AndWhere('role', 'IN', ['admin', 'moderator'])
    ->FetchAll();

echo "<p><strong>Active admins/moderators:</strong></p><ul>";
foreach ($result as $user) {
    echo "<li>" . htmlspecialchars((string)$user['username']) . " (" . htmlspecialchars((string)$user['role']) . ")</li>";
}
echo "</ul>";
?>
        <pre>&lt;?php
$users = $qb
    ->Select('username', 'role')
    ->From('users')
    ->Where('status', '=', 'active')
    ->AndWhere('role', 'IN', ['admin', 'moderator'])
    ->FetchAll();</pre>
    </div>

    <h2>ORDER BY</h2>
    <div class="section">
<?php
$qb->Reset();

$result = $qb
    ->Select('username', 'role')
    ->From('users')
    ->OrderBy('role', 'ASC')
    ->AddOrderBy('username', 'DESC')
    ->FetchAll();

echo "<table>";
echo "<tr><th>Role</th><th>Username</th></tr>";
foreach ($result as $user) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars((string)$user['role']) . "</td>";
    echo "<td>" . htmlspecialchars((string)$user['username']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
        <pre>&lt;?php
$users = $qb
    ->Select('username', 'role')
    ->From('users')
    ->OrderBy('role', 'ASC')
    ->AddOrderBy('username', 'DESC')
    ->FetchAll();</pre>
    </div>

    <h2>LIMIT and OFFSET</h2>
    <div class="section">
<?php
$qb->Reset();

$result = $qb
    ->Select('username')
    ->From('users')
    ->OrderBy('username')
    ->Limit(2)
    ->Offset(1)
    ->FetchAll();

echo "<p>2 users, starting from offset 1:</p><ul>";
foreach ($result as $user) {
    echo "<li>" . htmlspecialchars((string)$user['username']) . "</li>";
}
echo "</ul>";
?>
        <pre>&lt;?php
$users = $qb
    ->Select('username')
    ->From('users')
    ->Limit(2)
    ->Offset(1)
    ->FetchAll();</pre>
    </div>

    <h2>JOIN Query</h2>
    <div class="section">
<?php
$qb->Reset();

$result = $qb
    ->Select('u.username', 'o.total', 'o.status AS order_status')
    ->From('users', 'u')
    ->LeftJoin('orders', 'o', 'u.id = o.user_id')
    ->Where('o.status', '=', 'completed')
    ->FetchAll();

echo "<table>";
echo "<tr><th>Username</th><th>Order Total</th><th>Status</th></tr>";
foreach ($result as $row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars((string)$row['username']) . "</td>";
    echo "<td>$" . number_format((float)$row['total'], 2) . "</td>";
    echo "<td>" . htmlspecialchars((string)$row['order_status']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
        <pre>&lt;?php
$result = $qb
    ->Select('u.username', 'o.total', 'o.status AS order_status')
    ->From('users', 'u')
    ->LeftJoin('orders', 'o', 'u.id = o.user_id')
    ->Where('o.status', '=', 'completed')
    ->FetchAll();</pre>
    </div>

    <h2>GROUP BY with Aggregates</h2>
    <div class="section">
<?php
$qb->Reset();

$result = $qb
    ->Select('u.username', 'COUNT(o.id) AS order_count', 'SUM(o.total) AS total_spent')
    ->From('users', 'u')
    ->LeftJoin('orders', 'o', 'u.id = o.user_id')
    ->GroupBy('u.id', 'u.username')
    ->Having('COUNT(o.id) > 0')
    ->FetchAll();

echo "<table>";
echo "<tr><th>Username</th><th>Orders</th><th>Total Spent</th></tr>";
foreach ($result as $row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars((string)$row['username']) . "</td>";
    echo "<td>" . htmlspecialchars((string)$row['order_count']) . "</td>";
    echo "<td>$" . number_format((float)$row['total_spent'], 2) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
        <pre>&lt;?php
$result = $qb
    ->Select('u.username', 'COUNT(o.id) AS order_count', 'SUM(o.total) AS total_spent')
    ->From('users', 'u')
    ->LeftJoin('orders', 'o', 'u.id = o.user_id')
    ->GroupBy('u.id', 'u.username')
    ->Having('COUNT(o.id) > 0')
    ->FetchAll();</pre>
    </div>

    <h2>Fetch Methods</h2>
    <div class="section">
<?php
$qb->Reset();
$all = $qb->Select('*')->From('users')->FetchAll();

$qb->Reset();
$one = $qb->Select('*')->From('users')->Where('id', '=', 1)->FetchOne();

$qb->Reset();
$usernames = $qb->Select('username')->From('users')->FetchColumn();

$qb->Reset();
$count = $qb->Select('COUNT(*)')->From('users')->FetchScalar();

echo "<table>";
echo "<tr><th>Method</th><th>Result</th></tr>";
echo "<tr><td><code>FetchAll()</code></td><td>" . count($all) . " rows</td></tr>";
echo "<tr><td><code>FetchOne()</code></td><td>" . ($one ? htmlspecialchars((string)$one['username']) : 'null') . "</td></tr>";
echo "<tr><td><code>FetchColumn()</code></td><td>" . htmlspecialchars(implode(', ', $usernames)) . "</td></tr>";
echo "<tr><td><code>FetchScalar()</code></td><td>" . htmlspecialchars((string)$count) . " users</td></tr>";
echo "</table>";
?>
    </div>

    <h2>INSERT</h2>
    <div class="section">
<?php
$qb->Reset();

$affected = $qb
    ->Insert('users')
    ->SetValues([
        'username' => 'eve',
        'email' => 'eve@example.com',
        'role' => 'user',
        'status' => 'pending'
    ])
    ->ExecuteStatement();

echo "<p class='success'>Inserted <strong>" . $affected . "</strong> row(s)</p>";
echo "<p>Last insert ID: <code>" . $db->LastInsertId() . "</code></p>";
?>
        <pre>&lt;?php
$affected = $qb
    ->Insert('users')
    ->SetValues([
        'username' => 'eve',
        'email' => 'eve@example.com',
        'role' => 'user',
        'status' => 'pending'
    ])
    ->ExecuteStatement();</pre>
    </div>

    <h2>UPDATE</h2>
    <div class="section">
<?php
$qb->Reset();

$affected = $qb
    ->Update('users')
    ->Set('status', 'active')
    ->Where('username', '=', 'eve')
    ->ExecuteStatement();

echo "<p class='success'>Updated <strong>" . $affected . "</strong> row(s)</p>";
?>
        <pre>&lt;?php
$affected = $qb
    ->Update('users')
    ->Set('status', 'active')
    ->Where('username', '=', 'eve')
    ->ExecuteStatement();</pre>
    </div>

    <h2>DELETE</h2>
    <div class="section">
<?php
$qb->Reset();

$affected = $qb
    ->Delete('users')
    ->Where('username', '=', 'eve')
    ->ExecuteStatement();

echo "<p class='error'>Deleted <strong>" . $affected . "</strong> row(s)</p>";
?>
        <pre>&lt;?php
$affected = $qb
    ->Delete('users')
    ->Where('username', '=', 'eve')
    ->ExecuteStatement();</pre>
    </div>

    <h2>View Generated SQL</h2>
    <div class="section">
<?php
$qb->Reset();

$qb
    ->Select('u.username', 'COUNT(o.id) AS orders')
    ->From('users', 'u')
    ->LeftJoin('orders', 'o', 'u.id = o.user_id')
    ->Where('u.status', '=', 'active')
    ->GroupBy('u.id', 'u.username')
    ->Having('COUNT(o.id) > 0')
    ->OrderBy('orders', 'DESC')
    ->Limit(10);

echo "<p><strong>Generated SQL:</strong></p>";
echo "<div class='sql'>" . htmlspecialchars($qb->GetSQL()) . "</div>";
echo "<p><strong>Parameters:</strong> <code>" . htmlspecialchars(json_encode($qb->GetParameters())) . "</code></p>";
?>
    </div>

<?php $db->Close(); ?>

    <p><a href="index.php">&larr; Back to Examples</a></p>
</body>
</html>
