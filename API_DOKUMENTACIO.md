# API Dokumentáció - Termék Értékelések

## Base URL
```
http://localhost/Termekertekelesek/Termekertekelesek/public/api
```

---

## 0. AUTENTIKÁCIÓ (Auth)

### 0.1 Regisztráció
**POST** `/register`

**Headers:**
```
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "name": "Teszt Felhasználó",
    "email": "teszt@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Validáció:**
- `name`: kötelező, max 255 karakter
- `email`: kötelező, egyedi email cím
- `password`: kötelező, min 8 karakter
- `password_confirmation`: kötelező, meg kell egyezzen a password-del

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

---

### 0.2 Bejelentkezés
**POST** `/login`

**Headers:**
```
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "email": "teszt@example.com",
    "password": "password123"
}
```

**Válasz (200):**
```json
{
    "user": {
        "id": 1,
        "name": "Teszt Felhasználó",
        "email": "teszt@example.com",
        "created_at": "2025-12-04T10:00:00.000000Z",
        "updated_at": "2025-12-04T10:00:00.000000Z"
    },
    "token": "2|xyz789ghi012...",
    "token_type": "Bearer"
}
```

**Hiba (422):**
```json
{
    "message": "The provided credentials are incorrect.",
    "errors": {
        "email": [
            "The provided credentials are incorrect."
        ]
    }
}
```

---

### 0.3 Kijelentkezés
**POST** `/logout`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {token}
```

**Válasz (200):**
```json
{
    "message": "Logged out successfully"
}
```

---

### 0.4 Bejelentkezett felhasználó adatai
**GET** `/user`

**Headers:**
```
Authorization: Bearer {token}
```

**Válasz (200):**
```json
{
    "id": 1,
    "name": "Teszt Felhasználó",
    "email": "teszt@example.com",
    "email_verified_at": null,
    "created_at": "2025-12-04T10:00:00.000000Z",
    "updated_at": "2025-12-04T10:00:00.000000Z"
}
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

### 4.1 Regisztráció
1. Új request: **POST** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/register`
2. Headers: `Content-Type: application/json`
3. Body → raw → JSON:
```json
{
    "name": "Teszt Felhasználó",
    "email": "teszt@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```
4. Send
5. **Mentsd el a kapott token-t!**

### 4.2 Bejelentkezés
1. Új request: **POST** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/login`
2. Headers: `Content-Type: application/json`
3. Body → raw → JSON:
```json
{
    "email": "teszt@example.com",
    "password": "password123"
}
```
4. Send
5. **Mentsd el a kapott token-t!**

### 4.3 Termék létrehozása
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

### 4.4 Értékelés létrehozása (Bearer token-nel)
1. Új request: **POST** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/reviews`
2. Headers: 
   - `Content-Type: application/json`
   - `Authorization: Bearer {token}` (a login-nél kapott token)
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

### 4.5 Kijelentkezés
1. Új request: **POST** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/logout`
2. Headers: 
   - `Content-Type: application/json`
   - `Authorization: Bearer {token}`
3. Send

### 4.6 Termék értékeléseinek lekérése
1. Új request: **GET** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/products/1/reviews`
2. Send
