# BookFlow

> Effortless bookings for service businesses — a Calendly alternative built with Laravel 13, Inertia.js and Vue 3.

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php)](https://www.php.net/)
[![Vue 3](https://img.shields.io/badge/Vue-3-4FC08D?logo=vue.js)](https://vuejs.org/)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?logo=typescript)](https://www.typescriptlang.org/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-18-4169E1?logo=postgresql)](https://www.postgresql.org/)
[![Pest](https://img.shields.io/badge/Tests-Pest_4-FF6E6E)](https://pestphp.com/)

## What it does

BookFlow lets a service provider publish bookable services (consultations, classes,
sessions), set recurring weekly availability, and accept paid bookings via Stripe
Checkout. Customers can book without creating an account — they get a unique cancel
link by email.

## Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 13 (PHP 8.3+) |
| **Frontend** | Inertia.js 2 + Vue 3 + TypeScript + Tailwind CSS |
| **Database** | PostgreSQL 18 |
| **Cache / Queue** | Redis |
| **Auth** | Laravel Breeze (sessions, email verification, password reset) |
| **Payments** | Stripe Checkout (PaymentIntent + webhooks) |
| **Mail (dev)** | Mailpit |
| **Mail (prod)** | Resend |
| **Testing** | Pest 4 + pest-plugin-laravel |
| **Dev env** | Laravel Sail (Docker Compose) |
| **Deploy target** | Railway (Laravel app + Postgres managed) |

## Quick start

Requires [Docker Desktop](https://www.docker.com/products/docker-desktop/) and
PHP/Composer to bootstrap.

```bash
# 1. Clone
git clone git@github.com:Alejandro-Montepeque/bookflow.git
cd bookflow

# 2. Install PHP deps (needs PHP 8.3+ locally to install Sail)
composer install

# 3. Copy env template and generate app key
cp .env.example .env
php artisan key:generate

# 4. Bring the stack up (Postgres, Redis, Mailpit, Laravel)
./vendor/bin/sail up -d

# 5. Install JS deps
./vendor/bin/sail npm install

# 6. Run migrations and seed demo data
./vendor/bin/sail artisan migrate:fresh --seed

# 7. Start Vite for HMR (in a second terminal)
./vendor/bin/sail npm run dev
```

Then open:

- **App** → http://localhost:8080
- **Mailpit** (outgoing emails) → http://localhost:8025

The seeder creates a demo provider you can log in with right away:

| Email | Password |
|---|---|
| `demo@bookflow.app` | `password` |

## Running tests

```bash
./vendor/bin/sail artisan test
```

The suite covers Breeze auth flows plus model relationships, scopes and business
rules across `Service`, `AvailabilityRule`, `Booking` and `Payment`.

## Project structure

```
app/
├── Http/Controllers/       Inertia controllers
├── Models/                 Eloquent models (Service, Booking, Payment, AvailabilityRule)
└── ...
database/
├── migrations/             Schema (services, availability_rules, bookings, payments)
├── factories/              Faker definitions for tests/seeds
└── seeders/                DatabaseSeeder is idempotent (safe to re-run)
resources/
├── js/
│   ├── Pages/              Inertia pages (1 .vue per route)
│   ├── Components/         Reusable Vue components
│   ├── Layouts/            AuthenticatedLayout, GuestLayout
│   ├── types/              TypeScript domain types
│   └── utils/              Format helpers (price, date, status)
└── css/app.css             Tailwind entrypoint
routes/web.php              All routes — Inertia handles the rest
tests/Feature/Models/       Pest tests for the domain
compose.yaml                Docker Compose (Sail) services
```

## Roadmap

- [x] Authentication scaffold (Breeze, Vue + TS + Pest)
- [x] Domain models (Service, AvailabilityRule, Booking, Payment)
- [x] Docker dev environment (Sail with Postgres, Redis, Mailpit)
- [ ] Provider dashboard with stats
- [ ] CRUD for services + availability editor
- [ ] Public booking page with slot picker
- [ ] Stripe Checkout integration + webhook
- [ ] Booking confirmation / cancellation emails
- [ ] Deploy to Railway
- [ ] CI on GitHub Actions

## License

MIT
