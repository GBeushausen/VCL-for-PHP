<?php
/**
 * VCL Database Migrations Configuration
 *
 * This file configures the VCL migration system. Copy this file to your
 * project root and adjust the settings as needed.
 */

return [
    // Table to store migration versions
    'migrations_table' => 'vcl_migrations',

    // Directory containing migration files
    'migrations_path' => __DIR__ . '/migrations',

    // PHP namespace for migration classes
    'migrations_namespace' => 'VCL\\Migrations',

    // Run all migrations in a single transaction
    // If one fails, all are rolled back
    'all_or_nothing' => true,

    // Database connection configuration
    // Can be overridden when creating MigrationManager
    'connection' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => '',
        'username' => '',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
];
