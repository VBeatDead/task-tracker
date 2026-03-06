# TaskTracker

Aplikasi manajemen proyek dan task berbasis web — Technical Test Junior Fullstack Web Developer Energeek 2026.

## Tech Stack

| Layer | Teknologi |
|:------|:----------|
| Backend | Laravel 12.x + Laravel Sanctum 4.x (Personal Access Token) |
| Database | PostgreSQL 14+ |
| Frontend | Vue 3 + TypeScript + Pinia + Vite |
| Testing | PHPUnit 11 (backend) · Vitest 4 (frontend) |

## Fitur Utama

- Autentikasi berbasis token (Sanctum PAT)
- Manajemen project: buat, edit, filter status, detail
- Kanban board 5 kolom dengan drag & drop
- Manajemen task: CRUD, soft delete, filter multi-kriteria
- Dashboard: statistik aktif + task mendekati due date
- Validasi per-field (422) dan penanganan error global

## Prasyarat

- PHP >= 8.2 + Composer
- Node.js >= 18 + npm >= 9
- PostgreSQL 14+

## Instalasi

### 1. Clone & masuk ke folder

```bash
git clone <repo-url> task-tracker
cd task-tracker
```

### 2. Backend

```bash
cd backend
composer install
cp .env.example .env
```

Edit `.env` — sesuaikan bagian database:

```env
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tasktracker_db
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

Buat database di PostgreSQL terlebih dahulu:

```bash
createdb -U postgres tasktracker_db
# atau via psql:
# psql -U postgres -c "CREATE DATABASE tasktracker_db;"
```

```bash
php artisan key:generate
php artisan migrate:fresh --seed
```

### 3. Frontend

```bash
cd ../frontend
npm install
cp .env.example .env
```

Pastikan isi `.env`:

```env
VITE_API_BASE_URL=http://localhost:8000
```

## Menjalankan Aplikasi

Buka dua terminal:

```bash
# Terminal 1 — Backend (port 8000)
cd backend && php artisan serve

# Terminal 2 — Frontend (port 5173)
cd frontend && npm run dev
```

Akses di: **http://localhost:5173**

**Akun default:**
- Email: `admin@energeek.id`
- Password: `password123`

## Menjalankan via Docker (Alternatif)

Jika Docker dan Docker Compose sudah terinstal:

```bash
docker compose up --build
```

Akses di: **http://localhost:5173** (frontend) · **http://localhost:8000** (backend API)

> Database, migrasi, dan seeder dijalankan otomatis saat container backend pertama kali start.

## Menjalankan Tests

```bash
# Backend — PHPUnit
cd backend && php artisan test
# Expected: 23 tests, 62 assertions

# Frontend — Vitest
cd frontend && npm run test
# Expected: 18 tests, 4 test files

# TypeScript check
cd frontend && npm run type-check
```

## Production Build

```bash
cd frontend && npm run build
# Output di frontend/dist/ — siap dideploy ke static hosting
```

## Struktur Proyek

```
task-tracker/
├── backend/                  # Laravel 12 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/  # AuthController, ProjectController, TaskController, ...
│   │   │   └── Requests/     # Form validation (per-field, Bahasa Indonesia)
│   │   └── Models/           # User, Project, Task, Category
│   ├── database/
│   │   ├── migrations/       # Skema DB lengkap
│   │   └── seeders/          # Data awal: admin, 3 project, 8+ task
│   ├── routes/api.php        # Semua endpoint REST API
│   └── tests/Feature/        # PHPUnit Feature tests
└── frontend/                 # Vue 3 SPA
    ├── src/
    │   ├── components/       # AppLayout, AppModal, Badge, TaskCard, Modals
    │   ├── views/            # Login, Dashboard, Projects, ProjectDetail, Tasks
    │   ├── stores/           # Pinia auth store
    │   ├── services/         # Axios API services
    │   ├── types/            # TypeScript interfaces
    │   ├── constants/        # Shared constants (CATEGORIES)
    │   └── plugins/          # Toast helper, Date helper
    └── tests/                # Vitest component & helper tests
```

## API Endpoints

| Method | Endpoint | Deskripsi | Auth |
|:-------|:---------|:----------|:-----|
| POST | `/api/auth/login` | Login, dapat token | — |
| DELETE | `/api/auth/logout` | Logout, revoke token | ✓ |
| GET | `/api/dashboard` | Statistik + upcoming tasks | ✓ |
| GET | `/api/projects` | List project (`?search=` `?status=`) | ✓ |
| POST | `/api/projects` | Buat project | ✓ |
| GET | `/api/projects/{id}` | Detail project + tasks | ✓ |
| PUT | `/api/projects/{id}` | Update project | ✓ |
| GET | `/api/tasks` | List task (`?search=` `?category_id=` `?project_id=`) | ✓ |
| POST | `/api/tasks` | Buat task | ✓ |
| PUT | `/api/tasks/{id}` | Update task | ✓ |
| PATCH | `/api/tasks/{id}/category` | Pindah kolom kanban | ✓ |
| DELETE | `/api/tasks/{id}` | Soft delete task | ✓ |
| GET | `/api/categories` | List kategori (read-only) | ✓ |

Semua response format: `{ "data": ..., "message": "..." }`

Lihat detail request/response di: [`docs/tasktracker.postman_collection.json`](docs/tasktracker.postman_collection.json)

## Catatan Teknis

- **Soft delete** diimplementasi manual (`deleted_at` + `deleted_by`) tanpa Laravel SoftDeletes trait, agar kedua kolom terisi bersamaan
- **Drag & drop** menggunakan vuedraggable@next dengan optimistic update — jika API gagal, task otomatis kembali ke posisi semula
- **Due date** pada create task divalidasi `after_or_equal:today`; pada edit boleh lampau (task overdue tetap bisa diedit)
- **Token expired (401)** ditangani global di Axios interceptor → redirect `/login`
