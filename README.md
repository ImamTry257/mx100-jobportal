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
```

---

### ▶️ Menjalankan Server

```bash
php artisan serve
```

---

### Authentication

Gunakan Laravel Sanctum:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

php artisan install:api
```

### Storage for Uploading File

Gunakan command berikut:

```bash
php artisan storage:link
```

---

### Endpoint Utama

#### Auth

* POST /api/auth/register
* POST /api/auth/login

#### Vacancy

* GET /api/vacancies
* POST /api/vacancies
* GET /api/vacancies/{id}
* PUT /api/vacancies/{id}
* GET /api/vacancies/{id}/applicants

#### Applications

* POST /api/vacancies/{id}/apply
* POST /api/vacancies/my-applications


---
