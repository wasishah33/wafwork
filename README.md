# WAFWork - Minimal PHP MVC Framework

[![Latest Version on Packagist](https://img.shields.io/packagist/v/wafwork/wafwork.svg?style=flat-square)](https://packagist.org/packages/wafwork/wafwork)
[![Total Downloads](https://img.shields.io/packagist/dt/wafwork/wafwork.svg?style=flat-square)](https://packagist.org/packages/wafwork/wafwork)
[![License](https://img.shields.io/packagist/l/wafwork/wafwork.svg?style=flat-square)](https://packagist.org/packages/wafwork/wafwork)

WAFWork is a lightweight PHP MVC framework inspired by Laravel, designed for fast performance and minimal overhead while maintaining essential functionality.

## Features

- **MVC Architecture** - Clean separation of concerns with Models, Views, and Controllers
- **Simple & Intuitive Routing** - Express-style routing with support for all HTTP methods
- **Database Abstraction Layer** - Simple ORM implementation with fluent query builder
- **Template Engine** - Blade-like syntax for views with layouts, sections, and includes
- **Dependency Injection Container** - Service container for managing class dependencies
- **Environment Configuration** - Support for .env files to manage environment variables
- **Helper Functions** - Laravel-inspired helper functions for common tasks
- **Middleware Support** - Request/response filters with middleware pattern

## Versioning

WAFWork follows [Semantic Versioning](https://semver.org/). Version numbers are in the format of MAJOR.MINOR.PATCH:

- **MAJOR**: Incompatible API changes
- **MINOR**: Add functionality in a backward-compatible manner
- **PATCH**: Backward-compatible bug fixes

Check [releases page](https://github.com/wasishah33/wafwork/releases) for the latest version.

## Directory Structure

```
wafwork3/
├── app/                  # Application code
│   ├── Controllers/      # Controller classes
│   ├── Models/           # Model classes
│   ├── Middleware/       # Middleware classes
│   ├── Views/            # View templates
│   │   └── layouts/      # Layout templates
│   └── Helpers/          # Helper functions
├── config/               # Configuration files
├── database/             # Database migrations and seeds
├── framework/            # Core framework code
│   ├── Core/             # Core components
│   ├── Database/         # Database components
│   ├── Http/             # HTTP components
│   └── View/             # View components
├── public/               # Publicly accessible files
│   └── index.php         # Entry point
├── routes/               # Route definitions
│   └── web.php           # Web routes
├── storage/              # Storage for logs, cache, etc.
├── vendor/               # Dependencies (Composer)
├── .env                  # Environment variables
└── composer.json         # Composer dependencies
```

## Installation

### Requirements

- PHP 7.4 or higher
- Composer
- PDO PHP Extension

### Install via Packagist (Recommended)

The easiest way to install WAFWork is through Composer:

```bash
# Create a new project
composer create-project wafwork/wafwork your-project-name

# OR add to an existing project
composer require wafwork/wafwork
```

You can also specify a specific version:

```bash
# Install specific version
composer create-project wafwork/wafwork:^1.0 your-project-name
```

After installation, the directory structure will be automatically created, and you'll be ready to start building your application.

### Manual Installation

1. Clone the repository:

```bash
git clone https://github.com/wasishah33/wafwork.git your-project-name
cd your-project-name
```

2. Install the dependencies:

```bash
composer install
```

3. Create your environment file:

```bash
cp .env.example .env
```

4. Configure your environment variables in the `.env` file:

```bash
APP_NAME=YourAppName
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Set proper permissions:

```bash
chmod -R 775 storage
```

6. Configure your web server:

**Apache**

Ensure your Apache configuration points to the `public` directory and that `.htaccess` is enabled with `mod_rewrite`.

Example `.htaccess` for the public directory:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Nginx**

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/your-project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

7. Visit your site in a browser and verify the installation.

## Basic Usage

### Routes

Routes are defined in the `routes/web.php` file:

```php
// routes/web.php
$router->get('/', 'HomeController@index');
$router->post('/users', 'UserController@store');
$router->get('/users/{id}', 'UserController@show');
```

### Controllers

Controllers handle the incoming requests and return responses:

```php
// app/Controllers/UserController.php
namespace App\Controllers;

use WAFWork\Http\Controller;
use WAFWork\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        return $this->view('users.index', ['users' => $users]);
    }
    
    public function show(Request $request)
    {
        $user = User::find($request->param('id'));
        return $this->view('users.show', ['user' => $user]);
    }
}
```

### Models

Models represent database tables and provide an ORM interface:

```php
// app/Models/User.php
namespace App\Models;

use WAFWork\Database\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    
    // Define relationships or custom methods
    public function posts()
    {
        // Relationship implementation
    }
}

// Usage
$users = User::all();
$user = User::find(1);
$activeUsers = User::where('status', 'active');

$user = new User(['name' => 'John', 'email' => 'john@example.com']);
$user->save();
```

### Views

Views use a Blade-like template syntax:

```php
<!-- app/Views/users/index.php -->
@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <h1>Users</h1>
    
    <ul>
        @foreach($users as $user)
            <li>{{ $user->name }} - {{ $user->email }}</li>
        @endforeach
    </ul>
@endsection
```

### Middleware

Middleware provides a mechanism to filter HTTP requests:

```php
// app/Middleware/AuthMiddleware.php
namespace App\Middleware;

use WAFWork\Http\Middleware;
use WAFWork\Http\Request;
use WAFWork\Http\Response;

class AuthMiddleware implements Middleware
{
    public function handle(Request $request, callable $next)
    {
        if (!isset($_SESSION['user_id'])) {
            return redirect('/login');
        }
        
        return $next($request);
    }
}

// Usage in routes
$router->get('/dashboard', 'DashboardController@index')->middleware('auth');
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Staying Up-to-Date

To ensure you're using the latest version of WAFWork, regularly update your installation:

```bash
composer update wafwork/wafwork
```

You can check for available updates without actually installing them:

```bash
composer outdated wafwork/wafwork
```

## Security

If you discover a security vulnerability within WAFWork, please send an email to Wasif Waheed at wasishah33@gmail.com. All security vulnerabilities will be promptly addressed.

## Upgrading

When upgrading between major versions of the framework, please review the [upgrade guide](https://github.com/wasishah33/wafwork/blob/master/UPGRADING.md) for specific instructions.

## Community

- [GitHub Discussions](https://github.com/wasishah33/wafwork/discussions)
- [GitHub Issues](https://github.com/wasishah33/wafwork/issues)

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Credits

WAFWork is inspired by Laravel and other PHP frameworks, with the goal of providing a lightweight alternative that maintains essential functionality. 