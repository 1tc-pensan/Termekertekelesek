# API Dokumentáció - Termék Értékelések

## Base URL
```
http://localhost/Termekertekelesek/Termekertekelesek/public/api
```

---

## 📋 TARTALOMJEGYZÉK

1. [Autentikáció](#0-autentikáció-auth)
2. [Termékek (Products)](#1-termékek-products)
3. [Értékelések (Reviews)](#2-értékelések-reviews)
4. [Admin Végpontok](#3-admin-végpontok)
5. [Hibaüzenetek](#4-hibaüzenetek)
6. [Tesztelés](#5-tesztelés-lépései-postman-ben)
7. [Összefoglaló](#6-összefoglaló)

---

## 📌 JOGOSULTSÁGI SZINTEK

- 🌐 **Nyilvános**: Nincs szükség autentikációra
- 🔑 **Autentikált**: Bearer token szükséges
- 👑 **Admin**: Bearer token + admin jogosultság szükséges

---

## 0. AUTENTIKÁCIÓ (Auth)

### 0.1 Regisztráció 🌐
**POST** `/register`

**Jogosultság:** Nyilvános

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

### 0.2 Bejelentkezés 🌐
**POST** `/login`

**Jogosultság:** Nyilvános

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

### 0.3 Kijelentkezés 🔑
**POST** `/logout`

**Jogosultság:** Autentikált (Bearer token)

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

### 0.4 Bejelentkezett felhasználó adatai 🔑
**GET** `/user`

**Jogosultság:** Autentikált (Bearer token)

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
    "is_admin": false,
    "email_verified_at": null,
    "created_at": "2025-12-04T10:00:00.000000Z",
    "updated_at": "2025-12-04T10:00:00.000000Z"
}
```

---

## 1. TERMÉKEK (Products)

### 1.1 Összes termék lekérése 🌐
**GET** `/products`

**Jogosultság:** Nyilvános

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

---

### 1.2 Új termék létrehozása 👑
**POST** `/products`

**Jogosultság:** **ADMIN** (Bearer admin token)

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "name": "Laptop",
    "description": "Gaming laptop",
    "price": 299999
}
```

**Validáció:**
- `name`: kötelező, max 255 karakter
- `description`: opcionális, szöveg
- `price`: kötelező, numerikus érték

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

**Hiba (403) - Ha nem admin:**
```json
{
    "message": "Unauthorized. Admin access required."
}
```

---

### 1.3 Egy termék lekérése 🌐
**GET** `/products/{id}`

**Jogosultság:** Nyilvános

**Példa:** `/products/1`

**Válasz (200):**
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

### 1.4 Termék frissítése 👑
**PUT** `/products/{id}` vagy **PATCH** `/products/{id}`

**Jogosultság:** **ADMIN** (Bearer admin token)

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "name": "Gaming Laptop",
    "price": 349999
}
```

**Válasz (200):**
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

### 1.5 Termék törlése 👑
**DELETE** `/products/{id}`

**Jogosultság:** **ADMIN** (Bearer admin token)

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (204):** Nincs tartalom

---

### 1.6 Termékhez tartozó értékelések 🌐
**GET** `/products/{id}/reviews`

**Jogosultság:** Nyilvános

**Példa:** `/products/1/reviews`

**Válasz (200):**
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

### 2.1 Összes értékelés lekérése 🌐
**GET** `/reviews`

**Jogosultság:** Nyilvános

**Válasz (200):**
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

### 2.2 Új értékelés létrehozása 🔑
**POST** `/reviews`

**Jogosultság:** Autentikált (Bearer token)

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {token}
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

### 2.3 Egy értékelés lekérése 🌐
**GET** `/reviews/{id}`

**Jogosultság:** Nyilvános

**Példa:** `/reviews/1`

**Válasz (200):**
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

### 2.4 Értékelés frissítése 🔑
**PUT** `/reviews/{id}` vagy **PATCH** `/reviews/{id}`

**Jogosultság:** Autentikált (Bearer token)

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "rating": 4,
    "comment": "Jó termék, de drága."
}
```

**Megjegyzés:** A `user_id` és `product_id` nem módosítható.

**Válasz (200):**
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

### 2.5 Értékelés törlése 🔑
**DELETE** `/reviews/{id}`

**Jogosultság:** Autentikált (Bearer token)

**Headers:**
```
Authorization: Bearer {token}
```

**Válasz (204):** Nincs tartalom

---

## 3. ADMIN VÉGPONTOK

**Megjegyzés:** Az admin végpontok használatához bejelentkezett admin felhasználó szükséges. Az admin jogosultság ellenőrzése az `is_admin` mező alapján történik.

**Admin bejelentkezés:**
- Email: `admin@example.com`
- Jelszó: `admin123`

**Headers minden admin végponthoz:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

---

### 3.1 FELHASZNÁLÓK KEZELÉSE 👑

#### 3.1.1 Összes felhasználó lekérése (lapozva) 👑
**GET** `/admin/users`

**Jogosultság:** **ADMIN**

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (200):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "is_admin": true,
            "email_verified_at": null,
            "created_at": "2025-12-04T10:00:00.000000Z",
            "updated_at": "2025-12-04T10:00:00.000000Z"
        },
        {
            "id": 2,
            "name": "Teszt Felhasználó",
            "email": "teszt@example.com",
            "is_admin": false,
            "email_verified_at": null,
            "created_at": "2025-12-04T10:05:00.000000Z",
            "updated_at": "2025-12-04T10:05:00.000000Z"
        }
    ],
    "first_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/users?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/users?page=1",
    "next_page_url": null,
    "path": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/users",
    "per_page": 20,
    "prev_page_url": null,
    "to": 2,
    "total": 2
}
```

**Hiba (403):**
```json
{
    "message": "Unauthorized. Admin access required."
}
```

---

#### 3.1.2 Új felhasználó létrehozása 👑
**POST** `/admin/users`

**Jogosultság:** **ADMIN**

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "name": "Új Felhasználó",
    "email": "uj@example.com",
    "password": "password123",
    "is_admin": false
}
```

**Validáció:**
- `name`: kötelező, max 255 karakter
- `email`: kötelező, egyedi email cím
- `password`: kötelező, min 8 karakter
- `is_admin`: opcionális, boolean (alapértelmezett: false)

**Válasz (201):**
```json
{
    "id": 13,
    "name": "Új Felhasználó",
    "email": "uj@example.com",
    "is_admin": false,
    "email_verified_at": null,
    "created_at": "2025-12-04T12:00:00.000000Z",
    "updated_at": "2025-12-04T12:00:00.000000Z"
}
```

---

#### 3.1.3 Egy felhasználó lekérése 👑
**GET** `/admin/users/{id}`

**Jogosultság:** **ADMIN**

**Példa:** `/admin/users/2`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (200):**
```json
{
    "id": 2,
    "name": "Teszt Felhasználó",
    "email": "teszt@example.com",
    "is_admin": false,
    "email_verified_at": null,
    "created_at": "2025-12-04T10:05:00.000000Z",
    "updated_at": "2025-12-04T10:05:00.000000Z"
}
```

---

#### 3.1.4 Felhasználó frissítése 👑
**PUT** `/admin/users/{id}` vagy **PATCH** `/admin/users/{id}`

**Jogosultság:** **ADMIN**

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "name": "Módosított Név",
    "email": "modositott@example.com",
    "password": "ujjelszo123",
    "is_admin": true
}
```

**Megjegyzés:** A `password` mező opcionális. Ha nincs megadva, a jelenlegi jelszó nem változik.

**Validáció:**
- `name`: kötelező, max 255 karakter
- `email`: kötelező, egyedi email cím (az aktuális user email-je kivéve)
- `password`: opcionális, min 8 karakter
- `is_admin`: opcionális, boolean

**Válasz (200):**
```json
{
    "id": 2,
    "name": "Módosított Név",
    "email": "modositott@example.com",
    "is_admin": true,
    "email_verified_at": null,
    "created_at": "2025-12-04T10:05:00.000000Z",
    "updated_at": "2025-12-04T13:00:00.000000Z"
}
```

---

#### 3.1.5 Felhasználó törlése 👑
**DELETE** `/admin/users/{id}`

**Jogosultság:** **ADMIN**

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (204):** Nincs tartalom

**Megjegyzés:** A felhasználó törlése törli az összes hozzá tartozó értékelést is (cascade delete).

---

### 3.2 TERMÉKEK KEZELÉSE (Admin) 👑

#### 3.2.1 Összes termék lekérése értékelésekkel (lapozva) 👑
**GET** `/admin/products`

**Jogosultság:** **ADMIN**

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (200):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "name": "Laptop",
            "description": "Gaming laptop",
            "price": "299999.00",
            "created_at": "2025-12-04T10:00:00.000000Z",
            "updated_at": "2025-12-04T10:00:00.000000Z",
            "reviews": [
                {
                    "id": 1,
                    "user_id": 2,
                    "product_id": 1,
                    "rating": 5,
                    "comment": "Nagyon jó termék!",
                    "created_at": "2025-12-04T10:30:00.000000Z",
                    "updated_at": "2025-12-04T10:30:00.000000Z"
                }
            ]
        }
    ],
    "first_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/products?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/products?page=1",
    "next_page_url": null,
    "path": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/products",
    "per_page": 20,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

---

#### 3.2.2 Új termék létrehozása 👑
**POST** `/admin/products`

**Jogosultság:** **ADMIN**

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "name": "Új Termék",
    "description": "Termék leírása",
    "price": 49999
}
```

**Validáció:**
- `name`: kötelező, max 255 karakter
- `description`: opcionális, szöveg
- `price`: kötelező, numerikus érték

**Válasz (201):**
```json
{
    "id": 21,
    "name": "Új Termék",
    "description": "Termék leírása",
    "price": "49999.00",
    "created_at": "2025-12-04T14:00:00.000000Z",
    "updated_at": "2025-12-04T14:00:00.000000Z"
}
```

---

#### 3.2.3 Egy termék lekérése értékelésekkel 👑
**GET** `/admin/products/{id}`

**Jogosultság:** **ADMIN**

**Példa:** `/admin/products/1`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (200):**
```json
{
    "id": 1,
    "name": "Laptop",
    "description": "Gaming laptop",
    "price": "299999.00",
    "created_at": "2025-12-04T10:00:00.000000Z",
    "updated_at": "2025-12-04T10:00:00.000000Z",
    "reviews": [
        {
            "id": 1,
            "user_id": 2,
            "product_id": 1,
            "rating": 5,
            "comment": "Nagyon jó termék!",
            "created_at": "2025-12-04T10:30:00.000000Z",
            "updated_at": "2025-12-04T10:30:00.000000Z"
        }
    ]
}
```

---

#### 3.2.4 Termék frissítése 👑
**PUT** `/admin/products/{id}` vagy **PATCH** `/admin/products/{id}`

**Jogosultság:** **ADMIN**

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "name": "Gaming Laptop Pro",
    "description": "Professzionális gaming laptop",
    "price": 399999
}
```

**Válasz (200):**
```json
{
    "id": 1,
    "name": "Gaming Laptop Pro",
    "description": "Professzionális gaming laptop",
    "price": "399999.00",
    "created_at": "2025-12-04T10:00:00.000000Z",
    "updated_at": "2025-12-04T14:30:00.000000Z"
}
```

---

#### 3.2.5 Termék törlése 👑
**DELETE** `/admin/products/{id}`

**Jogosultság:** **ADMIN**

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (204):** Nincs tartalom

**Megjegyzés:** A termék törlése törli az összes hozzá tartozó értékelést is (cascade delete).

---

### 3.3 ÉRTÉKELÉSEK KEZELÉSE (Admin) 👑

#### 3.3.1 Összes értékelés lekérése (lapozva) 👑
**GET** `/admin/reviews`

**Jogosultság:** **ADMIN**

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (200):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "user_id": 2,
            "product_id": 1,
            "rating": 5,
            "comment": "Nagyon jó termék!",
            "created_at": "2025-12-04T10:30:00.000000Z",
            "updated_at": "2025-12-04T10:30:00.000000Z",
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
    ],
    "first_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/reviews?page=1",
    "from": 1,
    "last_page": 3,
    "last_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/reviews?page=3",
    "next_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/reviews?page=2",
    "path": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/reviews",
    "per_page": 20,
    "prev_page_url": null,
    "to": 20,
    "total": 50
}
```

---

#### 3.3.2 Új értékelés létrehozása 👑
**POST** `/admin/reviews`

**Jogosultság:** **ADMIN**

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "user_id": 2,
    "product_id": 1,
    "rating": 5,
    "comment": "Admin által létrehozott értékelés"
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
    "id": 51,
    "user_id": 2,
    "product_id": 1,
    "rating": 5,
    "comment": "Admin által létrehozott értékelés",
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

---

#### 3.3.3 Egy értékelés lekérése 👑
**GET** `/admin/reviews/{id}`

**Jogosultság:** **ADMIN**

**Példa:** `/admin/reviews/1`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (200):**
```json
{
    "id": 1,
    "user_id": 2,
    "product_id": 1,
    "rating": 5,
    "comment": "Nagyon jó termék!",
    "created_at": "2025-12-04T10:30:00.000000Z",
    "updated_at": "2025-12-04T10:30:00.000000Z",
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

---

#### 3.3.4 Értékelés frissítése 👑
**PUT** `/admin/reviews/{id}` vagy **PATCH** `/admin/reviews/{id}`

**Jogosultság:** **ADMIN**

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "rating": 4,
    "comment": "Moderált értékelés"
}
```

**Megjegyzés:** Az admin bármelyik értékelést módosíthatja. A `user_id` és `product_id` nem módosítható.

**Validáció:**
- `rating`: opcionális, 1-5 közötti egész szám
- `comment`: opcionális, szöveg

**Válasz (200):**
```json
{
    "id": 1,
    "user_id": 2,
    "product_id": 1,
    "rating": 4,
    "comment": "Moderált értékelés",
    "created_at": "2025-12-04T10:30:00.000000Z",
    "updated_at": "2025-12-04T15:30:00.000000Z",
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

---

#### 3.3.5 Értékelés törlése 👑
**DELETE** `/admin/reviews/{id}`

**Jogosultság:** **ADMIN**

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (204):** Nincs tartalom

**Megjegyzés:** Az admin bármelyik értékelést törölheti.

---

## 4. HIBAÜZENETEK

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

### 403 Forbidden (Admin jogosultság hiányzik)
```json
{
    "message": "Unauthorized. Admin access required."
}
```

### 401 Unauthorized (Token hiányzik vagy érvénytelen)
```json
{
    "message": "Unauthenticated."
}
```

---

## 5. TESZTELÉS LÉPÉSEI POSTMAN-BEN

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

## 3. ADMIN VÉGPONTOK

**Megjegyzés:** Az admin végpontok használatához bejelentkezett admin felhasználó szükséges. Az admin jogosultság ellenőrzése az `is_admin` mező alapján történik.

**Admin bejelentkezés:**
- Email: `admin@example.com`
- Jelszó: `admin123`

**Headers minden admin végponthoz:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

---

### 3.1 FELHASZNÁLÓK KEZELÉSE

#### 3.1.1 Összes felhasználó lekérése (lapozva)
**GET** `/admin/users`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (200):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "is_admin": true,
            "email_verified_at": null,
            "created_at": "2025-12-04T10:00:00.000000Z",
            "updated_at": "2025-12-04T10:00:00.000000Z"
        },
        {
            "id": 2,
            "name": "Teszt Felhasználó",
            "email": "teszt@example.com",
            "is_admin": false,
            "email_verified_at": null,
            "created_at": "2025-12-04T10:05:00.000000Z",
            "updated_at": "2025-12-04T10:05:00.000000Z"
        }
    ],
    "first_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/users?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/users?page=1",
    "next_page_url": null,
    "path": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/users",
    "per_page": 20,
    "prev_page_url": null,
    "to": 2,
    "total": 2
}
```

**Hiba (403):**
```json
{
    "message": "Unauthorized. Admin access required."
}
```

---

#### 3.1.2 Új felhasználó létrehozása
**POST** `/admin/users`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "name": "Új Felhasználó",
    "email": "uj@example.com",
    "password": "password123",
    "is_admin": false
}
```

**Validáció:**
- `name`: kötelező, max 255 karakter
- `email`: kötelező, egyedi email cím
- `password`: kötelező, min 8 karakter
- `is_admin`: opcionális, boolean (alapértelmezett: false)

**Válasz (201):**
```json
{
    "id": 13,
    "name": "Új Felhasználó",
    "email": "uj@example.com",
    "is_admin": false,
    "email_verified_at": null,
    "created_at": "2025-12-04T12:00:00.000000Z",
    "updated_at": "2025-12-04T12:00:00.000000Z"
}
```

---

#### 3.1.3 Egy felhasználó lekérése
**GET** `/admin/users/{id}`

**Példa:** `/admin/users/2`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (200):**
```json
{
    "id": 2,
    "name": "Teszt Felhasználó",
    "email": "teszt@example.com",
    "is_admin": false,
    "email_verified_at": null,
    "created_at": "2025-12-04T10:05:00.000000Z",
    "updated_at": "2025-12-04T10:05:00.000000Z"
}
```

---

#### 3.1.4 Felhasználó frissítése
**PUT** `/admin/users/{id}` vagy **PATCH** `/admin/users/{id}`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "name": "Módosított Név",
    "email": "modositott@example.com",
    "password": "ujjelszo123",
    "is_admin": true
}
```

**Megjegyzés:** A `password` mező opcionális. Ha nincs megadva, a jelenlegi jelszó nem változik.

**Validáció:**
- `name`: kötelező, max 255 karakter
- `email`: kötelező, egyedi email cím (az aktuális user email-je kivéve)
- `password`: opcionális, min 8 karakter
- `is_admin`: opcionális, boolean

**Válasz (200):**
```json
{
    "id": 2,
    "name": "Módosított Név",
    "email": "modositott@example.com",
    "is_admin": true,
    "email_verified_at": null,
    "created_at": "2025-12-04T10:05:00.000000Z",
    "updated_at": "2025-12-04T13:00:00.000000Z"
}
```

---

#### 3.1.5 Felhasználó törlése
**DELETE** `/admin/users/{id}`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (204):** Nincs tartalom

**Megjegyzés:** A felhasználó törlése törli az összes hozzá tartozó értékelést is (cascade delete).

---

### 3.2 TERMÉKEK KEZELÉSE (Admin)

#### 3.2.1 Összes termék lekérése értékelésekkel (lapozva)
**GET** `/admin/products`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (200):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "name": "Laptop",
            "description": "Gaming laptop",
            "price": "299999.00",
            "created_at": "2025-12-04T10:00:00.000000Z",
            "updated_at": "2025-12-04T10:00:00.000000Z",
            "reviews": [
                {
                    "id": 1,
                    "user_id": 2,
                    "product_id": 1,
                    "rating": 5,
                    "comment": "Nagyon jó termék!",
                    "created_at": "2025-12-04T10:30:00.000000Z",
                    "updated_at": "2025-12-04T10:30:00.000000Z"
                }
            ]
        }
    ],
    "first_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/products?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/products?page=1",
    "next_page_url": null,
    "path": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/products",
    "per_page": 20,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

---

#### 3.2.2 Új termék létrehozása
**POST** `/admin/products`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "name": "Új Termék",
    "description": "Termék leírása",
    "price": 49999
}
```

**Validáció:**
- `name`: kötelező, max 255 karakter
- `description`: opcionális, szöveg
- `price`: kötelező, numerikus érték

**Válasz (201):**
```json
{
    "id": 21,
    "name": "Új Termék",
    "description": "Termék leírása",
    "price": "49999.00",
    "created_at": "2025-12-04T14:00:00.000000Z",
    "updated_at": "2025-12-04T14:00:00.000000Z"
}
```

---

#### 3.2.3 Egy termék lekérése értékelésekkel
**GET** `/admin/products/{id}`

**Példa:** `/admin/products/1`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (200):**
```json
{
    "id": 1,
    "name": "Laptop",
    "description": "Gaming laptop",
    "price": "299999.00",
    "created_at": "2025-12-04T10:00:00.000000Z",
    "updated_at": "2025-12-04T10:00:00.000000Z",
    "reviews": [
        {
            "id": 1,
            "user_id": 2,
            "product_id": 1,
            "rating": 5,
            "comment": "Nagyon jó termék!",
            "created_at": "2025-12-04T10:30:00.000000Z",
            "updated_at": "2025-12-04T10:30:00.000000Z"
        }
    ]
}
```

---

#### 3.2.4 Termék frissítése
**PUT** `/admin/products/{id}` vagy **PATCH** `/admin/products/{id}`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "name": "Gaming Laptop Pro",
    "description": "Professzionális gaming laptop",
    "price": 399999
}
```

**Válasz (200):**
```json
{
    "id": 1,
    "name": "Gaming Laptop Pro",
    "description": "Professzionális gaming laptop",
    "price": "399999.00",
    "created_at": "2025-12-04T10:00:00.000000Z",
    "updated_at": "2025-12-04T14:30:00.000000Z"
}
```

---

#### 3.2.5 Termék törlése
**DELETE** `/admin/products/{id}`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (204):** Nincs tartalom

**Megjegyzés:** A termék törlése törli az összes hozzá tartozó értékelést is (cascade delete).

---

### 3.3 ÉRTÉKELÉSEK KEZELÉSE (Admin)

#### 3.3.1 Összes értékelés lekérése (lapozva)
**GET** `/admin/reviews`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (200):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "user_id": 2,
            "product_id": 1,
            "rating": 5,
            "comment": "Nagyon jó termék!",
            "created_at": "2025-12-04T10:30:00.000000Z",
            "updated_at": "2025-12-04T10:30:00.000000Z",
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
    ],
    "first_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/reviews?page=1",
    "from": 1,
    "last_page": 3,
    "last_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/reviews?page=3",
    "next_page_url": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/reviews?page=2",
    "path": "http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/reviews",
    "per_page": 20,
    "prev_page_url": null,
    "to": 20,
    "total": 50
}
```

---

#### 3.3.2 Új értékelés létrehozása
**POST** `/admin/reviews`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "user_id": 2,
    "product_id": 1,
    "rating": 5,
    "comment": "Admin által létrehozott értékelés"
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
    "id": 51,
    "user_id": 2,
    "product_id": 1,
    "rating": 5,
    "comment": "Admin által létrehozott értékelés",
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

---

#### 3.3.3 Egy értékelés lekérése
**GET** `/admin/reviews/{id}`

**Példa:** `/admin/reviews/1`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (200):**
```json
{
    "id": 1,
    "user_id": 2,
    "product_id": 1,
    "rating": 5,
    "comment": "Nagyon jó termék!",
    "created_at": "2025-12-04T10:30:00.000000Z",
    "updated_at": "2025-12-04T10:30:00.000000Z",
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

---

#### 3.3.4 Értékelés frissítése
**PUT** `/admin/reviews/{id}` vagy **PATCH** `/admin/reviews/{id}`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {admin_token}
```

**Body (JSON):**
```json
{
    "rating": 4,
    "comment": "Moderált értékelés"
}
```

**Megjegyzés:** Az admin bármelyik értékelést módosíthatja. A `user_id` és `product_id` nem módosítható.

**Validáció:**
- `rating`: opcionális, 1-5 közötti egész szám
- `comment`: opcionális, szöveg

**Válasz (200):**
```json
{
    "id": 1,
    "user_id": 2,
    "product_id": 1,
    "rating": 4,
    "comment": "Moderált értékelés",
    "created_at": "2025-12-04T10:30:00.000000Z",
    "updated_at": "2025-12-04T15:30:00.000000Z",
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

---

#### 3.3.5 Értékelés törlése
**DELETE** `/admin/reviews/{id}`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Válasz (204):** Nincs tartalom

**Megjegyzés:** Az admin bármelyik értékelést törölheti.

---

## 4. HIBAÜZENETEK

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

### 403 Forbidden (Admin jogosultság hiányzik)
```json
{
    "message": "Unauthorized. Admin access required."
}
```

### 401 Unauthorized (Token hiányzik vagy érvénytelen)
```json
{
    "message": "Unauthenticated."
}
```

---

## 5. TESZTELÉS LÉPÉSEI POSTMAN-BEN

### 5.1 Regisztráció
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

### 5.2 Bejelentkezés
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

### 5.3 Admin bejelentkezés
1. Új request: **POST** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/login`
2. Headers: `Content-Type: application/json`
3. Body → raw → JSON:
```json
{
    "email": "admin@example.com",
    "password": "admin123"
}
```
4. Send
5. **Mentsd el a kapott ADMIN token-t külön!**

### 5.4 Termék létrehozása
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

### 5.5 Értékelés létrehozása (Bearer token-nel)
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

### 5.6 Admin végpont tesztelése - Felhasználók listája
1. Új request: **GET** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/users`
2. Headers: 
   - `Authorization: Bearer {admin_token}` (az admin login-nél kapott token)
3. Send
4. **Ellenőrizd:** lapozott válasz érkezik 20 felhasználóval oldalanként

### 5.7 Admin végpont tesztelése - Új felhasználó létrehozása
1. Új request: **POST** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/users`
2. Headers: 
   - `Content-Type: application/json`
   - `Authorization: Bearer {admin_token}`
3. Body → raw → JSON:
```json
{
    "name": "Admin Által Létrehozott User",
    "email": "adminuser@example.com",
    "password": "password123",
    "is_admin": false
}
```
4. Send

### 5.8 Admin végpont tesztelése - Termékek értékelésekkel
1. Új request: **GET** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/products`
2. Headers: 
   - `Authorization: Bearer {admin_token}`
3. Send
4. **Ellenőrizd:** minden termék tartalmazza a hozzá tartozó reviews tömböt

### 5.9 Admin végpont tesztelése - Értékelés módosítása
1. Új request: **PUT** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/reviews/1`
2. Headers: 
   - `Content-Type: application/json`
   - `Authorization: Bearer {admin_token}`
3. Body → raw → JSON:
```json
{
    "rating": 3,
    "comment": "Moderált tartalom"
}
```
4. Send

### 5.10 Jogosultság tesztelése - Normál user próbál admin végpontot elérni
1. Új request: **GET** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/admin/users`
2. Headers: 
   - `Authorization: Bearer {normal_user_token}` (NEM admin token!)
3. Send
4. **Ellenőrizd:** 403 Forbidden hibát kapsz "Unauthorized. Admin access required." üzenettel

### 5.11 Kijelentkezés
1. Új request: **POST** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/logout`
2. Headers: 
   - `Content-Type: application/json`
   - `Authorization: Bearer {token}`
3. Send

### 5.12 Termék értékeléseinek lekérése
1. Új request: **GET** `http://localhost/Termekertekelesek/Termekertekelesek/public/api/products/1/reviews`
2. Send

---

## 6. ÖSSZEFOGLALÓ

### Publikus végpontok (nincs autentikáció szükséges):
- `POST /register` - Regisztráció
- `POST /login` - Bejelentkezés
- `GET /products` - Termékek listázása
- `GET /products/{id}` - Egy termék megtekintése
- `GET /products/{id}/reviews` - Termék értékeléseinek megtekintése
- `GET /reviews` - Értékelések listázása
- `GET /reviews/{id}` - Egy értékelés megtekintése

### Autentikált végpontok (Bearer token szükséges):
- `POST /logout` - Kijelentkezés
- `GET /user` - Saját profil
- `POST /products` - Termék létrehozása
- `PUT/PATCH /products/{id}` - Termék módosítása
- `DELETE /products/{id}` - Termék törlése
- `POST /reviews` - Értékelés létrehozása
- `PUT/PATCH /reviews/{id}` - Értékelés módosítása
- `DELETE /reviews/{id}` - Értékelés törlése

### Admin végpontok (Bearer token + admin jogosultság szükséges):
**Felhasználók:**
- `GET /admin/users` - Összes felhasználó (lapozva)
- `POST /admin/users` - Új felhasználó létrehozása
- `GET /admin/users/{id}` - Egy felhasználó megtekintése
- `PUT/PATCH /admin/users/{id}` - Felhasználó módosítása
- `DELETE /admin/users/{id}` - Felhasználó törlése

**Termékek:**
- `GET /admin/products` - Összes termék értékelésekkel (lapozva)
- `POST /admin/products` - Új termék létrehozása
- `GET /admin/products/{id}` - Egy termék megtekintése értékelésekkel
- `PUT/PATCH /admin/products/{id}` - Termék módosítása
- `DELETE /admin/products/{id}` - Termék törlése

**Értékelések:**
- `GET /admin/reviews` - Összes értékelés (lapozva)
- `POST /admin/reviews` - Új értékelés létrehozása
- `GET /admin/reviews/{id}` - Egy értékelés megtekintése
- `PUT/PATCH /admin/reviews/{id}` - Értékelés módosítása
- `DELETE /admin/reviews/{id}` - Értékelés törlése

### Admin hozzáférés:
- **Email:** admin@example.com
- **Jelszó:** admin123
- **Jogosultság ellenőrzés:** `is_admin` mező (boolean)
- **Middleware:** `auth:sanctum` + `admin`

