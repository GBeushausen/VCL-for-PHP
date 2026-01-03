<?php
/**
 * Example Migration
 *
 * This is an example migration file. Copy and modify this template
 * or use 'php bin/migrate generate <name>' to create new migrations.
 */

declare(strict_types=1);

namespace VCL\Migrations;

use Doctrine\DBAL\Schema\Schema;
use VCL\Database\Migration\AbstractMigration;

/**
 * Creates an example users table.
 */
class Version20250103120000_CreateExampleTable extends AbstractMigration
{
    public function GetDescription(): string
    {
        return 'Creates an example users table with common fields';
    }

    public function Up(Schema $schema): void
    {
        // Create users table
        $users = $schema->createTable('example_users');
        $users->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $users->addColumn('username', 'string', ['length' => 100]);
        $users->addColumn('email', 'string', ['length' => 255]);
        $users->addColumn('password_hash', 'string', ['length' => 255]);
        $users->addColumn('first_name', 'string', ['length' => 100, 'notnull' => false]);
        $users->addColumn('last_name', 'string', ['length' => 100, 'notnull' => false]);
        $users->addColumn('is_active', 'boolean', ['default' => true]);
        $users->addColumn('created_at', 'datetime');
        $users->addColumn('updated_at', 'datetime', ['notnull' => false]);
        $users->setPrimaryKey(['id']);
        $users->addUniqueIndex(['username'], 'idx_users_username');
        $users->addUniqueIndex(['email'], 'idx_users_email');

        // Create user_roles table
        $roles = $schema->createTable('example_user_roles');
        $roles->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $roles->addColumn('user_id', 'integer', ['unsigned' => true]);
        $roles->addColumn('role', 'string', ['length' => 50]);
        $roles->addColumn('granted_at', 'datetime');
        $roles->setPrimaryKey(['id']);
        $roles->addIndex(['user_id'], 'idx_user_roles_user');
        $roles->addForeignKeyConstraint(
            'example_users',
            ['user_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'fk_user_roles_user'
        );
    }

    public function Down(Schema $schema): void
    {
        // Drop in reverse order due to foreign keys
        $schema->dropTable('example_user_roles');
        $schema->dropTable('example_users');
    }
}
