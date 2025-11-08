# Sagari Framework

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)

Sagari Framework adalah kerangka kerja PHP ringan yang terinspirasi oleh Laravel, dirancang untuk membangun aplikasi backend dengan struktur yang bersih, modular, dan mudah dipelihara.

## 🚀 Fitur Utama

- **Routing**: Mendukung routing dasar, grup route, dan parameter dinamis
- **Middleware**: Sistem middleware untuk menangani otentikasi, CORS, rate limiting, dan lainnya
- **Dependency Injection Container**: Container sederhana untuk manajemen dependensi
- **Database Abstraction**: Koneksi database menggunakan PDO dengan metode dasar (select, insert, update, delete)
- **Model & ORM Sederhana**: Kelas Model dasar untuk interaksi dengan database
- **Konfigurasi**: Manajemen konfigurasi melalui file `.env` dan file PHP terpisah
- **Manajemen File**: Fasilitas upload dan manajemen file
- **Caching**: Sistem caching berbasis file

## 📋 Prasyarat

- PHP >= 8.0
- Composer
- Database (MySQL, PostgreSQL, dll. - disesuaikan di `.env`)

## 🔧 Instalasi

### 1. Clone atau Unduh Repository

```bash
git clone <url> sagari-framework
cd sagari-framework
```

### 2. Instal Dependency

```bash
composer install
```

### 3. Konfigurasi Lingkungan

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Buka file `.env` dan sesuaikan konfigurasi seperti nama database, username, dan password.

### 4. Konfigurasi Web Server

#### XAMPP (Rekomendasi - Virtual Host)

1. Tambahkan entri virtual host di `httpd-vhosts.conf` (XAMPP):

```apache
<VirtualHost *:80>
    DocumentRoot "path/ke/sagari-framework/public"
    ServerName sagari-framework.local
    <Directory "path/ke/sagari-framework/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

2. Tambahkan `127.0.0.1 sagari-framework.local` ke file hosts di `C:\Windows\System32\drivers\etc\hosts`

3. Restart Apache

4. Akses aplikasi di `http://sagari-framework.local`

#### Development Server PHP (Internal)

Pastikan Anda berada di root directory proyek (tempat `composer.json` berada), lalu jalankan:

```bash
php serve.php
```

## 📁 Struktur Direktori

```
project-root/
├── app/
│   ├── Controllers/
│   ├── Middleware/
│   ├── Models/
│   └── Services/
├── config/
├── core/
├── database/
│   └── migrations/  (Opsional)
├── public/
│   └── index.php
├── routes/
├── storage/
│   ├── cache/
│   ├── logs/
│   └── uploads/
├── vendor/
├── .env
├── .env.example
├── composer.json
├── serve.php
└── README.md
```

## 💻 Penggunaan

### Routing

Route didefinisikan di `routes/web.php` (untuk halaman web) dan `routes/api.php` (untuk API).

**Contoh:**

```php
// routes/web.php
$router->get('/', function($request) {
    return response()->json(['message' => 'Hello World!']);
});

$router->get('/users/{id}', 'UserController@show');
```

### Controller

Controller harus ditempatkan di `app/Controllers/` dan mewarisi dari `Core\Controller`.

**Contoh:**

```php
// app/Controllers/UserController.php
namespace App\Controllers;

use Core\Controller;
use Core\Request;

class UserController extends Controller
{
    public function show(Request $request, $id)
    {
        // Ambil data user berdasarkan $id
        $user = /* ... */;

        if (!$user) {
            return $this->error('User not found', 404);
        }

        return $this->success('User retrieved', $user);
    }
}
```

### Model

Model harus ditempatkan di `app/Models/` dan mewarisi dari `App\Models\Model`.

**Contoh:**

```php
// app/Models/User.php
namespace App\Models;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
}
```

### Middleware

Middleware harus ditempatkan di `app/Middleware/` dan mewarisi dari `Core\Middleware`.

**Contoh:**

```php
// app/Middleware/AuthMiddleware.php
namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;

class AuthMiddleware extends Middleware
{
    public function handle(Request $request, $next)
    {
        // Logika otentikasi
        $token = $request->bearerToken();

        if (!$token /* atau token tidak valid */) {
            $response = new Response();
            return $response->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
```

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan buat Pull Request atau buka Issue untuk diskusi.

## 👨‍💻 Penulis

**Atep Riandi Pahmi**  
Project Manager  
[github.com/sikutep](https://github.com/sikutep)

## 📄 License

Proyek ini dilisensikan di bawah [MIT License](LICENSE)

---
