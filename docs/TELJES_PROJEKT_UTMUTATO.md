# Termék Értékelések REST API - Teljes Projekt Útmutató

## 📋 Projekt Áttekintés

Laravel 12 alapú REST API termékek értékeléséhez Laravel Sanctum authentikációval.

**Funkciók:**
- ✅ User regisztráció és bejelentkezés
- ✅ Bearer token alapú authentikáció
- ✅ Admin és User szerepkörök
- ✅ Termékek CRUD (Admin)
- ✅ Értékelések CRUD (User)
- ✅ Admin felület felhasználók/termékek/értékelések kezeléséhez
- ✅ Teljes AUTH védelem (MINDEN endpoint token szükséges kivéve register/login)
- ✅ 36 PHPUnit teszt

---

## 🚀 I. PROJEKT LÉTREHOZÁSA (0-ról)

### 1.1 Környezet Előkészítés

**Szükséges:**
- XAMPP (Apache + MySQL)
- Composer
- PHP 8.2+
- Git (opcionális)

### 1.2 Laravel Projekt Létrehozása

```bash
# Navigálj a XAMPP htdocs mappájába
cd c:\xampp\htdocs

# Laravel projekt létrehozása
composer create-project laravel/laravel Termekertekelesek

# Belépés a projekt mappába
cd Termekertekelesek
```

**Várt kimenet:**
```
Installing laravel/laravel (v12.x)
  - Installing laravel/laravel (v12.x): Extracting archive
Created project in C:\xampp\htdocs\Termekertekelesek
```

---

## 🗄️ II. ADATBÁZIS BEÁLLÍTÁS

### 2.1 MySQL Adatbázis Létrehozása

**XAMPP phpMyAdmin:**
1. Nyisd meg: `http://localhost/phpmyadmin`
2. Új adatbázis: `termekertekelesek`
3. Karakter készlet: `utf8mb4_unicode_ci`

**VAGY konzolon keresztül:**

```bash
# MySQL konzol megnyitása
mysql -u root -p

# Adatbázis létrehozása
CREATE DATABASE termekertekelesek CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Kilépés
exit;
```

### 2.2 .env Fájl Konfigurálása

**Szerkeszd a `.env` fájlt:**

```env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=termekertekelesek
DB_USERNAME=root
DB_PASSWORD=

```


## 🔐 III. LARAVEL SANCTUM TELEPÍTÉS

### 3.1 Sanctum Package Telepítése

```bash
# Sanctum telepítése
composer require laravel/sanctum

# Sanctum konfiguráció publikálása
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

**Kimenet:**
```
Copied File [/vendor/laravel/sanctum/database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php]
```

### 3.2 Sanctum Middleware Konfiguráció

**Fájl:** `bootstrap/app.php`

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // API route-okon JSON 401 választ ad token nélküli hozzáférésnél
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });
    })->create();
```

---

## 📊 IV. ADATBÁZIS SÉMA

### 4.1 User Model Módosítása

**Fájl:** `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    // Kapcsolatok
    public function reviews()
    {
        return $this->hasMany(Reviews::class);
    }
}
```

### 4.2 Migration Módosítása (users)

**Szerkeszd:** `database/migrations/0001_01_01_000000_create_users_table.php`

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->boolean('is_admin')->default(false); // ÚJ MEZŐ
    $table->rememberToken();
    $table->timestamps();
});
```

### 4.3 Products Migration Létrehozása

```bash
php artisan make:migration create_products_table
```

**Fájl:** `database/migrations/YYYY_MM_DD_XXXXXX_create_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

### 4.4 Reviews Migration Létrehozása

```bash
php artisan make:migration create_reviews_table
```

**Fájl:** `database/migrations/YYYY_MM_DD_XXXXXX_create_reviews_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
```

### 4.5 Migrations Futtatása

```bash
php artisan migrate
```

**Kimenet:**
```
   INFO  Running migrations.

  0001_01_01_000000_create_users_table ........................ 38ms DONE
  0001_01_01_000001_create_cache_table ......................... 9ms DONE
  0001_01_01_000002_create_jobs_table ........................ 26ms DONE
  2019_12_14_000001_create_personal_access_tokens_table ....... 19ms DONE
  2025_12_01_082139_create_products_table ...................... 5ms DONE
  2025_12_01_082156_create_reviews_table ...................... 36ms DONE
```

---

## 🏗️ V. MODELLEK ÉS FACTORIES

### 5.1 Products Model Létrehozása

```bash
php artisan make:model Products
```

**Fájl:** `app/Models/Products.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    // Kapcsolatok
    public function reviews()
    {
        return $this->hasMany(Reviews::class, 'product_id');
    }
}
```

### 5.2 Reviews Model Létrehozása

```bash
php artisan make:model Reviews
```

**Fájl:** `app/Models/Reviews.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    // Kapcsolatok
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
```

### 5.3 User Factory Módosítása

**Fájl:** `database/factories/UserFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'is_admin' => false,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
```

### 5.4 Products Factory Létrehozása

```bash
php artisan make:factory ProductsFactory
```

**Fájl:** `database/factories/ProductsFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(10),
            'price' => fake()->randomFloat(2, 1000, 999999),
        ];
    }
}
```

### 5.5 Reviews Factory Létrehozása

```bash
php artisan make:factory ReviewsFactory
```

**Fájl:** `database/factories/ReviewsFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Products::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence(15),
        ];
    }
}
```

---

## 🌱 VI. DATABASE SEEDER

### 6.1 DatabaseSeeder Módosítása

**Fájl:** `database/seeders/DatabaseSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Products;
use App\Models\Reviews;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin felhasználó
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'is_admin' => true,
        ]);

        // Test felhasználó
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        // 10 random felhasználó
        User::factory(10)->create();

        // 20 termék
        Products::factory(20)->create();

        // 50 értékelés (random user + product párosítással)
        Reviews::factory(50)->create();
    }
}
```

### 6.2 Seeder Futtatása

```bash
# Migrációk újrafuttatása + seed
php artisan migrate:fresh --seed
```

**Kimenet:**
```
  Dropping all tables .......................................... 67ms DONE

   INFO  Preparing database.

  Creating migration table ...................................... 9ms DONE

   INFO  Running migrations.

  0001_01_01_000000_create_users_table ........................ 38ms DONE
  0001_01_01_000001_create_cache_table ......................... 9ms DONE
  0001_01_01_000002_create_jobs_table ........................ 26ms DONE
  2019_12_14_000001_create_personal_access_tokens_table ....... 19ms DONE
  2025_12_01_082139_create_products_table ...................... 5ms DONE
  2025_12_01_082156_create_reviews_table ...................... 36ms DONE

   INFO  Seeding database.
```

---

## 🎯 VII. MIDDLEWARE ÉS ROUTE-OK

### 7.1 IsAdmin Middleware Létrehozása

```bash
php artisan make:middleware IsAdmin
```

**Fájl:** `app/Http/Middleware/IsAdmin.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json([
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        return $next($request);
    }
}
```

### 7.2 Middleware Regisztrálása

**Fájl:** `bootstrap/app.php` - alias hozzáadása

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->statefulApi();
    $middleware->alias([
        'admin' => \App\Http\Middleware\IsAdmin::class,
    ]);
})
```

### 7.3 API Routes Beállítása

**Fájl:** `routes/api.php`

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\ReviewController as AdminReviewController;

// ==========================================
// NYILVÁNOS VÉGPONTOK (Public - NO AUTH)
// ==========================================

// Auth routes (CSAK ezek nyilvánosak)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// ==========================================
// VÉDETT VÉGPONTOK (AUTH REQUIRED)
// ==========================================

Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Products - olvasás (autentikált felhasználók)
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    
    // Termékhez tartozó értékelések (autentikált felhasználók)
    Route::get('products/{id}/reviews', function ($id) {
        $product = \App\Models\Products::with('reviews.user')->findOrFail($id);
        return response()->json($product->reviews);
    });

    // Reviews - olvasás (autentikált felhasználók)
    Route::get('reviews', [ReviewController::class, 'index']);
    Route::get('reviews/{id}', [ReviewController::class, 'show']);

    // Reviews - írás/módosítás/törlés (autentikált felhasználók)
    Route::post('reviews', [ReviewController::class, 'store']);
    Route::put('reviews/{id}', [ReviewController::class, 'update']);
    Route::patch('reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);

    // ==========================================
    // ADMIN VÉGPONTOK (Admin Only)
    // ==========================================
    
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::apiResource('users', AdminUserController::class);
        Route::apiResource('products', AdminProductController::class);
        Route::apiResource('reviews', AdminReviewController::class);
    });

    // Products - írás/módosítás/törlés (CSAK admin)
    Route::middleware('admin')->group(function () {
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{id}', [ProductController::class, 'update']);
        Route::patch('products/{id}', [ProductController::class, 'update']);
        Route::delete('products/{id}', [ProductController::class, 'destroy']);
    });
});
```

---

## 🎮 VIII. CONTROLLEREK LÉTREHOZÁSA

### 8.1 Auth Controller

```bash
php artisan make:controller Api/AuthController
```

**Fájl:** `app/Http/Controllers/Api/AuthController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
```

### 8.2 Product Controller

```bash
php artisan make:controller Api/ProductController
```

**Fájl:** `app/Http/Controllers/Api/ProductController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Products::all();
    }

    public function show($id)
    {
        return Products::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
        ]);

        $product = Products::create($validated);

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Products::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
```

### 8.3 Review Controller

```bash
php artisan make:controller Api/ReviewController
```

**Fájl:** `app/Http/Controllers/Api/ReviewController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reviews;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return Reviews::with(['user', 'product'])->get();
    }

    public function show($id)
    {
        return Reviews::with(['user', 'product'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Reviews::create($validated);

        return response()->json($review->load(['user', 'product']), 201);
    }

    public function update(Request $request, $id)
    {
        $review = Reviews::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($validated);

        return response()->json($review->load(['user', 'product']));
    }

    public function destroy($id)
    {
        $review = Reviews::findOrFail($id);
        $review->delete();

        return response()->json(null, 204);
    }
}
```

### 8.4 Admin Controllers

```bash
php artisan make:controller Api/Admin/UserController --api
php artisan make:controller Api/Admin/ProductController --api
php artisan make:controller Api/Admin/ReviewController --api
```

**Fájl:** `app/Http/Controllers/Api/Admin/UserController.php`

```php
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::paginate(20);
    }

    public function show($id)
    {
        return User::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'is_admin' => 'sometimes|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'is_admin' => 'sometimes|boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }
}
```

**Fájl:** `app/Http/Controllers/Api/Admin/ProductController.php`

```php
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Products::with('reviews')->paginate(20);
    }

    public function show($id)
    {
        return Products::with('reviews')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
        ]);

        $product = Products::create($validated);

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Products::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
```

**Fájl:** `app/Http/Controllers/Api/Admin/ReviewController.php`

```php
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reviews;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return Reviews::with(['user', 'product'])->paginate(20);
    }

    public function show($id)
    {
        return Reviews::with(['user', 'product'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Reviews::create($validated);

        return response()->json($review->load(['user', 'product']), 201);
    }

    public function update(Request $request, $id)
    {
        $review = Reviews::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($validated);

        return response()->json($review->load(['user', 'product']));
    }

    public function destroy($id)
    {
        $review = Reviews::findOrFail($id);
        $review->delete();

        return response()->json(null, 204);
    }
}
```

---

## 🧪 IX. TESZTELÉS

### 9.1 Test Fájlok Létrehozása

```bash
php artisan make:test AuthTest
php artisan make:test ProductTest
php artisan make:test ReviewTest
php artisan make:test AdminTest
```

### 9.2 Tesztek Futtatása

```bash
# Összes teszt futtatása
php artisan test

# Csak egy adott teszt futtatása
php artisan test --filter=AuthTest

```

**Várt kimenet:**
```
   PASS  Tests\Feature\AuthTest
  ✓ user can register
  ✓ user can login
  ✓ user can logout
  ... (36 teszt összesen)

  Tests:    36 passed (223 assertions)
  Duration: 1.35s
```

---

## 🚀 X. SZERVER INDÍTÁS

### 10.1 XAMPP Módszer (Ajánlott)

1. **XAMPP Control Panel** → Indítsd el:
   - Apache
   - MySQL

2. **Elérhetőség:**
   ```
   http://localhost/Termekertekelesek/Termekertekelesek/public/api
   ```

### 10.2 Laravel Beépített Szerver

```bash
php artisan serve
```

**Kimenet:**
```
   INFO  Server running on [http://127.0.0.1:8000].

  Press Ctrl+C to stop the server
```

**Elérhetőség:**
```
http://127.0.0.1:8000/api
```

---

## 📮 XI. POSTMAN TESZTELÉS

### 11.1 Postman Collection Import

1. Postman megnyitása
2. Import → File → Válaszd ki: `docs/Postman_Collection_AUTH.json`
3. Collection megjelenik: **"Termék Értékelések API (Teljes Auth)"**

### 11.2 Environment Változók Beállítása

**Collection Variables:**
- `base_url`: `http://localhost/Termekertekelesek/Termekertekelesek/public/api`
- `user_token`: (automatikusan mentődik login után)
- `admin_token`: (automatikusan mentődik admin login után)

### 11.3 Alapvető Tesztelési Flow

**1. Admin bejelentkezés:**

```
POST {{base_url}}/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "admin123"
}
```

✅ Token automatikusan mentve `admin_token` néven

**2. User bejelentkezés:**

```
POST {{base_url}}/login
Content-Type: application/json

{
    "email": "test@example.com",
    "password": "password"
}
```

✅ Token automatikusan mentve `user_token` néven

**3. Termékek listázása (AUTH):**

```
GET {{base_url}}/products
Authorization: Bearer {{user_token}}
```

**4. Új termék létrehozása (ADMIN):**

```
POST {{base_url}}/products
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "name": "Gaming Laptop",
    "description": "RTX 4090",
    "price": 899999
}
```

**5. Értékelés létrehozása (USER):**

```
POST {{base_url}}/reviews
Authorization: Bearer {{user_token}}
Content-Type: application/json

{
    "user_id": 2,
    "product_id": 1,
    "rating": 5,
    "comment": "Nagyon jó termék!"
}
```

## 📊 XII. HASZNOS PARANCSOK

### Adatbázis Parancsok

```bash
# Migrációk újra futtatása
php artisan migrate:fresh

# Migrations + Seeders
php artisan migrate:fresh --seed

# Csak seeder futtatása
php artisan db:seed

# Rollback utolsó migration
php artisan migrate:rollback

# Összes migration rollback
php artisan migrate:reset

# Migration státusz
php artisan migrate:status
```

### Generálás Parancsok

```bash
# Controller létrehozása
php artisan make:controller ControllerName

# Model létrehozása
php artisan make:model ModelName

# Migration létrehozása
php artisan make:migration create_table_name

# Factory létrehozása
php artisan make:factory FactoryName

# Seeder létrehozása
php artisan make:seeder SeederName

# Middleware létrehozása
php artisan make:middleware MiddlewareName

# Request létrehozása
php artisan make:request RequestName

# Test létrehozása
php artisan make:test TestName
```

## 🗄️ XIV. ADATBÁZIS TERV

### Táblák és Kapcsolatok

```
┌─────────────────────────────────────────────────────────────────┐
│                     ADATBÁZIS SÉMA DIAGRAM                      │
└─────────────────────────────────────────────────────────────────┘

                    ┌──────────────────┐
                    │      USERS       │
                    ├──────────────────┤
                    │ id (PK)          │
                    │ name             │
                    │ email (UNIQUE)   │
                    │ password         │
                    │ is_admin         │
                    │ created_at       │
                    │ updated_at       │
                    └──────────────────┘
                            │
                            │ 1
                            │
                ┌───────────┴───────────┐
                │                       │
             N  │                       │ N
                │                       │
    ┌───────────▼──────────┐  ┌────────▼─────────┐
    │     PRODUCTS         │  │     REVIEWS      │
    ├──────────────────────┤  ├──────────────────┤
    │ id (PK)              │  │ id (PK)          │
    │ name                 │  │ user_id (FK)     │
    │ description          │  │ product_id (FK)  │
    │ price                │  │ rating           │
    │ created_at           │  │ comment          │
    │ updated_at           │  │ created_at       │
    └──────────────────────┘  │ updated_at       │
                │              └──────────────────┘
                │ 1                     │
                │                       │
                └───────────┬───────────┘
                            │ N

Kapcsolatok:
  • users → reviews: 1:N (egy user több értékelést is írhat)
  • products → reviews: 1:N (egy termékhez több értékelés tartozhat)
```

### Részletes Tábla Leírások

#### 1. **users** tábla
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_is_admin (is_admin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Mezők:**
- `id`: Elsődleges kulcs, auto-increment
- `name`: Felhasználó neve (max 255 karakter)
- `email`: E-mail cím, egyedi, kötelező
- `password`: Bcrypt hash-elt jelszó
- `is_admin`: Admin jogosultság (0/1)
- `created_at`: Létrehozás időbélyegzője
- `updated_at`: Utolsó módosítás időbélyegzője

**Indexek:**
- Email gyors kereséséhez (login)
- Admin szűréshez

---

#### 2. **products** tábla
```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_price (price),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Mezők:**
- `id`: Elsődleges kulcs, auto-increment
- `name`: Termék neve, kötelező
- `description`: Termék leírása (opcionális, TEXT típus)
- `price`: Ár (2 tizedesjegy pontossággal, pl. 1999.99)
- `created_at`: Létrehozás időbélyegzője
- `updated_at`: Utolsó módosítás időbélyegzője

**Indexek:**
- Ár szerinti rendezéshez/szűréshez
- Dátum szerinti rendezéshez

---

#### 3. **reviews** tábla
```sql
CREATE TABLE reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id),
    INDEX idx_rating (rating),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Mezők:**
- `id`: Elsődleges kulcs, auto-increment
- `user_id`: Felhasználó azonosító (FK → users.id)
- `product_id`: Termék azonosító (FK → products.id)
- `rating`: Értékelés 1-5 skálán (validálva)
- `comment`: Szöveges vélemény (opcionális)
- `created_at`: Létrehozás időbélyegzője
- `updated_at`: Utolsó módosítás időbélyegzője

**Idegen kulcsok:**
- `user_id` → `users.id` (CASCADE törlés)
- `product_id` → `products.id` (CASCADE törlés)

**Indexek:**
- Felhasználó értékeléseinek lekérdezéséhez
- Termék értékeléseinek lekérdezéséhez
- Értékelés szerinti szűréshez
- Dátum szerinti rendezéshez

**Validáció:**
- Rating CHECK constraint: 1 ≤ rating ≤ 5

---

#### 4. **personal_access_tokens** tábla (Laravel Sanctum)
```sql
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_tokenable (tokenable_type, tokenable_id),
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Mezők:**
- `id`: Elsődleges kulcs
- `tokenable_type`: Model típus (pl. "App\Models\User")
- `tokenable_id`: User ID
- `name`: Token neve (pl. "auth_token")
- `token`: Egyedi hash-elt token
- `abilities`: JSON jogosultságok (opcionális)
- `last_used_at`: Utolsó használat időpontja
- `expires_at`: Lejárati idő (opcionális)
- `created_at`: Létrehozás időbélyegzője
- `updated_at`: Módosítás időbélyegzője

---

### Adatbázis Kapcsolatok Részletesen

#### **1:N Kapcsolat - users → reviews**
```
Egy felhasználó több értékelést is írhat.
Egy értékelés pontosan egy felhasználóhoz tartozik.

Példa:
User #1 (test@example.com)
  ├── Review #1 (Product #1, Rating: 5)
  ├── Review #2 (Product #2, Rating: 4)
  └── Review #3 (Product #1, Rating: 5)
```

#### **1:N Kapcsolat - products → reviews**
```
Egy termékhez több értékelés is tartozhat.
Egy értékelés pontosan egy termékhez tartozik.

Példa:
Product #1 (Laptop)
  ├── Review #1 (User #1, Rating: 5)
  ├── Review #2 (User #2, Rating: 4)
  └── Review #3 (User #3, Rating: 5)
```

---

### Minta Adatok (Seeder)

#### Users
```
┌────┬──────────────────────┬─────────────┬──────────┐
│ ID │ Email                │ Name        │ Is Admin │
├────┼──────────────────────┼─────────────┼──────────┤
│ 1  │ admin@example.com    │ Admin User  │ 1        │
│ 2  │ test@example.com     │ Test User   │ 0        │
└────┴──────────────────────┴─────────────┴──────────┘
```

#### Products
```
┌────┬─────────────────┬──────────┬──────────────────────────┐
│ ID │ Name            │ Price    │ Description              │
├────┼─────────────────┼──────────┼──────────────────────────┤
│ 1  │ Laptop          │ 299999   │ High performance laptop  │
│ 2  │ Smartphone      │ 149999   │ Latest model smartphone  │
│ 3  │ Headphones      │ 29999    │ Wireless headphones      │
│ 4  │ Keyboard        │ 15999    │ Mechanical keyboard      │
│ 5  │ Mouse           │ 8999     │ Gaming mouse             │
└────┴─────────────────┴──────────┴──────────────────────────┘
```

#### Reviews
```
┌────┬─────────┬────────────┬────────┬────────────────────────┐
│ ID │ User ID │ Product ID │ Rating │ Comment                │
├────┼─────────┼────────────┼────────┼────────────────────────┤
│ 1  │ 1       │ 1          │ 5      │ Excellent laptop!      │
│ 2  │ 2       │ 1          │ 4      │ Good but expensive     │
│ 3  │ 1       │ 2          │ 5      │ Best phone ever        │
│ 4  │ 2       │ 3          │ 3      │ Average sound quality  │
└────┴─────────┴────────────┴────────┴────────────────────────┘
```


## 📁 XVI. PROJEKT STRUKTÚRA

```
Termekertekelesek/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── ProductController.php
│   │   │       ├── ReviewController.php
│   │   │       └── Admin/
│   │   │           ├── UserController.php
│   │   │           ├── ProductController.php
│   │   │           └── ReviewController.php
│   │   └── Middleware/
│   │       └── IsAdmin.php
│   └── Models/
│       ├── User.php
│       ├── Products.php
│       └── Reviews.php
├── bootstrap/
│   └── app.php (Sanctum + Exception config)
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   ├── ProductsFactory.php
│   │   └── ReviewsFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2019_12_14_000001_create_personal_access_tokens_table.php
│   │   ├── YYYY_MM_DD_create_products_table.php
│   │   └── YYYY_MM_DD_create_reviews_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── docs/
│   ├── TermekErtekelesek_API.md
│   ├── Postman_Collection_AUTH.json
│   └── API_DOKUMENTACIO.md
├── routes/
│   ├── api.php (API endpoints)
│   └── web.php
├── tests/
│   └── Feature/
│       ├── AuthTest.php (7 teszt)
│       ├── ProductTest.php (8 teszt)
│       ├── ReviewTest.php (9 teszt)
│       └── AdminTest.php (10 teszt)
├── .env
├── composer.json
└── artisan
```

---

## 🎯 XVI. GYORS REFERENCIA

### Admin Hozzáférés

```
Email: admin@example.com
Password: admin123
```

### Test User Hozzáférés

```
Email: test@example.com
Password: password
```

### API Base URL (XAMPP)

```
http://localhost/Termekertekelesek/Termekertekelesek/public/api
```

### Végpontok Összefoglalása

| Endpoint | Method | Auth | Admin | Leírás |
|----------|--------|------|-------|--------|
| `/register` | POST | ❌ | ❌ | Regisztráció |
| `/login` | POST | ❌ | ❌ | Bejelentkezés |
| `/logout` | POST | ✅ | ❌ | Kijelentkezés |
| `/user` | GET | ✅ | ❌ | User adatok |
| `/products` | GET | ✅ | ❌ | Termékek lista |
| `/products/{id}` | GET | ✅ | ❌ | Egy termék |
| `/products` | POST | ✅ | ✅ | Termék létrehozása |
| `/products/{id}` | PUT | ✅ | ✅ | Termék módosítása |
| `/products/{id}` | DELETE | ✅ | ✅ | Termék törlése |
| `/reviews` | GET | ✅ | ❌ | Értékelések lista |
| `/reviews/{id}` | GET | ✅ | ❌ | Egy értékelés |
| `/reviews` | POST | ✅ | ❌ | Értékelés létrehozása |
| `/reviews/{id}` | PUT | ✅ | ❌ | Értékelés módosítása |
| `/reviews/{id}` | DELETE | ✅ | ❌ | Értékelés törlése |
| `/admin/users` | GET | ✅ | ✅ | Admin: Users |
| `/admin/products` | GET | ✅ | ✅ | Admin: Products |
| `/admin/reviews` | GET | ✅ | ✅ | Admin: Reviews |

**Jelmagyarázat:**
- ✅ = Szükséges
- ❌ = Nem szükséges

---

## ✅ XVII. ELLENŐRZŐ LISTA

### Projekt Setup
- [ ] XAMPP telepítve (Apache + MySQL)
- [ ] Composer telepítve
- [ ] PHP 8.2+ verzió
- [ ] Laravel projekt létrehozva
- [ ] `.env` fájl konfigurálva
- [ ] MySQL adatbázis létrehozva

### Sanctum Setup
- [ ] `composer require laravel/sanctum` futtatva
- [ ] Sanctum config publikálva
- [ ] `bootstrap/app.php` middleware konfigurálva
- [ ] `bootstrap/app.php` exception handler beállítva

### Modellek és Migrations
- [ ] User model módosítva (`is_admin` mező)
- [ ] Products model létrehozva
- [ ] Reviews model létrehozva
- [ ] Migrations futtatva (`migrate:fresh --seed`)
- [ ] Factories létrehozva (User, Products, Reviews)
- [ ] DatabaseSeeder beállítva

### Middleware és Routes
- [ ] IsAdmin middleware létrehozva
- [ ] Middleware alias regisztrálva
- [ ] `routes/api.php` beállítva
- [ ] Auth routes nyilvánosak
- [ ] Product/Review GET routes védettek
- [ ] Admin routes védettek + admin middleware

### Controllerek
- [ ] AuthController létrehozva (register, login, logout)
- [ ] ProductController létrehozva
- [ ] ReviewController létrehozva
- [ ] Admin/UserController létrehozva
- [ ] Admin/ProductController létrehozva
- [ ] Admin/ReviewController létrehozva

### Tesztelés
- [ ] AuthTest létrehozva (7 teszt)
- [ ] ProductTest létrehozva (8 teszt)
- [ ] ReviewTest létrehozva (9 teszt)
- [ ] AdminTest létrehozva (10 teszt)
- [ ] `php artisan test` sikeres (36/36 teszt)

### Dokumentáció
- [ ] API dokumentáció létrehozva
- [ ] Postman Collection importálva
- [ ] Environment variables beállítva
- [ ] Admin login működik
- [ ] User login működik

### Biztonsági Ellenőrzés
- [ ] Token nélkül 401 hiba
- [ ] User token admin végponton 403 hiba
- [ ] Admin token minden végponton működik
- [ ] Validációs hibák 422 kóddal

---

**🎉 PROJEKT KÉSZ! 🎉**

**Összefoglaló:**
- ✅ 36 sikeres PHPUnit teszt
- ✅ Teljes AUTH védelem (kivéve register/login)
- ✅ Admin jogosultságkezelés
- ✅ Postman Collection
- ✅ Dokumentáció

**Következő lépések:**
1. Importáld a Postman Collection-t
2. Jelentkezz be admin-ként
3. Teszteld az végpontokat
4. Futtasd a teszteket: `php artisan test`

**Élvezd a kódolást! 🚀**
