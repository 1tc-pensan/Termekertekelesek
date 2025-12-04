# API Dokumentáció - Termék Értékelések

## Base URL
```
http://localhost/Termekertekelesek/Termekertekelesek/public/api
```

---

## 1. TERMÉKEK (Products)

### 1.1 Összes termék lekérése
**GET** `/products`

**Válasz:**
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

---

### 1.2 Új termék létrehozása
**POST** `/products`

**Headers:**
```
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "name": "Laptop",
    "description": "Gaming laptop",
    "price": 299999
}
```

**Válasz (201):**
```json
{
    "id": 1,
    "name": "Laptop",
    "description": "Gaming laptop",
    "price": "299999.00",
    "created_at": "2025-12-04T10:00:00.000000Z",
    "updated_at": "2025-12-04T10:00:00.000000Z"
}
```

---

### 1.3 Egy termék lekérése
**GET** `/products/{id}`

**Példa:** `/products/1`

**Válasz:**
```json
{
    "id": 1,
    "name": "Laptop",
    "description": "Gaming laptop",
    "price": "299999.00",
    "created_at": "2025-12-04T10:00:00.000000Z",
    "updated_at": "2025-12-04T10:00:00.000000Z"
}
```

---

### 1.4 Termék frissítése
**PUT** `/products/{id}` vagy **PATCH** `/products/{id}`

**Headers:**
```
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "name": "Gaming Laptop",
    "price": 349999
}
```

**Válasz:**
```json
{
    "id": 1,
    "name": "Gaming Laptop",
    "description": "Gaming laptop",
    "price": "349999.00",
    "created_at": "2025-12-04T10:00:00.000000Z",
    "updated_at": "2025-12-04T11:00:00.000000Z"
}
```

---

### 1.5 Termék törlése
**DELETE** `/products/{id}`

**Válasz (204):** Nincs tartalom

---

### 1.6 Termékhez tartozó értékelések
**GET** `/products/{id}/reviews`

**Példa:** `/products/1/reviews`

**Válasz:**
```json
[
    {
        "id": 1,
        "user_id": 1,
        "product_id": 1,
        "rating": 5,
        "comment": "Nagyon jó termék!",
        "created_at": "2025-12-04T10:30:00.000000Z",
        "updated_at": "2025-12-04T10:30:00.000000Z",
        "user": {
            "id": 1,
            "name": "Teszt Felhasználó",
            "email": "teszt@example.com"
        }
    }
]
```

---

## 2. ÉRTÉKELÉSEK (Reviews)

### 2.1 Összes értékelés lekérése
**GET** `/reviews`

**Válasz:**
```json
[
    {
        "id": 1,
        "user_id": 1,
        "product_id": 1,
        "rating": 5,
        "comment": "Nagyon jó termék!",
        "created_at": "2025-12-04T10:30:00.000000Z",
        "updated_at": "2025-12-04T10:30:00.000000Z",
        "user": {
            "id": 1,
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
]
```

---

### 2.2 Új értékelés létrehozása
**POST** `/reviews`

**Headers:**
```
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "user_id": 1,
    "product_id": 1,
    "rating": 5,
    "comment": "Nagyon jó termék!"
}
```

**Validáció:**
- `user_id`: kötelező, létező user ID
- `product_id`: kötelező, létező termék ID
- `rating`: kötelező, 1-5 közötti egész szám
- `comment`: opcionális, szöveg

**Válasz (201):**
```json
{
    "id": 1,
    "user_id": 1,
    "product_id": 1,
    "rating": 5,
    "comment": "Nagyon jó termék!",
    "created_at": "2025-12-04T10:30:00.000000Z",
    "updated_at": "2025-12-04T10:30:00.000000Z",
    "user": {
        "id": 1,
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

---

### 2.3 Egy értékelés lekérése
**GET** `/reviews/{id}`

**Példa:** `/reviews/1`

**Válasz:**
```json
{
    "id": 1,
    "user_id": 1,
    "product_id": 1,
    "rating": 5,
    "comment": "Nagyon jó termék!",
    "created_at": "2025-12-04T10:30:00.000000Z",
    "updated_at": "2025-12-04T10:30:00.000000Z",
    "user": {
        "id": 1,
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

---

### 2.4 Értékelés frissítése
**PUT** `/reviews/{id}` vagy **PATCH** `/reviews/{id}`

**Headers:**
```
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "rating": 4,
    "comment": "Jó termék, de drága."
}
```

**Megjegyzés:** A `user_id` és `product_id` nem módosítható.

**Válasz:**
```json
{
    "id": 1,
    "user_id": 1,
    "product_id": 1,
    "rating": 4,
    "comment": "Jó termék, de drága.",
    "created_at": "2025-12-04T10:30:00.000000Z",
    "updated_at": "2025-12-04T11:30:00.000000Z",
    "user": {
        "id": 1,
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

---

### 2.5 Értékelés törlése
**DELETE** `/reviews/{id}`

**Válasz (204):** Nincs tartalom

---

## 3. HIBAÜZENETEK

### 404 Not Found
```json
{
    "message": "No query results for model [App\\Models\\Products] 1"
}
```

### 422 Validation Error
```json
{
    "message": "The rating field is required. (and 1 more error)",
    "errors": {
        "rating": [
            "The rating field is required."
        ],
        "user_id": [
            "The user id field is required."
        ]
    }
}
```

---

## 4. TESZTELÉS LÉPÉSEI POSTMAN-BEN

### 4.1 Termék létrehozása
1. Új request: **POST** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/products`
2. Headers: `Content-Type: application/json`
3. Body → raw → JSON:
```json
{
    "name": "Laptop",
    "description": "Gaming laptop",
    "price": 299999
}
```
4. Send

### 4.2 User létrehozása (ha nincs még)
Laravel alapértelmezetten nem biztosít user regisztrációs endpoint-ot az API-ban. Létrehozhatsz user-t:
- Tinker-rel: `php artisan tinker` majd:
```php
\App\Models\User::create(['name' => 'Teszt', 'email' => 'teszt@example.com', 'password' => bcrypt('password')]);
```

### 4.3 Értékelés létrehozása
1. Új request: **POST** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/reviews`
2. Headers: `Content-Type: application/json`
3. Body → raw → JSON:
```json
{
    "user_id": 1,
    "product_id": 1,
    "rating": 5,
    "comment": "Nagyon jó termék!"
}
```
4. Send

### 4.4 Termék értékeléseinek lekérése
1. Új request: **GET** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/products/1/reviews`
2. Send
