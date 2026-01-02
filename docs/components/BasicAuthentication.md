# BasicAuthentication

Performs HTTP Basic Authentication for page protection.

**Namespace:** `VCL\Auth`
**File:** `src/VCL/Auth/BasicAuthentication.php`
**Extends:** `Component`

## Usage

```php
use VCL\Auth\BasicAuthentication;

$auth = new BasicAuthentication($this);
$auth->Name = "Auth1";
$auth->Title = "Protected Area";
$auth->Username = "admin";
$auth->Password = "secret";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Title` | `string` | `'Login'` | Dialog title/realm |
| `ErrorMessage` | `string` | `'Unauthorized'` | Message on auth failure |
| `Username` | `string` | `''` | Valid username |
| `Password` | `string` | `''` | Valid password |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnAuthenticate` | `?string` | Custom authentication handler |

## Methods

| Method | Description |
|--------|-------------|
| `Execute()` | Perform authentication (prompts if needed) |
| `isAuthenticated()` | Check if currently authenticated |
| `getAuthenticatedUsername()` | Get current username |

## Basic Usage

```php
class ProtectedPage extends Page
{
    public ?BasicAuthentication $Auth = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->Auth = new BasicAuthentication($this);
        $this->Auth->Name = "Auth";
        $this->Auth->Title = "Admin Area";
        $this->Auth->Username = "admin";
        $this->Auth->Password = "secretpassword";
        $this->Auth->ErrorMessage = "Invalid credentials. Access denied.";
    }

    public function show(): void
    {
        // Require authentication before showing page
        $this->Auth->Execute();

        parent::show();
    }
}
```

## Custom Authentication

Use `OnAuthenticate` for database or custom validation:

```php
class MyPage extends Page
{
    public ?BasicAuthentication $Auth = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->Auth = new BasicAuthentication($this);
        $this->Auth->Name = "Auth";
        $this->Auth->Title = "User Login";
        $this->Auth->OnAuthenticate = "AuthHandler";
    }

    public function AuthHandler(object $sender, array $params): bool
    {
        $username = $params['username'];
        $password = $params['password'];

        // Check against database
        $query = $this->DB->Execute(
            "SELECT id FROM users WHERE username = " .
            $this->DB->QuoteStr($username) .
            " AND password_hash = " .
            $this->DB->QuoteStr(password_hash($password, PASSWORD_DEFAULT))
        );

        return mysqli_num_rows($query) > 0;
    }

    public function show(): void
    {
        $this->Auth->Execute();
        parent::show();
    }
}
```

## Checking Authentication Status

```php
if ($this->Auth->isAuthenticated()) {
    $user = $this->Auth->getAuthenticatedUsername();
    echo "Welcome, " . htmlspecialchars($user);
}
```

## HTTP Headers

On failed authentication, sends:

```
WWW-Authenticate: Basic realm="Admin Area"
HTTP/1.0 401 Unauthorized
```

## Notes

- Uses browser's native login dialog
- Credentials sent with every request (no session)
- Not secure over HTTP (use HTTPS)
- For simple protection; use session-based auth for complex needs
