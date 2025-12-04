# Termék Értékelések REST API

## Projekt Leírás

Ez a projekt egy Laravel alapú REST API, amely termékek értékelését teszi lehetővé. A rendszer támogatja a felhasználók regisztrációját, bejelentkezését, valamint a termékek és értékelések kezelését. Admin felhasználók teljes CRUD jogosultsággal rendelkeznek.

### Adatbázis Struktúra

**users** tábla:
- id (PK)
- name
- email (unique)
- password
- is_admin (boolean, default: false)
- created_at
- updated_at

**products** tábla:
- id (PK)
- name
- description
- price (decimal)
- created_at
- updated_at

**reviews** tábla:
- id (PK)
- user_id (FK -> users)
- product_id (FK -> products)
- rating (integer, 1-5)
- comment
- created_at
- updated_at

---

## I. ELŐKÉSZÍTÉS

### 1.1 Laravel Projekt Létrehozása

```bash
# XAMPP htdocs mappában
cd c:\xampp\htdocs

# Laravel projekt létrehozása
composer create-project laravel/laravel Termekertekelesek
cd Termekertekelesek
```

### 1.2 Adatbázis Beállítás

**.env fájl módosítása:**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=termekertekelesek
DB_USERNAME=root
DB_PASSWORD=
```

**Adatbázis létrehozása:**

```bash
# MySQL konzolban vagy phpMyAdmin-ban
CREATE DATABASE termekertekelesek CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 1.3 Laravel Sanctum Telepítése

```bash
# Sanctum telepítése
composer require laravel/sanctum

# Sanctum config publikálása
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Migrations futtatása
php artisan migrate
```

### 1.4 Sanctum Konfiguráció

**bootstrap/app.php:**

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

### 1.5 Migrations Létrehozása

**Users tábla módosítása (0001_01_01_000000_create_users_table.php):**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
```

**Products tábla:**

```bash
php artisan make:migration create_products_table
```

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

**Reviews tábla:**

```bash
php artisan make:migration create_reviews_table
```

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

**Migrations futtatása:**

```bash
php artisan migrate
```

### 1.6 Models Létrehozása

**User Model (app/Models/User.php):**

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
        return $this->hasMany(Reviews::class, 'user_id');
    }
}
```

**Product Model:**

```bash
php artisan make:model Products
```

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

    // Kapcsolatok
    public function reviews()
    {
        return $this->hasMany(Reviews::class, 'product_id');
    }
}
```

**Review Model:**

```bash
php artisan make:model Reviews
```

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

### 1.7 Factories Létrehozása

**ProductsFactory (database/factories/ProductsFactory.php):**

```bash
php artisan make:factory ProductsFactory
```

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
            'description' => fake()->sentence(20),
            'price' => fake()->randomFloat(2, 1000, 500000),
        ];
    }
}
```

**ReviewsFactory (database/factories/ReviewsFactory.php):**

```bash
php artisan make:factory ReviewsFactory
```

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

### 1.8 Seeders Létrehozása

**DatabaseSeeder (database/seeders/DatabaseSeeder.php):**

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
        // Admin user létrehozása
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'is_admin' => true,
            'password' => bcrypt('admin123'),
        ]);

        // 10 user létrehozása
        $users = User::factory(10)->create();

        // 20 termék létrehozása
        $products = Products::factory(20)->create();

        // 50 értékelés létrehozása (random userek és termékek)
        Reviews::factory(50)->create([
            'user_id' => fn() => $users->random()->id,
            'product_id' => fn() => $products->random()->id,
        ]);

        // Teszt user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
```

**Seeder futtatása:**

```bash
php artisan db:seed
```

vagy újra létrehozás:

```bash
php artisan migrate:fresh --seed
```

### 1.9 Admin Middleware Létrehozása

```bash
php artisan make:middleware IsAdmin
```

**app/Http/Middleware/IsAdmin.php:**

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

---

## II. CONTROLLEREK ÉS VÉGPONTOK

### 2.1 Auth Controller

```bash
php artisan make:controller Api/AuthController
```

**app/Http/Controllers/Api/AuthController.php:**

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

        $token = $user->createToken('auth_token')->plainTextToken;

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

        $token = $user->createToken('auth_token')->plainTextToken;

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
            'message' => 'Logged out successfully',
        ]);
    }
}
```

### 2.2 Product Controller

```bash
php artisan make:controller Api/ProductController --api
```

**app/Http/Controllers/Api/ProductController.php:**

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/products
    public function index()
    {
        $products = Products::all();
        return response()->json($products);
    }

    // POST /api/products (ADMIN ONLY)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Products::create($validated);
        return response()->json($product, 201);
    }

    // GET /api/products/{id}
    public function show(string $id)
    {
        $product = Products::findOrFail($id);
        return response()->json($product);
    }

    // PUT/PATCH /api/products/{id} (ADMIN ONLY)
    public function update(Request $request, string $id)
    {
        $product = Products::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $product->update($validated);
        return response()->json($product);
    }

    // DELETE /api/products/{id} (ADMIN ONLY)
    public function destroy(string $id)
    {
        $product = Products::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}
```

### 2.3 Review Controller

```bash
php artisan make:controller Api/ReviewController --api
```

**app/Http/Controllers/Api/ReviewController.php:**

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reviews;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // GET /api/reviews
    public function index()
    {
        $reviews = Reviews::with(['user', 'product'])->get();
        return response()->json($reviews);
    }

    // POST /api/reviews (AUTH REQUIRED)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Reviews::create($validated);
        $review->load(['user', 'product']);
        
        return response()->json($review, 201);
    }

    // GET /api/reviews/{id}
    public function show(string $id)
    {
        $review = Reviews::with(['user', 'product'])->findOrFail($id);
        return response()->json($review);
    }

    // PUT/PATCH /api/reviews/{id} (AUTH REQUIRED)
    public function update(Request $request, string $id)
    {
        $review = Reviews::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($validated);
        $review->load(['user', 'product']);
        
        return response()->json($review);
    }

    // DELETE /api/reviews/{id} (AUTH REQUIRED)
    public function destroy(string $id)
    {
        $review = Reviews::findOrFail($id);
        $review->delete();
        return response()->json(null, 204);
    }
}
```

### 2.4 Admin Controllers

**Admin User Controller:**

```bash
php artisan make:controller Api/Admin/UserController --api
```

**app/Http/Controllers/Api/Admin/UserController.php:**

```php
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET /api/admin/users
    public function index()
    {
        $users = User::with('reviews')->paginate(20);
        return response()->json($users);
    }

    // POST /api/admin/users
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'is_admin' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        
        return response()->json($user, 201);
    }

    // GET /api/admin/users/{id}
    public function show(string $id)
    {
        $user = User::with('reviews')->findOrFail($id);
        return response()->json($user);
    }

    // PUT/PATCH /api/admin/users/{id}
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'is_admin' => 'boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return response()->json($user);
    }

    // DELETE /api/admin/users/{id}
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }
}
```

**Admin Product Controller:**

```bash
php artisan make:controller Api/Admin/ProductController --api
```

**app/Http/Controllers/Api/Admin/ProductController.php:**

```php
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/admin/products
    public function index()
    {
        $products = Products::with('reviews')->paginate(20);
        return response()->json($products);
    }

    // POST /api/admin/products
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Products::create($validated);
        return response()->json($product, 201);
    }

    // GET /api/admin/products/{id}
    public function show(string $id)
    {
        $product = Products::with('reviews')->findOrFail($id);
        return response()->json($product);
    }

    // PUT/PATCH /api/admin/products/{id}
    public function update(Request $request, string $id)
    {
        $product = Products::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $product->update($validated);
        return response()->json($product);
    }

    // DELETE /api/admin/products/{id}
    public function destroy(string $id)
    {
        $product = Products::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}
```

**Admin Review Controller:**

```bash
php artisan make:controller Api/Admin/ReviewController --api
```

**app/Http/Controllers/Api/Admin/ReviewController.php:**

```php
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reviews;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // GET /api/admin/reviews
    public function index()
    {
        $reviews = Reviews::with(['user', 'product'])->paginate(20);
        return response()->json($reviews);
    }

    // POST /api/admin/reviews
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Reviews::create($validated);
        $review->load(['user', 'product']);
        
        return response()->json($review, 201);
    }

    // GET /api/admin/reviews/{id}
    public function show(string $id)
    {
        $review = Reviews::with(['user', 'product'])->findOrFail($id);
        return response()->json($review);
    }

    // PUT/PATCH /api/admin/reviews/{id}
    public function update(Request $request, string $id)
    {
        $review = Reviews::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($validated);
        $review->load(['user', 'product']);
        
        return response()->json($review);
    }

    // DELETE /api/admin/reviews/{id}
    public function destroy(string $id)
    {
        $review = Reviews::findOrFail($id);
        $review->delete();
        return response()->json(null, 204);
    }
}
```

### 2.5 Routes Beállítása

**routes/api.php:**

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
// NYILVÁNOS VÉGPONTOK (Public Endpoints)
// ==========================================

// Auth routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Product routes (csak olvasás)
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

// Review routes (csak olvasás)
Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);

// Termékhez tartozó értékelések
Route::get('products/{id}/reviews', function ($id) {
    $product = \App\Models\Products::with('reviews.user')->findOrFail($id);
    return response()->json($product->reviews);
});

// ==========================================
// AUTENTIKÁLT VÉGPONTOK (Authenticated)
// ==========================================

Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Reviews írás/módosítás/törlés (bármely bejelentkezett user)
    Route::apiResource('reviews', ReviewController::class)->except(['index', 'show']);

    // ==========================================
    // ADMIN VÉGPONTOK (Admin Only)
    // ==========================================
    
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::apiResource('users', AdminUserController::class);
        Route::apiResource('products', AdminProductController::class);
        Route::apiResource('reviews', AdminReviewController::class);
    });
});

// ==========================================
// PRODUCTS ÍRÁS/MÓDOSÍTÁS/TÖRLÉS (Admin Only)
// ==========================================

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
});
```

---

## III. TESZTELÉS ÉS DOKUMENTÁCIÓ

### 3.1 Szerver Indítása

**XAMPP használata:**

1. XAMPP Control Panel → Apache és MySQL indítása
2. Projekt elérhetősége: `http://localhost/Termekertekelesek/Termekertekelesek/public/api`

**vagy Laravel beépített szerver:**

```bash
php artisan serve
```

Elérhetőség: `http://127.0.0.1:8000/api`

### 3.2 Postman Tesztelési Lépések

#### 3.2.1 Regisztráció

**Request:**
```
POST http://localhost/Termekertekelesek/Termekertekelesek/public/api/register

Headers:
Content-Type: application/json

Body (JSON):
{
    "name": "Teszt Felhasználó",
    "email": "teszt@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Válasz (201):**
```json
{
    "user": {
        "id": 1,
        "name": "Teszt Felhasználó",
        "email": "teszt@example.com",
        "created_at": "2025-12-04T10:00:00.000000Z",
        "updated_at": "2025-12-04T10:00:00.000000Z"
    },
    "token": "1|abc123def456...",
    "token_type": "Bearer"
}
```

#### 3.2.2 Bejelentkezés (Admin)

**Request:**
```
POST http://localhost/Termekertekelesek/Termekertekelesek/public/api/login

Headers:
Content-Type: application/json

Body (JSON):
{
    "email": "admin@example.com",
    "password": "admin123"
}
```

**Válasz (200):**
```json
{
    "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@example.com",
        "is_admin": true,
        "created_at": "2025-12-04T10:00:00.000000Z",
        "updated_at": "2025-12-04T10:00:00.000000Z"
    },
    "token": "2|xyz789ghi012...",
    "token_type": "Bearer"
}
```

#### 3.2.3 Termékek Listázása (Nyilvános)

**Request:**
```
GET http://localhost/Termekertekelesek/Termekertekelesek/public/api/products
```

**Válasz (200):**
```json
[
    {
        "id": 1,
        "name": "Laptop",
        "description": "Gaming laptop",
        "price": "299999.00",
        "created_at": "2025-12-04T10:00:00.000000Z",
        "updated_at": "2025-12-04T10:00:00.000000Z"
    }
]
```

#### 3.2.4 Új Termék Létrehozása (Admin)

**Request:**
```
POST http://localhost/Termekertekelesek/Termekertekelesek/public/api/products

Headers:
Content-Type: application/json
Authorization: Bearer {admin_token}

Body (JSON):
{
    "name": "Új Laptop",
    "description": "Professzionális laptop",
    "price": 399999
}
```

**Válasz (201):**
```json
{
    "id": 21,
    "name": "Új Laptop",
    "description": "Professzionális laptop",
    "price": "399999.00",
    "created_at": "2025-12-04T14:00:00.000000Z",
    "updated_at": "2025-12-04T14:00:00.000000Z"
}
```

#### 3.2.5 Értékelés Létrehozása (User)

**Request:**
```
POST http://localhost/Termekertekelesek/Termekertekelesek/public/api/reviews

Headers:
Content-Type: application/json
Authorization: Bearer {user_token}

Body (JSON):
{
    "user_id": 2,
    "product_id": 1,
    "rating": 5,
    "comment": "Nagyon jó termék!"
}
```

**Válasz (201):**
```json
{
    "id": 51,
    "user_id": 2,
    "product_id": 1,
    "rating": 5,
    "comment": "Nagyon jó termék!",
    "created_at": "2025-12-04T15:00:00.000000Z",
    "updated_at": "2025-12-04T15:00:00.000000Z",
    "user": {
        "id": 2,
        "name": "Teszt Felhasználó",
        "email": "teszt@example.com"
    },
    "product": {
        "id": 1,
        "name": "Laptop",
        "description": "Gaming laptop",
        "price": "299999.00"
    }
}
```

### 3.3 API Végpontok Összefoglalója

| HTTP Metódus | Útvonal | Jogosultság | Leírás |
|--------------|---------|-------------|--------|
| **POST** | `/register` | 🌐 Nyilvános | Új felhasználó regisztrációja |
| **POST** | `/login` | 🌐 Nyilvános | Bejelentkezés |
| **POST** | `/logout` | 🔑 Auth | Kijelentkezés |
| **GET** | `/user` | 🔑 Auth | Bejelentkezett user adatai |
| **GET** | `/products` | 🌐 Nyilvános | Termékek listázása |
| **GET** | `/products/{id}` | 🌐 Nyilvános | Egy termék lekérése |
| **POST** | `/products` | 👑 Admin | Új termék létrehozása |
| **PUT/PATCH** | `/products/{id}` | 👑 Admin | Termék frissítése |
| **DELETE** | `/products/{id}` | 👑 Admin | Termék törlése |
| **GET** | `/products/{id}/reviews` | 🌐 Nyilvános | Termékhez tartozó értékelések |
| **GET** | `/reviews` | 🌐 Nyilvános | Értékelések listázása |
| **GET** | `/reviews/{id}` | 🌐 Nyilvános | Egy értékelés lekérése |
| **POST** | `/reviews` | 🔑 Auth | Új értékelés létrehozása |
| **PUT/PATCH** | `/reviews/{id}` | 🔑 Auth | Értékelés frissítése |
| **DELETE** | `/reviews/{id}` | 🔑 Auth | Értékelés törlése |
| **GET** | `/admin/users` | 👑 Admin | Felhasználók listázása (lapozva) |
| **POST** | `/admin/users` | 👑 Admin | Új felhasználó létrehozása |
| **GET** | `/admin/users/{id}` | 👑 Admin | Egy felhasználó lekérése |
| **PUT/PATCH** | `/admin/users/{id}` | 👑 Admin | Felhasználó frissítése |
| **DELETE** | `/admin/users/{id}` | 👑 Admin | Felhasználó törlése |
| **GET** | `/admin/products` | 👑 Admin | Termékek listázása értékelésekkel (lapozva) |
| **POST** | `/admin/products` | 👑 Admin | Új termék létrehozása |
| **GET** | `/admin/products/{id}` | 👑 Admin | Egy termék lekérése értékelésekkel |
| **PUT/PATCH** | `/admin/products/{id}` | 👑 Admin | Termék frissítése |
| **DELETE** | `/admin/products/{id}` | 👑 Admin | Termék törlése |
| **GET** | `/admin/reviews` | 👑 Admin | Értékelések listázása (lapozva) |
| **POST** | `/admin/reviews` | 👑 Admin | Új értékelés létrehozása |
| **GET** | `/admin/reviews/{id}` | 👑 Admin | Egy értékelés lekérése |
| **PUT/PATCH** | `/admin/reviews/{id}` | 👑 Admin | Értékelés frissítése |
| **DELETE** | `/admin/reviews/{id}` | 👑 Admin | Értékelés törlése |

**Jelmagyarázat:**
- 🌐 **Nyilvános**: Nincs szükség autentikációra
- 🔑 **Auth**: Bearer token szükséges
- 👑 **Admin**: Bearer token + admin jogosultság szükséges

### 3.4 Gyakori Hibák és Megoldások

**401 Unauthorized - Token hiányzik vagy érvénytelen**

Megoldás: Ellenőrizd, hogy a `Authorization: Bearer {token}` header helyesen van-e beállítva.

**403 Forbidden - Admin jogosultság hiányzik**

```json
{
    "message": "Unauthorized. Admin access required."
}
```

Megoldás: Admin user tokennel jelentkezz be.

**422 Validation Error - Hibás adatok**

```json
{
    "message": "The rating field is required.",
    "errors": {
        "rating": ["The rating field is required."]
    }
}
```

Megoldás: Ellenőrizd a kötelező mezőket.

**404 Not Found - Erőforrás nem található**

```json
{
    "message": "No query results for model [App\\Models\\Products] 1"
}
```

Megoldás: Ellenőrizd az ID-t.

### 3.5 Tesztadatok

**Admin bejelentkezés:**
- Email: `admin@example.com`
- Jelszó: `admin123`

**Test user bejelentkezés:**
- Email: `test@example.com`
- Jelszó: `password`

**Generált adatok:**
- 10 felhasználó
- 20 termék
- 50 értékelés

---

## 📝 Megjegyzések

1. **CORS beállítás**: Ha külső frontendből éred el az API-t, állítsd be a CORS-t a `config/cors.php` fájlban.

2. **Rate limiting**: Az API alapértelmezetten rate limitinget használ. Módosítsd a `app/Http/Kernel.php` fájlban, ha szükséges.

3. **Validáció**: Minden endpoint alapos validációt tartalmaz. A hibák 422-es státuszkóddal térnek vissza.

4. **Soft delete**: A user és product törlések cascade-el törlik a kapcsolódó értékeléseket is.

5. **Token lejárat**: A Sanctum tokenek alapértelmezetten nem járnak le. Beállítható a `config/sanctum.php` fájlban.

---

## 🚀 Gyors Start

```bash
# 1. Projekt klónozása/telepítése
cd c:\xampp\htdocs
composer create-project laravel/laravel Termekertekelesek

# 2. Adatbázis létrehozása
# MySQL: CREATE DATABASE termekertekelesek;

# 3. .env beállítása
# DB_DATABASE=termekertekelesek

# 4. Sanctum telepítése
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# 5. Migrations + Seeders futtatása
php artisan migrate:fresh --seed

# 6. Szerver indítása
php artisan serve
# vagy XAMPP használata
```

**API elérhető:**
- XAMPP: `http://localhost/Termekertekelesek/Termekertekelesek/public/api`
- Laravel serve: `http://127.0.0.1:8000/api`

---

**Készítette:** GitHub Copilot  
**Dátum:** 2025. december 4.  
**Laravel verzió:** 12.10.1  
**Sanctum verzió:** 4.x
