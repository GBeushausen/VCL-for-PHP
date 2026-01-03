<?php
/**
 * This file is part of the VCL for PHP project
 *
 * Copyright (c) 2004-2008 qadram software S.L. <support@qadram.com>
 * Copyright (c) 2024-2025 Gunnar Beushausen
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 */

declare(strict_types=1);

require_once("vcl/vcl.inc.php");
use_unit("designide.inc.php");

setPackageTitle("Database VCL for PHP Components");
setIconPath("./icons");

// Database components - now use Doctrine DBAL (src/VCL/Database/)
registerNamespacedComponent("Data Access", "Connection", \VCL\Database\Connection::class);
registerNamespacedComponent("Data Access", "Table", \VCL\Database\Table::class);
registerNamespacedComponent("Data Access", "Query", \VCL\Database\Query::class);
registerNamespacedComponent("Data Access", "StoredProc", \VCL\Database\StoredProc::class);
registerNamespacedComponent("Data Access", "Datasource", \VCL\Database\Datasource::class);

// Property configuration
registerPropertyValues("Datasource", "DataSet", array('DataSet'));
registerPropertyValues("Table", "Database", array('Connection'));
registerPropertyValues("Query", "Database", array('Connection'));
registerPropertyValues("Table", "MasterSource", array('Datasource'));
registerPropertyValues("Query", "Order", array('ASC', 'DESC'));

registerBooleanProperty("CustomConnection", "Connected");
registerBooleanProperty("Connection", "Debug");
registerBooleanProperty("Connection", "Persistent");
registerBooleanProperty("Table", "Active");
registerBooleanProperty("Query", "Active");
registerBooleanProperty("Table", "HasAutoInc");

registerPasswordProperty("CustomConnection", "UserPassword");
registerPropertyEditor("Query", "SQL", "TStringListPropertyEditor", "native");
registerPropertyEditor("Table", "MasterFields", "TValueListPropertyEditor", "native");

// Supported database drivers (Doctrine DBAL)
registerPropertyValues("Connection", "DriverName", array(
    'mysql',
    'mariadb',
    'pgsql',
    'sqlite',
    'sqlsrv',
    'oci8',
    'ibm_db2',
));
