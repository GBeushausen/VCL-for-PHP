<?php
/**
 * VCL Database Example: Transactions
 *
 * This example demonstrates how to use database transactions
 * for ensuring data integrity during multi-step operations.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use VCL\Database\ConnectionFactory;

// Create an in-memory SQLite database for this demo
$db = ConnectionFactory::SQLite(':memory:');
$db->Open();

// Create sample tables
$db->ExecuteStatement("
    CREATE TABLE accounts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        balance REAL NOT NULL DEFAULT 0
    )
");

$db->ExecuteStatement("
    CREATE TABLE transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        from_account INTEGER,
        to_account INTEGER,
        amount REAL NOT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )
");

// Create initial accounts
$db->ExecuteStatement("INSERT INTO accounts (name, balance) VALUES ('Alice', 1000.00)");
$db->ExecuteStatement("INSERT INTO accounts (name, balance) VALUES ('Bob', 500.00)");
$db->ExecuteStatement("INSERT INTO accounts (name, balance) VALUES ('Charlie', 250.00)");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCL Database Example: Transactions</title>
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
    <h1>VCL Database Example: Transactions</h1>
    <p>This example demonstrates how to use database transactions for ensuring data integrity during multi-step operations.</p>

    <h2>Initial Balances</h2>
    <div class="section">
<?php
$result = $db->Execute("SELECT name, balance FROM accounts");
echo "<table>";
echo "<tr><th>Account</th><th>Balance</th></tr>";
while ($row = $result->fetchAssociative()) {
    echo "<tr><td>" . htmlspecialchars($row['name']) . "</td><td>$" . number_format((float)$row['balance'], 2) . "</td></tr>";
}
echo "</table>";
?>
    </div>

    <h2>Successful Transaction</h2>
    <div class="section">
<?php
function transferMoney($db, int $fromId, int $toId, float $amount): bool
{
    $db->BeginTrans();

    try {
        $db->ExecuteStatement(
            "UPDATE accounts SET balance = balance - ? WHERE id = ?",
            [$amount, $fromId]
        );

        $db->ExecuteStatement(
            "UPDATE accounts SET balance = balance + ? WHERE id = ?",
            [$amount, $toId]
        );

        $db->ExecuteStatement(
            "INSERT INTO transactions (from_account, to_account, amount) VALUES (?, ?, ?)",
            [$fromId, $toId, $amount]
        );

        $db->Commit();
        return true;

    } catch (\Throwable $e) {
        $db->Rollback();
        return false;
    }
}

$success = transferMoney($db, 1, 2, 200.00);
echo "<p>Transfer $200 from Alice to Bob: ";
echo $success ? "<span class='success'>Success</span>" : "<span class='error'>Failed</span>";
echo "</p>";

echo "<p><strong>Balances after transfer:</strong></p>";
$result = $db->Execute("SELECT name, balance FROM accounts");
echo "<table>";
echo "<tr><th>Account</th><th>Balance</th></tr>";
while ($row = $result->fetchAssociative()) {
    echo "<tr><td>" . htmlspecialchars($row['name']) . "</td><td>$" . number_format((float)$row['balance'], 2) . "</td></tr>";
}
echo "</table>";
?>
        <pre>&lt;?php
$db->BeginTrans();

try {
    $db->ExecuteStatement("UPDATE accounts SET balance = balance - ? WHERE id = ?", [$amount, $fromId]);
    $db->ExecuteStatement("UPDATE accounts SET balance = balance + ? WHERE id = ?", [$amount, $toId]);
    $db->ExecuteStatement("INSERT INTO transactions (...) VALUES (?, ?, ?)", [...]);

    $db->Commit();
} catch (\Throwable $e) {
    $db->Rollback();
}</pre>
    </div>

    <h2>Transaction with Rollback</h2>
    <div class="section">
<?php
function transferMoneyWithValidation($db, int $fromId, int $toId, float $amount): array
{
    $db->BeginTrans();

    try {
        $result = $db->Execute("SELECT balance FROM accounts WHERE id = ?", [$fromId]);
        $sender = $result->fetchAssociative();

        if (!$sender || $sender['balance'] < $amount) {
            throw new \RuntimeException("Insufficient funds");
        }

        $db->ExecuteStatement(
            "UPDATE accounts SET balance = balance - ? WHERE id = ?",
            [$amount, $fromId]
        );

        $db->ExecuteStatement(
            "UPDATE accounts SET balance = balance + ? WHERE id = ?",
            [$amount, $toId]
        );

        $db->Commit();
        return ['success' => true, 'error' => null];

    } catch (\Throwable $e) {
        $db->Rollback();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

$result = transferMoneyWithValidation($db, 3, 1, 1000.00);
echo "<p>Transfer $1000 from Charlie to Alice (Charlie only has $250):</p>";
if ($result['success']) {
    echo "<p class='success'>Success</p>";
} else {
    echo "<p class='error'>Failed: " . htmlspecialchars($result['error']) . "</p>";
    echo "<p class='info'>Transaction rolled back - no changes made.</p>";
}

echo "<p><strong>Balances after failed transfer (unchanged):</strong></p>";
$dbResult = $db->Execute("SELECT name, balance FROM accounts");
echo "<table>";
echo "<tr><th>Account</th><th>Balance</th></tr>";
while ($row = $dbResult->fetchAssociative()) {
    echo "<tr><td>" . htmlspecialchars($row['name']) . "</td><td>$" . number_format((float)$row['balance'], 2) . "</td></tr>";
}
echo "</table>";
?>
    </div>

    <h2>Using CompleteTrans</h2>
    <div class="section">
<?php
$db->BeginTrans();
$db->ExecuteStatement("UPDATE accounts SET balance = balance + 100 WHERE id = 3");
$committed = $db->CompleteTrans(true);

echo "<p><code>\$db->CompleteTrans(true)</code> commits the transaction.</p>";
echo "<p>Result: " . ($committed ? "<span class='success'>Committed</span>" : "<span class='error'>Rolled back</span>") . "</p>";

$dbResult = $db->Execute("SELECT name, balance FROM accounts WHERE id = 3");
$row = $dbResult->fetchAssociative();
echo "<p>Charlie's new balance: <strong>$" . number_format((float)$row['balance'], 2) . "</strong></p>";
?>
        <pre>&lt;?php
$db->BeginTrans();
$db->ExecuteStatement("UPDATE accounts SET balance = balance + 100 WHERE id = 3");
$committed = $db->CompleteTrans(true);  // true = commit, false = rollback</pre>
    </div>

    <h2>Multiple Operations in Transaction</h2>
    <div class="section">
<?php
$db->BeginTrans();

try {
    $db->ExecuteStatement("UPDATE accounts SET balance = balance * 1.05");
    echo "<p class='success'>Applied 5% bonus to all accounts</p>";

    $db->ExecuteStatement("UPDATE accounts SET balance = balance - 10");
    echo "<p class='success'>Deducted $10 fee from all accounts</p>";

    $db->Commit();
    echo "<p class='success'><strong>All operations committed successfully!</strong></p>";

} catch (\Throwable $e) {
    $db->Rollback();
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . " - All operations rolled back</p>";
}

echo "<p><strong>Final balances:</strong></p>";
$result = $db->Execute("SELECT name, balance FROM accounts");
echo "<table>";
echo "<tr><th>Account</th><th>Balance</th></tr>";
while ($row = $result->fetchAssociative()) {
    echo "<tr><td>" . htmlspecialchars($row['name']) . "</td><td>$" . number_format((float)$row['balance'], 2) . "</td></tr>";
}
echo "</table>";
?>
    </div>

    <h2>Transaction Log</h2>
    <div class="section">
<?php
$result = $db->Execute("SELECT * FROM transactions ORDER BY id");
echo "<table>";
echo "<tr><th>ID</th><th>From</th><th>To</th><th>Amount</th></tr>";
while ($row = $result->fetchAssociative()) {
    $from = $row['from_account'] == 0 ? 'System' : "Account #" . $row['from_account'];
    echo "<tr>";
    echo "<td>#" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($from) . "</td>";
    echo "<td>Account #" . $row['to_account'] . "</td>";
    echo "<td>$" . number_format((float)$row['amount'], 2) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
    </div>

<?php $db->Close(); ?>

    <p><a href="index.php">&larr; Back to Examples</a></p>
</body>
</html>
