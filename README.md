# TaskTracker

Aplikasi manajemen proyek dan task berbasis web untuk Technical Test Energeek 2026.

## Stack
- **Backend**: Laravel 12 + Sanctum 4.x + PostgreSQL 14
- **Frontend**: Vue 3 + TypeScript + Pinia + Vite

## Struktur
```
backend/   → Laravel 12 API (port 8000)
frontend/  → Vue 3 SPA (port 5173)
```

## Setup

### Prerequisites
- PHP 8.4+, Composer
- Node.js 20+, npm
- PostgreSQL 14+

### Backend
```bash
cd backend
composer install
cp .env.example .env
# Edit .env: isi DB_PASSWORD
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### Frontend
```bash
cd frontend
npm install
npm run dev
```

## Akun Default
- Email: admin@energeek.id
- Password: password123
