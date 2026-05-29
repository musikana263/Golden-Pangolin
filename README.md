# Golden Pangolin

[![Tests](https://github.com/muchaisam/Tours-Travel/actions/workflows/tests.yml/badge.svg)](https://github.com/muchaisam/Tours-Travel/actions/workflows/tests.yml)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A travel booking site for [goldenpangolin.com](https://goldenpangolin.com) — curated destinations, tour packages, accommodations, and a blog covering Zimbabwe, Botswana, and Zambia. Built on **Laravel 11**, **Bootstrap 5**, and a small **REST API** for public consumers.

## Tech stack

| Layer | Technology |
|-------|------------|
| Framework | Laravel 11 (PHP 8.2+) |
| Frontend | Blade templates, Bootstrap 5.3, custom CSS design system with dark mode default |
| Database | MySQL 8, Eloquent ORM with soft deletes |
| Email | Postmark (production), `log` driver (local) |
| API | Versioned read-only REST (`/api/v1`), JSON Resources + Collections |
| Auth | Laravel UI (login only — no public registration), email verification |
| Testing | PHPUnit 11, SQLite `:memory:` |
| CI | GitHub Actions (test + lint + `composer audit`) |
| Hosting | DirectAdmin (see [docs/DEPLOY_DIRECTADMIN.md](docs/DEPLOY_DIRECTADMIN.md)) |

## Permission model

There are exactly two kinds of authenticated users:

| Capability | Any authenticated user | Admin |
|---|---|---|
| Read public pages, API, blog | ✅ | ✅ |
| Submit reviews | ✅ | ✅ |
| Create / edit / delete destinations, blog posts, categories, activities, accommodations, tour packages, hero package | ✅ | ✅ |
| Trash / restore destinations | ✅ | ✅ |
| Edit own profile | ✅ | ✅ |
| **Add a new user, list all users, promote a user to admin** | ❌ | ✅ |

That last row is the only thing admins can do that regular users cannot. Public registration is disabled — every account is created by an admin via `/users/create`.

## Module rundown

See [docs/MODULES.md](docs/MODULES.md) for a one-page tour of every module in the site (public pages, content management, user management, API, services).

## Setup

### Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js & npm
- MySQL 8

### Local install

```bash
git clone https://github.com/muchaisam/Tours-Travel.git golden-pangolin
cd golden-pangolin

composer install && npm install
cp .env.example .env
php artisan key:generate
```

### Database config

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=golden_pangolin
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Migrate & seed

```bash
php artisan migrate --seed
php artisan storage:link
```

This creates a local admin account (only in non-production environments):

| Field | Value |
|-------|-------|
| Email | `samadmin@gmail.com` |
| Password | `password` |

In **production**, the seeder refuses to run unless `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD` env vars are set, so a guessable admin can never be shipped to the public internet.

### Run the app

```bash
php artisan serve   # http://localhost:8000
npm run dev         # build frontend assets
```

### Docker (optional)

```bash
docker-compose up -d
docker-compose exec app php artisan migrate --seed
# http://localhost:8080
```

## Production

Hosting target is **DirectAdmin** at `goldenpangolin.com`. See:

- [docs/DEPLOY_DIRECTADMIN.md](docs/DEPLOY_DIRECTADMIN.md) — step-by-step deploy
- [docs/EMAIL_SETUP.md](docs/EMAIL_SETUP.md) — Postmark + SPF/DKIM/DMARC
- [docs/SECURITY_CHECKLIST.md](docs/SECURITY_CHECKLIST.md) — security baseline in code + manual steps
- [docs/PENTEST_REPORT.md](docs/PENTEST_REPORT.md) — static pentest findings

## Testing

```bash
./vendor/bin/phpunit              # all tests
./vendor/bin/phpunit --testdox    # verbose
./vendor/bin/pint --test          # code style check
composer audit                    # CVE scan
```

## API

Public read-only endpoints, all under `/api/v1`:

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/destinations` | Paginated list (filter: `?category=`, `?search=`, `?sort_by=`, `?per_page=`) |
| `GET` | `/destinations/{id}` | One destination with category, tags, reviews |
| `GET` | `/destinations/featured` | Featured destinations (`?limit=` capped at 50) |
| `GET` | `/destinations/search?q=` | Keyword search across title/description/content |
| `GET` | `/categories` | All categories |
| `GET` | `/categories/{id}` | One category |

Sortable columns are whitelisted (`published_at`, `title`, `created_at`, `id`) and `per_page` is capped at 50 to keep the DB safe from abuse.

There is no authenticated API surface — the legacy `auth:api` (Laravel 5 `token` driver) was removed. Install `laravel/sanctum` if you need authenticated API access later.

## License

[MIT](LICENSE)
