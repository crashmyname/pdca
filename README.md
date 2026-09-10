<p align="center">
  <img src="public/bpjs.png" alt="BPJS Framework" width="120">
</p>

<h1 align="center">BPJS Framework</h1>

<p align="center">
  <strong>Lightweight PHP MVC Framework</strong><br>
  Simple. Fast. Elegant. Built on native PDO with zero complexity.
</p>

<p align="center">
  <a href="https://packagist.org/packages/bpjs/bpjs"><img src="https://img.shields.io/packagist/v/bpjs/bpjs?color=%234f46e5&style=flat-square" alt="Latest Version"></a>
  <a href="https://packagist.org/packages/bpjs/bpjs"><img src="https://img.shields.io/packagist/php-v/bpjs/bpjs?color=%23059669&style=flat-square" alt="PHP Version"></a>
  <a href="https://packagist.org/packages/bpjs/bpjs"><img src="https://img.shields.io/packagist/dt/bpjs/bpjs?color=%23f59e0b&style=flat-square" alt="Downloads"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue?style=flat-square" alt="License"></a>
</p>

---

## 🚀 Why BPJS Framework?

✅ **Fluent ORM** — Query builder ekspresif dengan method chaining  
✅ **Powerful Routing** — Web & API routes, named routes, middleware, CSRF  
✅ **Built-in Validation** — Validasi input dengan aturan yang mudah  
✅ **CLI Tools** — Generate model, controller, migration via terminal  
✅ **Session Management** — Flash messages, user session  
✅ **Email & Queue** — Kirim email, background job processing  
✅ **Multi-Database** — MySQL, PostgreSQL, SQLite, SQL Server  
✅ **< 1MB** — Ringan, cepat, tanpa dependensi berat  

---

## 📦 Installation

```bash
composer create-project bpjs/bpjs nama_proyek_kamu
cd nama_proyek_kamu
cp .env.example .env
# Edit .env sesuai konfigurasi database kamu
php bpjs serve
# Buka http://localhost:8080
```

📁 Folder Structure
```bash
nama_proyek_kamu/
├── app/
│   ├── Controllers/       # Controller classes
│   ├── Exports/           # Data export handlers
│   ├── handle/
│   │   └── errors/        # Custom error pages
│   ├── Imports/           # Data import handlers
│   ├── Middleware/        # Custom middleware
│   ├── Models/            # Database models
│   └── Services/          # Business logic layer
├── bootstrap/
│   └── app.php            # Application bootstrap
├── config/                # Configuration files
├── database/
│   └── migrations/        # Database migrations
├── logs/                  # Application logs
├── public/                # Public assets (CSS, JS, images)
├── resources/
│   └── views/             # View templates
├── routes/
│   ├── web.php            # Web routes
│   └── api.php            # API routes
├── storage/               # Cache, sessions, uploads
├── vendor/                # Composer dependencies
├── .env                   # Environment configuration
├── .env.example           # Environment template
├── bpjs                   # CLI entry point
└── index.php              # Application entry point
```

⚡ Quick Start

Route
```php
// routes/web.php
use Bpjs\Framework\Helpers\Route;

Route::get('/', function() {
    return view('welcome', ['title' => 'Home']);
});

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
```

Controller
```php
// app/Controllers/UserController.php
namespace App\Controllers;

use App\Models\User;
use Bpjs\Framework\Core\Request;

class UserController
{
    public function index()
    {
        $users = User::all();
        return view('users/index', ['title' => 'Users', 'users' => $users]);
    }

    public function store(Request $request)
    {
        User::create([
            'name'  => $request->input('name'),
            'email' => $request->input('email'),
        ]);
        redirect('/users');
    }
}
```

Model
```php
// app/Models/User.php
namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class User extends BaseModel
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'password'];
    protected array $hidden = ['password'];
}

// Usage
$users = User::all();
$user  = User::find(1);
User::create(['name' => 'John', 'email' => 'john@mail.com']);
```

View
```php
// Render view dengan layout
View::render('users/index', [
    'title' => 'Data Users',
    'users' => $users,
], 'layouts/app');

// Return view (untuk route closure)
return view('welcome', ['title' => 'Welcome']);

// Redirect
View::redirectTo('/users');
redirect('/users');
```

Assets
```html
<!-- Images -->
<img src="<?= asset('logo.png') ?>" alt="Logo">

<!-- CSS -->
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">

<!-- JavaScript -->
<script src="<?= asset('js/app.js') ?>"></script>

<!-- Cache busting (auto versioning) -->
<link rel="stylesheet" href="<?= asset_v('css/app.css') ?>">
```

🔧 Environment (.env)

```env
APP_NAME=bpjs-framework
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bpjs
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=yoursecretkey
CRYPTO_KEY=yourkey

SESSION_LIFETIME=120
TIMEZONE=Asia/Jakarta
```

📚 Documentation
Dokumentasi lengkap tersedia di aplikasi saat kamu menjalankan php bpjs serve:

Core: Route, API Router, Controller, ORM, Model, View, DB Query Builder, TablePlus
CLI & Config: CLI, ENV
Helpers: Asset, Auth Middleware, Char, CORS, Crypto, CSRF, Date, HTTP Client, Importer, Mailer, Queue, Rate Limiter, Request, Response, Session, Store, Validator, DataTable

🤝 Contributing
Fork repository

Buat branch fitur (git checkout -b feature/amazing-feature)

Commit perubahan (git commit -m 'Add amazing feature')

Push ke branch (git push origin feature/amazing-feature)

Buka Pull Request

📄 License
MIT License — see LICENSE file.

📬 Contact
Fadli Azka Prayogi

https://img.shields.io/badge/Email-fadliazkaprayogi1%2540gmail.com-red?style=flat-square&logo=gmail
https://img.shields.io/badge/LinkedIn-fadli--azka--prayogi-blue?style=flat-square&logo=linkedin
https://img.shields.io/badge/GitHub-crashmyname-black?style=flat-square&logo=github
