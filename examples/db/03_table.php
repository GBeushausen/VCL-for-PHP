<?php
/**
 * VCL Database Example: Table Component
 *
 * This example demonstrates how to use the Table component
 * for working with database tables including CRUD operations.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use VCL\Database\ConnectionFactory;
use VCL\Database\Table;
use VCL\Database\Datasource;

// Create an in-memory SQLite database for this demo
$db = ConnectionFactory::SQLite(':memory:');
$db->Open();

// Create sample table
$db->ExecuteStatement("
    CREATE TABLE products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price REAL NOT NULL,
        stock INTEGER DEFAULT 0,
        category TEXT,
        active INTEGER DEFAULT 1
    )
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCL Database Example: Table</title>
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
    <h1>VCL Database Example: Table</h1>
    <p>This example demonstrates how to use the Table component for CRUD operations on database tables.</p>

    <h2>Basic Table Setup</h2>
    <div class="section">
<?php
$table = new Table();
$table->Database = $db;
$table->TableName = 'products';
$table->Open();

echo "<p>Table opened. Fields: <code>" . htmlspecialchars(implode(', ', $table->FieldNames())) . "</code></p>";
$table->Close();
?>
        <pre>&lt;?php
$table = new Table();
$table->Database = $db;
$table->TableName = 'products';
$table->Open();</pre>
    </div>

    <h2>Inserting Records</h2>
    <div class="section">
<?php
$table = new Table();
$table->Database = $db;
$table->TableName = 'products';
$table->Open();

$products = [
    ['Laptop', 999.99, 10, 'Electronics'],
    ['Mouse', 29.99, 50, 'Electronics'],
    ['Keyboard', 79.99, 30, 'Electronics'],
    ['Desk Chair', 299.99, 5, 'Furniture'],
    ['Monitor', 399.99, 15, 'Electronics'],
];

echo "<p>Inserting products:</p><ul>";
foreach ($products as $product) {
    $table->Insert();
    $table->name = $product[0];
    $table->price = $product[1];
    $table->stock = $product[2];
    $table->category = $product[3];
    $table->Post();
    echo "<li class='success'>Inserted: {$product[0]}</li>";
}
echo "</ul>";
?>
        <pre>&lt;?php
$table->Insert();
$table->name = 'Laptop';
$table->price = 999.99;
$table->stock = 10;
$table->category = 'Electronics';
$table->Post();</pre>
    </div>

    <h2>Reading Records</h2>
    <div class="section">
<?php
$table->First();
echo "<table>";
echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Category</th></tr>";
while (!$table->EOF()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars((string)$table->id) . "</td>";
    echo "<td>" . htmlspecialchars((string)$table->name) . "</td>";
    echo "<td>$" . number_format((float)$table->price, 2) . "</td>";
    echo "<td>" . htmlspecialchars((string)$table->stock) . "</td>";
    echo "<td>" . htmlspecialchars((string)$table->category) . "</td>";
    echo "</tr>";
    $table->Next();
}
echo "</table>";
?>
    </div>

    <h2>Filtering Records</h2>
    <div class="section">
<?php
$table->Filter = "category = 'Electronics'";
$table->Refresh();

echo "<p>Filter: <code>category = 'Electronics'</code></p>";
echo "<ul>";
$table->First();
while (!$table->EOF()) {
    echo "<li>" . htmlspecialchars((string)$table->name) . " - $" . number_format((float)$table->price, 2) . "</li>";
    $table->Next();
}
echo "</ul>";

$table->Filter = '';
$table->Refresh();
?>
        <pre>&lt;?php
$table->Filter = "category = 'Electronics'";
$table->Refresh();</pre>
    </div>

    <h2>Ordering Records</h2>
    <div class="section">
<?php
$table->OrderField = 'price';
$table->Order = 'DESC';
$table->Refresh();

echo "<p>Order: <code>price DESC</code></p>";
echo "<ul>";
$table->First();
while (!$table->EOF()) {
    echo "<li>" . htmlspecialchars((string)$table->name) . ": $" . number_format((float)$table->price, 2) . "</li>";
    $table->Next();
}
echo "</ul>";

$table->OrderField = '';
$table->Refresh();
?>
        <pre>&lt;?php
$table->OrderField = 'price';
$table->Order = 'DESC';
$table->Refresh();</pre>
    </div>

    <h2>Updating Records</h2>
    <div class="section">
<?php
$table->First();
while (!$table->EOF()) {
    if ($table->name === 'Mouse') {
        $oldPrice = $table->price;
        $table->Edit();
        $table->price = 24.99;
        $table->stock = 75;
        $table->Post();
        echo "<p class='success'>Updated <strong>Mouse</strong>: Price changed from $" . number_format((float)$oldPrice, 2) . " to $" . number_format((float)$table->price, 2) . ", Stock updated to " . $table->stock . "</p>";
        break;
    }
    $table->Next();
}
?>
        <pre>&lt;?php
$table->Edit();
$table->price = 24.99;
$table->stock = 75;
$table->Post();</pre>
    </div>

    <h2>Deleting Records</h2>
    <div class="section">
<?php
$table->Filter = '';
$table->Refresh();
$table->First();

while (!$table->EOF()) {
    if ($table->name === 'Desk Chair') {
        $deletedName = $table->name;
        $table->Delete();
        echo "<p class='error'>Deleted: <strong>" . htmlspecialchars((string)$deletedName) . "</strong></p>";
        break;
    }
    $table->Next();
}

echo "<p>Remaining products:</p><ul>";
$table->Refresh();
$table->First();
while (!$table->EOF()) {
    echo "<li>" . htmlspecialchars((string)$table->name) . "</li>";
    $table->Next();
}
echo "</ul>";
?>
        <pre>&lt;?php
$table->Delete();  // Deletes current record</pre>
    </div>

    <h2>Cancel Edit</h2>
    <div class="section">
<?php
$table->First();
$originalName = $table->name;

$table->Edit();
$table->name = 'CHANGED NAME';
$duringEdit = $table->name;

$table->Cancel();
$afterCancel = $table->name;

echo "<table>";
echo "<tr><th>State</th><th>Name Value</th></tr>";
echo "<tr><td>Original</td><td>" . htmlspecialchars((string)$originalName) . "</td></tr>";
echo "<tr><td>During Edit</td><td>" . htmlspecialchars((string)$duringEdit) . "</td></tr>";
echo "<tr><td>After Cancel</td><td>" . htmlspecialchars((string)$afterCancel) . "</td></tr>";
echo "</table>";
?>
        <pre>&lt;?php
$table->Edit();
$table->name = 'CHANGED NAME';
$table->Cancel();  // Reverts changes</pre>
    </div>

    <h2>Record State</h2>
    <div class="section">
<?php
$table->First();
echo "<table>";
echo "<tr><th>Action</th><th>State</th></tr>";
echo "<tr><td>After <code>First()</code></td><td><code>" . $table->State->name . "</code></td></tr>";

$table->Edit();
echo "<tr><td>After <code>Edit()</code></td><td><code>" . $table->State->name . "</code></td></tr>";

$table->Cancel();
echo "<tr><td>After <code>Cancel()</code></td><td><code>" . $table->State->name . "</code></td></tr>";

$table->Insert();
echo "<tr><td>After <code>Insert()</code></td><td><code>" . $table->State->name . "</code></td></tr>";

$table->Cancel();
echo "<tr><td>After <code>Cancel()</code></td><td><code>" . $table->State->name . "</code></td></tr>";
echo "</table>";
?>
    </div>

    <h2>Master-Detail Relationship</h2>
    <div class="section">
<?php
// Create orders table
$db->ExecuteStatement("
    CREATE TABLE orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER,
        quantity INTEGER,
        order_date TEXT DEFAULT CURRENT_TIMESTAMP
    )
");

$db->ExecuteStatement("INSERT INTO orders (product_id, quantity) VALUES (1, 2)");
$db->ExecuteStatement("INSERT INTO orders (product_id, quantity) VALUES (1, 1)");
$db->ExecuteStatement("INSERT INTO orders (product_id, quantity) VALUES (2, 5)");

$masterTable = new Table();
$masterTable->Database = $db;
$masterTable->TableName = 'products';
$masterTable->Open();

$masterSource = new Datasource();
$masterSource->DataSet = $masterTable;

$detailTable = new Table();
$detailTable->Database = $db;
$detailTable->TableName = 'orders';
$detailTable->MasterSource = $masterSource;
$detailTable->MasterFields = ['product_id' => 'id'];
$detailTable->Open();

echo "<p>Master-Detail: <strong>Products → Orders</strong></p>";

$masterTable->First();
while (!$masterTable->EOF()) {
    echo "<div style='margin: 10px 0; padding: 10px; background: #fff; border: 1px solid #dee2e6; border-radius: 4px;'>";
    echo "<strong>" . htmlspecialchars((string)$masterTable->name) . "</strong>";

    $detailTable->Refresh();
    $detailTable->First();

    $orderCount = 0;
    echo "<ul style='margin: 5px 0;'>";
    while (!$detailTable->EOF()) {
        $orderCount++;
        echo "<li>Order #" . $detailTable->id . ": Qty " . $detailTable->quantity . "</li>";
        $detailTable->Next();
    }
    echo "</ul>";

    if ($orderCount === 0) {
        echo "<em class='info'>(no orders)</em>";
    }
    echo "</div>";

    $masterTable->Next();
}

$masterTable->Close();
$detailTable->Close();
?>
        <pre>&lt;?php
$masterSource = new Datasource();
$masterSource->DataSet = $masterTable;

$detailTable = new Table();
$detailTable->MasterSource = $masterSource;
$detailTable->MasterFields = ['product_id' => 'id'];
$detailTable->Open();</pre>
    </div>

<?php
$table->Close();
$db->Close();
?>

    <p><a href="index.php">&larr; Back to Examples</a></p>
</body>
</html>
