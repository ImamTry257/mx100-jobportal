# 📘 MX100 Job Portal API

## 1. README

### 📌 Deskripsi

MX100 adalah platform job portal yang menghubungkan perusahaan dengan freelancer.

---

### ⚙️ Cara Install

```bash
composer install
cp .env.example .env
php artisan key:generate
```

---

### 🗄️ Setup Database

Edit file `.env`:

```
DB_DATABASE=mx100
DB_USERNAME=root
DB_PASSWORD=
```

Lalu jalankan:

```bash
php artisan migrate
php artisan db:seed
```

---

### ▶️ Menjalankan Server

```bash
php artisan serve
```

---

### 🔐 Authentication

Gunakan Laravel Sanctum:

```bash
php artisan install:sanctum
php artisan migrate
```

---

### 📡 Endpoint Utama

#### Auth

* POST /api/auth/register
* POST /api/auth/login

#### Vacancy

* GET /api/vacancy
* POST /api/vacancy
* GET /api/vacancy/{id}
* PUT /api/vacancy/{id}

#### Vacancy Apply

* POST /api/vacancy-apply/{id}

---
