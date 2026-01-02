# DataModule

A non-visible container for components.

**Namespace:** `VCL\Forms`
**File:** `src/VCL/Forms/DataModule.php`
**Extends:** `CustomPage`

## Usage

```php
use VCL\Forms\DataModule;
use VCL\Database\MySQL\MySQLDatabase;
use VCL\Database\MySQL\MySQLQuery;

class SharedData extends DataModule
{
    public ?MySQLDatabase $DB = null;
    public ?MySQLQuery $UsersQuery = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = "SharedData";

        $this->DB = new MySQLDatabase($this);
        $this->DB->Name = "DB";
        $this->DB->Host = "localhost";
        $this->DB->DatabaseName = "myapp";
        $this->DB->UserName = "root";
        $this->DB->UserPassword = "";

        $this->UsersQuery = new MySQLQuery($this);
        $this->UsersQuery->Name = "UsersQuery";
        $this->UsersQuery->Database = $this->DB;
        $this->UsersQuery->SQL = "SELECT * FROM users";
    }
}
```

## Purpose

DataModule is used to:
- Hold database connections shared across pages
- Store queries and datasets for reuse
- Organize non-visual components separately from UI

## Example: Using DataModule in a Page

```php
class MyPage extends Page
{
    public ?SharedData $DataModule = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        // Create or get shared data module
        $this->DataModule = new SharedData($this);

        // Use the shared database connection
        $this->DataModule->UsersQuery->Active = true;

        // Create UI that uses the shared data
        $this->Grid = new DBGrid($this);
        $this->Grid->DataSource = $this->DataModule->UsersDS;
    }
}
```

## Key Difference from Page

| Feature | Page | DataModule |
|---------|------|------------|
| Visual output | Yes | No |
| `show()` method | Renders HTML | Does nothing |
| Contains controls | Visual controls | Non-visual only |
| Purpose | User interface | Data/logic layer |

## Notes

- DataModule's `show()` method does nothing (non-visual)
- Use for database connections, queries, business logic components
- Can be shared between multiple pages
- Helps separate data layer from presentation layer
