<?php
/**
 * VCL Database Example: SchemaManager
 *
 * This example demonstrates how to use the SchemaManager
 * for database schema introspection and manipulation.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use VCL\Database\ConnectionFactory;
use VCL\Database\Schema\SchemaManager;

// Create an in-memory SQLite database for this demo
$db = ConnectionFactory::SQLite(':memory:');
$db->Open();

$schema = new SchemaManager($db);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCL Database Example: SchemaManager</title>
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
        ul { margin: 10px 0; padding-left: 20px; }
        li { margin: 5px 0; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.8em; }
        .badge-primary { background: #007bff; color: white; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
    </style>
</head>
<body>
    <h1>VCL Database Example: SchemaManager</h1>
    <p>This example demonstrates how to use the <code>SchemaManager</code> for database schema introspection and manipulation.</p>

    <h2>Creating Tables</h2>
    <div class="section">
<?php
// Create users table
$schema->CreateTable('users', [
    'id' => ['type' => 'integer', 'autoincrement' => true],
    'username' => ['type' => 'string', 'length' => 50, 'notnull' => true],
    'email' => ['type' => 'string', 'length' => 100, 'notnull' => true],
    'password_hash' => ['type' => 'string', 'length' => 255, 'notnull' => false],
    'status' => ['type' => 'string', 'length' => 20, 'default' => 'active', 'notnull' => false],
    'created_at' => ['type' => 'datetime', 'notnull' => false],
], [
    'primary' => 'id',
    'unique' => [
        'idx_username' => 'username',
        'idx_email' => 'email',
    ],
]);

// Create posts table
$schema->CreateTable('posts', [
    'id' => ['type' => 'integer', 'autoincrement' => true],
    'user_id' => ['type' => 'integer', 'notnull' => true],
    'title' => ['type' => 'string', 'length' => 200, 'notnull' => true],
    'content' => ['type' => 'text'],
    'published' => ['type' => 'boolean', 'default' => false],
    'created_at' => ['type' => 'datetime'],
], [
    'primary' => 'id',
    'indexes' => [
        'idx_user_id' => 'user_id',
    ],
]);

// Create tags table
$schema->CreateTable('tags', [
    'id' => ['type' => 'integer', 'autoincrement' => true],
    'name' => ['type' => 'string', 'length' => 50, 'notnull' => true],
], [
    'primary' => 'id',
    'unique' => [
        'idx_tag_name' => 'name',
    ],
]);
?>
        <p>Created tables:</p>
        <ul>
            <li><span class="badge badge-success">✓</span> <code>users</code> - with unique indexes on username and email</li>
            <li><span class="badge badge-success">✓</span> <code>posts</code> - with foreign key index on user_id</li>
            <li><span class="badge badge-success">✓</span> <code>tags</code> - with unique index on name</li>
        </ul>
        <pre>&lt;?php
$schema->CreateTable('users', [
    'id' => ['type' => 'integer', 'autoincrement' => true],
    'username' => ['type' => 'string', 'length' => 50, 'notnull' => true],
    'email' => ['type' => 'string', 'length' => 100, 'notnull' => true],
    'password_hash' => ['type' => 'string', 'length' => 255, 'notnull' => false],
    'status' => ['type' => 'string', 'length' => 20, 'default' => 'active', 'notnull' => false],
    'created_at' => ['type' => 'datetime', 'notnull' => false],
], [
    'primary' => 'id',
    'unique' => [
        'idx_username' => 'username',
        'idx_email' => 'email',
    ],
]);</pre>
    </div>

    <h2>Listing Tables</h2>
    <div class="section">
<?php
$tables = $schema->GetTables();
?>
        <p>Tables in database:</p>
        <ul>
<?php foreach ($tables as $table): ?>
            <li><code><?= htmlspecialchars($table) ?></code></li>
<?php endforeach; ?>
        </ul>
        <pre>&lt;?php
$tables = $schema->GetTables();
foreach ($tables as $table) {
    echo $table;
}</pre>
    </div>

    <h2>Table Details</h2>
    <div class="section">
<?php
$tableInfo = $schema->GetTable('users');
?>
        <p><strong>Table:</strong> <code><?= htmlspecialchars($tableInfo['name']) ?></code></p>
        <p><strong>Primary Key:</strong> <code><?= htmlspecialchars(implode(', ', $tableInfo['primaryKey'] ?? ['none'])) ?></code></p>

        <p><strong>Columns:</strong></p>
        <table>
            <tr>
                <th>Column</th>
                <th>Type</th>
                <th>Nullable</th>
                <th>Default</th>
            </tr>
<?php foreach ($tableInfo['columns'] as $name => $info): ?>
            <tr>
                <td><code><?= htmlspecialchars($name) ?></code></td>
                <td><?= htmlspecialchars($info['type']) ?></td>
                <td><?= $info['notnull'] ? '<span class="error">NOT NULL</span>' : '<span class="info">NULL</span>' ?></td>
                <td><?= $info['default'] !== null ? '<code>' . htmlspecialchars((string)$info['default']) . '</code>' : '-' ?></td>
            </tr>
<?php endforeach; ?>
        </table>
        <pre>&lt;?php
$tableInfo = $schema->GetTable('users');
foreach ($tableInfo['columns'] as $name => $info) {
    echo "$name: {$info['type']}";
}</pre>
    </div>

    <h2>Column Operations</h2>
    <div class="section">
<?php
$exists = $schema->ColumnExists('users', 'email');
$columns = $schema->GetColumnNames('users');
?>
        <p><strong>Check column exists:</strong> <code>email</code> in <code>users</code>: <span class="<?= $exists ? 'success' : 'error' ?>"><?= $exists ? 'Yes' : 'No' ?></span></p>
        <p><strong>User columns:</strong> <?= htmlspecialchars(implode(', ', $columns)) ?></p>

<?php
// Add a new column
$schema->AddColumn('users', 'last_login', [
    'type' => 'datetime',
    'notnull' => false,
]);
$columnsAfter = $schema->GetColumnNames('users');
?>
        <p><strong>After adding <code>last_login</code> column:</strong></p>
        <p><?= htmlspecialchars(implode(', ', $columnsAfter)) ?></p>
        <pre>&lt;?php
// Check if column exists
$exists = $schema->ColumnExists('users', 'email');

// Get column names
$columns = $schema->GetColumnNames('users');

// Add a new column
$schema->AddColumn('users', 'last_login', [
    'type' => 'datetime',
    'notnull' => false,
]);</pre>
    </div>

    <h2>Index Operations</h2>
    <div class="section">
<?php
$indexes = $schema->GetIndexes('users');
?>
        <p><strong>Indexes on <code>users</code> table:</strong></p>
        <table>
            <tr>
                <th>Index Name</th>
                <th>Type</th>
                <th>Columns</th>
            </tr>
<?php foreach ($indexes as $name => $info): ?>
<?php
$type = $info['primary'] ? 'PRIMARY' : ($info['unique'] ? 'UNIQUE' : 'INDEX');
$badgeClass = $info['primary'] ? 'badge-primary' : ($info['unique'] ? 'badge-warning' : 'badge-success');
?>
            <tr>
                <td><code><?= htmlspecialchars($name) ?></code></td>
                <td><span class="badge <?= $badgeClass ?>"><?= $type ?></span></td>
                <td><?= htmlspecialchars(implode(', ', $info['columns'])) ?></td>
            </tr>
<?php endforeach; ?>
        </table>

<?php
// Add a new index
$schema->AddIndex('posts', 'idx_published', ['published']);
$existsIdx = $schema->IndexExists('posts', 'idx_published');
?>
        <p><span class="badge badge-success">✓</span> Added index <code>idx_published</code> on <code>posts.published</code></p>
        <p><strong>Index exists:</strong> <span class="success"><?= $existsIdx ? 'Yes' : 'No' ?></span></p>
        <pre>&lt;?php
$indexes = $schema->GetIndexes('users');

// Add a new index
$schema->AddIndex('posts', 'idx_published', ['published']);

// Check if index exists
$exists = $schema->IndexExists('posts', 'idx_published');</pre>
    </div>

    <h2>Table Existence</h2>
    <div class="section">
        <ul>
            <li><code>users</code> exists: <span class="<?= $schema->TableExists('users') ? 'success' : 'error' ?>"><?= $schema->TableExists('users') ? 'Yes' : 'No' ?></span></li>
            <li><code>orders</code> exists: <span class="<?= $schema->TableExists('orders') ? 'success' : 'error' ?>"><?= $schema->TableExists('orders') ? 'Yes' : 'No' ?></span></li>
        </ul>
        <pre>&lt;?php
$exists = $schema->TableExists('users');  // true
$exists = $schema->TableExists('orders'); // false</pre>
    </div>

    <h2>Rename Table</h2>
    <div class="section">
<?php
$schema->RenameTable('tags', 'categories');
$tablesAfterRename = $schema->GetTables();
?>
        <p><span class="badge badge-success">✓</span> Renamed <code>tags</code> to <code>categories</code></p>
        <p><strong>Tables after rename:</strong> <?= htmlspecialchars(implode(', ', $tablesAfterRename)) ?></p>
        <pre>&lt;?php
$schema->RenameTable('tags', 'categories');</pre>
    </div>

    <h2>Full Schema Introspection</h2>
    <div class="section">
<?php
$fullSchema = $schema->GetTableDetails();
?>
        <table>
            <tr>
                <th>Table</th>
                <th>Columns</th>
                <th>Indexes</th>
                <th>Foreign Keys</th>
            </tr>
<?php foreach ($fullSchema as $tableName => $tableInfo): ?>
            <tr>
                <td><code><?= htmlspecialchars($tableName) ?></code></td>
                <td><?= count($tableInfo['columns']) ?></td>
                <td><?= count($tableInfo['indexes']) ?></td>
                <td><?= count($tableInfo['foreignKeys']) ?></td>
            </tr>
<?php endforeach; ?>
        </table>
        <pre>&lt;?php
$fullSchema = $schema->GetTableDetails();
foreach ($fullSchema as $tableName => $tableInfo) {
    echo "Table: {$tableName}";
    echo "Columns: " . count($tableInfo['columns']);
    echo "Indexes: " . count($tableInfo['indexes']);
}</pre>
    </div>

    <h2>Dropping Objects</h2>
    <div class="section">
<?php
// Drop an index
$schema->DropIndex('posts', 'idx_published');
// Drop a column
$schema->DropColumn('users', 'last_login');
// Drop a table
$schema->DropTable('categories');
$tablesRemaining = $schema->GetTables();
?>
        <ul>
            <li><span class="badge badge-warning">×</span> Dropped index <code>idx_published</code></li>
            <li><span class="badge badge-warning">×</span> Dropped column <code>last_login</code></li>
            <li><span class="badge badge-warning">×</span> Dropped table <code>categories</code></li>
        </ul>
        <p><strong>Remaining tables:</strong> <?= htmlspecialchars(implode(', ', $tablesRemaining)) ?></p>
        <pre>&lt;?php
$schema->DropIndex('posts', 'idx_published');
$schema->DropColumn('users', 'last_login');
$schema->DropTable('categories');</pre>
    </div>

    <h2>Truncate Table</h2>
    <div class="section">
<?php
// Insert some data first
$db->ExecuteStatement("INSERT INTO users (username, email) VALUES ('alice', 'alice@example.com')");
$db->ExecuteStatement("INSERT INTO users (username, email) VALUES ('bob', 'bob@example.com')");

$countBefore = $db->Execute("SELECT COUNT(*) as cnt FROM users")->fetchAssociative()['cnt'];

$schema->TruncateTable('users');

$countAfter = $db->Execute("SELECT COUNT(*) as cnt FROM users")->fetchAssociative()['cnt'];
?>
        <p><strong>Users before truncate:</strong> <?= $countBefore ?></p>
        <p><strong>Users after truncate:</strong> <?= $countAfter ?></p>
        <pre>&lt;?php
$schema->TruncateTable('users');</pre>
    </div>

    <h2>Direct DBAL Access</h2>
    <div class="section">
<?php
$dbalSm = $schema->GetDbalSchemaManager();
$schemaObj = $schema->IntrospectSchema();
?>
        <p><strong>DBAL SchemaManager class:</strong> <code><?= htmlspecialchars(get_class($dbalSm)) ?></code></p>
        <p><strong>Schema tables:</strong> <?= count($schemaObj->getTables()) ?></p>
        <pre>&lt;?php
// Access underlying Doctrine DBAL SchemaManager
$dbalSm = $schema->GetDbalSchemaManager();

// Get the full schema object
$schemaObj = $schema->IntrospectSchema();</pre>
    </div>

<?php
$db->Close();
?>

    <p><a href="index.php">&larr; Back to Examples</a></p>
</body>
</html>
