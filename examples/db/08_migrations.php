<?php
/**
 * VCL Database Example: Migrations
 *
 * This example demonstrates how to use the Migration system
 * for versioned database schema changes.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use VCL\Database\ConnectionFactory;
use VCL\Database\Migration\MigrationManager;
use VCL\Database\Migration\MigrationGenerator;

// Create an in-memory SQLite database for this demo
$db = ConnectionFactory::SQLite(':memory:');
$db->Open();

// Setup Migration Directory
$migrationsPath = sys_get_temp_dir() . '/vcl_migrations_demo_' . uniqid();
mkdir($migrationsPath, 0755, true);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCL Database Example: Migrations</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 900px; margin: 40px auto; padding: 0 20px; line-height: 1.6; }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        .section { background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0; }
        code { background: #e9ecef; padding: 2px 6px; border-radius: 4px; font-family: 'Fira Code', monospace; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 0.9em; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        table { border-collapse: collapse; width: 100%; margin: 15px 0; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        th { background: #e9ecef; }
        ul { margin: 10px 0; padding-left: 20px; }
        li { margin: 5px 0; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.8em; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-info { background: #17a2b8; color: white; }
        .badge-secondary { background: #6c757d; color: white; }
        .migration-file { background: #343a40; color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0; font-size: 0.85em; overflow-x: auto; }
        .migration-file .filename { color: #ffc107; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>VCL Database Example: Migrations</h1>
    <p>This example demonstrates how to use the <code>MigrationManager</code> for versioned database schema changes.</p>

    <h2>Creating Migration Files</h2>
    <div class="section">
        <p><strong>Migration directory:</strong> <code><?= htmlspecialchars($migrationsPath) ?></code></p>
<?php
// Migration 1: Create users table
$migration1 = <<<'PHP'
<?php
use Doctrine\DBAL\Schema\Schema;
use VCL\Database\Connection;

return new class {
    public function up(Schema $schema, Connection $connection): void
    {
        $table = $schema->createTable('users');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('username', 'string', ['length' => 50]);
        $table->addColumn('email', 'string', ['length' => 100]);
        $table->addColumn('created_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['username'], 'idx_username');
        $table->addUniqueIndex(['email'], 'idx_email');
    }

    public function down(Schema $schema, Connection $connection): void
    {
        $schema->dropTable('users');
    }
};
PHP;

file_put_contents($migrationsPath . '/20240101_000001_create_users_table.php', $migration1);

// Migration 2: Create posts table
$migration2 = <<<'PHP'
<?php
use Doctrine\DBAL\Schema\Schema;
use VCL\Database\Connection;

return new class {
    public function up(Schema $schema, Connection $connection): void
    {
        $table = $schema->createTable('posts');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('user_id', 'integer');
        $table->addColumn('title', 'string', ['length' => 200]);
        $table->addColumn('content', 'text', ['notnull' => false]);
        $table->addColumn('published', 'boolean', ['default' => false]);
        $table->addColumn('created_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['user_id'], 'idx_posts_user');
    }

    public function down(Schema $schema, Connection $connection): void
    {
        $schema->dropTable('posts');
    }
};
PHP;

file_put_contents($migrationsPath . '/20240101_000002_create_posts_table.php', $migration2);

// Migration 3: Add status column to users
$migration3 = <<<'PHP'
<?php
use Doctrine\DBAL\Schema\Schema;
use VCL\Database\Connection;

return new class {
    public function up(Schema $schema, Connection $connection): void
    {
        $table = $schema->getTable('users');
        $table->addColumn('status', 'string', ['length' => 20, 'default' => 'active']);
    }

    public function down(Schema $schema, Connection $connection): void
    {
        $table = $schema->getTable('users');
        $table->dropColumn('status');
    }
};
PHP;

file_put_contents($migrationsPath . '/20240101_000003_add_status_to_users.php', $migration3);
?>
        <p>Created migration files:</p>
        <ul>
            <li><span class="badge badge-success">✓</span> <code>20240101_000001_create_users_table.php</code></li>
            <li><span class="badge badge-success">✓</span> <code>20240101_000002_create_posts_table.php</code></li>
            <li><span class="badge badge-success">✓</span> <code>20240101_000003_add_status_to_users.php</code></li>
        </ul>

        <p><strong>Example migration file structure:</strong></p>
        <pre>&lt;?php
use Doctrine\DBAL\Schema\Schema;
use VCL\Database\Connection;

return new class {
    public function up(Schema $schema, Connection $connection): void
    {
        $table = $schema->createTable('users');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('username', 'string', ['length' => 50]);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema, Connection $connection): void
    {
        $schema->dropTable('users');
    }
};</pre>
    </div>

    <h2>Initialize Migration Manager</h2>
    <div class="section">
<?php
$manager = new MigrationManager($db, [
    'migrations_path' => $migrationsPath,
    'migrations_namespace' => 'VCL\\Migrations',
]);

$available = $manager->GetAvailableMigrations();
$pending = $manager->GetPendingMigrations();
?>
        <p><strong>Available migrations:</strong></p>
        <ul>
<?php foreach ($available as $version => $file): ?>
            <li><code><?= htmlspecialchars($version) ?></code></li>
<?php endforeach; ?>
        </ul>
        <p><strong>Pending migrations:</strong> <?= count($pending) ?></p>
        <pre>&lt;?php
$manager = new MigrationManager($db, [
    'migrations_path' => $migrationsPath,
    'migrations_namespace' => 'VCL\\Migrations',
]);

$available = $manager->GetAvailableMigrations();
$pending = $manager->GetPendingMigrations();</pre>
    </div>

    <h2>Running Migrations</h2>
    <div class="section">
<?php
$executed = [];
$migrationError = null;
try {
    $executed = $manager->Migrate();
} catch (\Throwable $e) {
    $migrationError = $e->getMessage();
}
?>
<?php if ($migrationError): ?>
        <p class="error"><strong>Migration failed:</strong> <?= htmlspecialchars($migrationError) ?></p>
<?php else: ?>
        <p><strong>Executed migrations:</strong></p>
        <ul>
<?php foreach ($executed as $version): ?>
            <li><span class="badge badge-success">✓</span> <code><?= htmlspecialchars($version) ?></code></li>
<?php endforeach; ?>
        </ul>
<?php endif; ?>
        <pre>&lt;?php
$executed = $manager->Migrate();
foreach ($executed as $version) {
    echo "Executed: {$version}";
}</pre>
    </div>

    <h2>Migration Status</h2>
    <div class="section">
<?php
$status = $manager->GetStatus();
$tables = $db->Tables();
?>
        <p><strong>Total:</strong> <?= $status['total'] ?> | <strong>Executed:</strong> <?= $status['executed'] ?> | <strong>Pending:</strong> <?= $status['pending'] ?></p>
        <table>
            <tr>
                <th>Status</th>
                <th>Version</th>
                <th>Executed At</th>
            </tr>
<?php foreach ($status['migrations'] as $version => $info): ?>
            <tr>
                <td>
<?php if ($info['executed']): ?>
                    <span class="badge badge-success">✓</span>
<?php else: ?>
                    <span class="badge badge-secondary">○</span>
<?php endif; ?>
                </td>
                <td><code><?= htmlspecialchars($version) ?></code></td>
                <td><?= $info['executed_at'] ?? '<span class="info">Not executed</span>' ?></td>
            </tr>
<?php endforeach; ?>
        </table>
        <p><strong>Database tables:</strong> <?= htmlspecialchars(implode(', ', $tables)) ?></p>
        <pre>&lt;?php
$status = $manager->GetStatus();
// $status contains: total, executed, pending, migrations
foreach ($status['migrations'] as $version => $info) {
    $executed = $info['executed'] ? '✓' : '○';
    echo "{$executed} {$version}";
}</pre>
    </div>

    <h2>Rollback Last Migration</h2>
    <div class="section">
<?php
$rolledBack = [];
$rollbackError = null;
try {
    $rolledBack = $manager->Rollback(1);
} catch (\Throwable $e) {
    $rollbackError = $e->getMessage();
}
?>
<?php if ($rollbackError): ?>
        <p class="error"><strong>Rollback failed:</strong> <?= htmlspecialchars($rollbackError) ?></p>
<?php else: ?>
        <p><strong>Rolled back:</strong></p>
        <ul>
<?php foreach ($rolledBack as $version): ?>
            <li><span class="badge badge-warning">↩</span> <code><?= htmlspecialchars($version) ?></code></li>
<?php endforeach; ?>
        </ul>
<?php endif; ?>

<?php
$statusAfterRollback = $manager->GetStatus();
?>
        <p><strong>Status after rollback:</strong></p>
        <table>
            <tr>
                <th>Status</th>
                <th>Version</th>
            </tr>
<?php foreach ($statusAfterRollback['migrations'] as $version => $info): ?>
            <tr>
                <td>
<?php if ($info['executed']): ?>
                    <span class="badge badge-success">✓</span>
<?php else: ?>
                    <span class="badge badge-secondary">○</span>
<?php endif; ?>
                </td>
                <td><code><?= htmlspecialchars($version) ?></code></td>
            </tr>
<?php endforeach; ?>
        </table>
        <pre>&lt;?php
$rolledBack = $manager->Rollback(1);
foreach ($rolledBack as $version) {
    echo "Rolled back: {$version}";
}</pre>
    </div>

    <h2>Migrate to Latest</h2>
    <div class="section">
<?php
$executedAgain = [];
$migrateError = null;
try {
    $executedAgain = $manager->Migrate();
} catch (\Throwable $e) {
    $migrateError = $e->getMessage();
}
?>
<?php if ($migrateError): ?>
        <p class="error"><strong>Migration failed:</strong> <?= htmlspecialchars($migrateError) ?></p>
<?php elseif (empty($executedAgain)): ?>
        <p class="info">No pending migrations to execute.</p>
<?php else: ?>
        <p><strong>Executed migrations:</strong></p>
        <ul>
<?php foreach ($executedAgain as $version): ?>
            <li><span class="badge badge-success">✓</span> <code><?= htmlspecialchars($version) ?></code></li>
<?php endforeach; ?>
        </ul>
<?php endif; ?>
        <pre>&lt;?php
// Migrate all pending migrations
$executed = $manager->Migrate();</pre>
    </div>

    <h2>Migration Generator</h2>
    <div class="section">
<?php
$generator = new MigrationGenerator($db, [
    'migrations_path' => $migrationsPath,
    'migrations_namespace' => 'VCL\\Migrations',
]);

// Generate empty migration
$emptyFile = $generator->Generate('add_comments_table');

// Generate migration for creating a table
$tableFile = $generator->GenerateCreateTable('categories', [
    'id' => ['type' => 'integer', 'autoincrement' => true],
    'name' => ['type' => 'string', 'length' => 100],
    'slug' => ['type' => 'string', 'length' => 100],
], [
    'primary' => 'id',
    'unique' => ['idx_slug' => ['slug']],
]);

$generatedContent = file_get_contents($tableFile);
?>
        <p><strong>Generated files:</strong></p>
        <ul>
            <li><span class="badge badge-info">+</span> <code><?= htmlspecialchars(basename($emptyFile)) ?></code> (empty template)</li>
            <li><span class="badge badge-info">+</span> <code><?= htmlspecialchars(basename($tableFile)) ?></code> (create table)</li>
        </ul>

        <p><strong>Generated migration content:</strong></p>
        <div class="migration-file">
            <div class="filename"><?= htmlspecialchars(basename($tableFile)) ?></div>
            <pre style="background: transparent; padding: 0; margin: 0;"><?= htmlspecialchars($generatedContent) ?></pre>
        </div>
        <pre>&lt;?php
$generator = new MigrationGenerator($db, [
    'migrations_path' => $migrationsPath,
    'migrations_namespace' => 'VCL\\Migrations',
]);

// Generate empty migration
$file = $generator->Generate('add_comments_table');

// Generate migration for creating a table
$file = $generator->GenerateCreateTable('categories', [
    'id' => ['type' => 'integer', 'autoincrement' => true],
    'name' => ['type' => 'string', 'length' => 100],
    'slug' => ['type' => 'string', 'length' => 100],
], [
    'primary' => 'id',
    'unique' => ['idx_slug' => ['slug']],
]);</pre>
    </div>

    <h2>Reset Database</h2>
    <div class="section">
<?php
$resetRolledBack = [];
$resetError = null;
try {
    $resetRolledBack = $manager->Reset();
} catch (\Throwable $e) {
    $resetError = $e->getMessage();
}
$tablesAfterReset = $db->Tables();
?>
<?php if ($resetError): ?>
        <p class="error"><strong>Reset failed:</strong> <?= htmlspecialchars($resetError) ?></p>
<?php else: ?>
        <p><span class="badge badge-warning">!</span> Reset complete. Rolled back <strong><?= count($resetRolledBack) ?></strong> migrations.</p>
<?php endif; ?>
        <p><strong>Remaining tables:</strong> <?= empty($tablesAfterReset) ? '<span class="info">(none)</span>' : htmlspecialchars(implode(', ', $tablesAfterReset)) ?></p>
        <pre>&lt;?php
// Roll back all migrations
$rolledBack = $manager->Reset();</pre>
    </div>

<?php
// Cleanup: Remove demo migration files
$files = glob($migrationsPath . '/*.php');
foreach ($files as $file) {
    unlink($file);
}
rmdir($migrationsPath);

$db->Close();
?>

    <p><a href="index.php">&larr; Back to Examples</a></p>
</body>
</html>
