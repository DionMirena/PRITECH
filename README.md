<div align="center">

# PRITECH

### Mini Issue Tracker

A small-team issue tracker built with Laravel 13, demonstrating clean Eloquent
relationships, Form Request validation, resource controllers, Blade with
partials, and AJAX interactions for tags, comments, assignees and filtering.

![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-ready-2496ED?logo=docker&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-blue.svg)

[Quick start](#quick-start) ·
[Features](#features) ·
[Architecture](#architecture) ·
[Routes](#routes) ·
[Docker](#option-a--docker-recommended) ·
[Local PHP](#option-b--local-php--laragon)

</div>

---

## Quick start

```bash
git clone https://github.com/DionMirena/PRITECH.git
cd PRITECH
docker compose up -d --build
```

Open **http://localhost:8080**. First boot takes ~30–60 seconds while MySQL
warms up and the seeder loads ~50 demo issues across 7 projects.

---

## Features

| Area | What's included |
|------|-----------------|
| **Projects** | List, create, edit, delete, show with associated issues |
| **Issues** | List with AJAX-debounced search and filtering by status, priority and tag. Full CRUD with Form Request validation |
| **Tags** | Create unique tags with a color; attach/detach to issues over AJAX, no page reload |
| **Comments** | Paginated AJAX load (5/page); AJAX submit prepends the new comment and clears the form; inline 422 errors |
| **Assignees** *(bonus)* | Many-to-many `issue_user` pivot, attached/detached via AJAX |
| **Authorization** *(bonus)* | `ProjectPolicy` lets only a project's owner edit or delete it |
| **Search** *(bonus)* | Debounced text search on title/description with AJAX result swap and URL state |
| **Quick edit** | Status and priority badges on the issue page are inline AJAX dropdowns with optimistic UI |

---

## Architecture

### Domain model

| Entity   | Fields | Relationships |
|----------|--------|---------------|
| `Project` | `name`, `description`, `start_date`, `deadline` | has many `Issue` |
| `Issue`   | `project_id`, `title`, `description`, `status`, `priority`, `due_date` | belongs to `Project`, has many `Comment`, many-to-many `Tag`, many-to-many `User` |
| `Tag`     | `name` *(unique)*, `color` | many-to-many `Issue` |
| `Comment` | `issue_id`, `author_name`, `body` | belongs to `Issue` |

> The `start_date` and `deadline` columns ship in a **dedicated follow-up
> migration** (`add_start_date_and_deadline_to_projects_table`) — exactly as
> the task brief required.

### Tech stack

- **Laravel 13.x** (PHP 8.3) — resource controllers, Form Requests, Eloquent
- **MySQL 8.4** — full FK constraints, cascading deletes on pivots
- **Bootstrap 5.3** via CDN — no Vite build step
- **Vanilla ES** + `fetch()` — no jQuery, no framework dependency
- **Docker** (nginx + PHP-FPM + MySQL) — one-command spin-up

---

## Setup

> Pick **one** option below. Docker is recommended because it requires no
> local PHP or MySQL install.

### Option A — Docker *(recommended)*

#### Prerequisites

- **Docker Desktop** (Windows/macOS) or **Docker Engine + Compose v2** (Linux)

  ```bash
  docker --version
  docker compose version
  ```

- Ports `8080` (web) and `3307` (MySQL) free on your host

#### Step 1 — Clone

```bash
git clone https://github.com/DionMirena/PRITECH.git
cd PRITECH
```

#### Step 2 — Build and start

```bash
docker compose up -d --build
```

#### Step 3 — Open the app

Visit **http://localhost:8080**.

You'll land on `/projects` with the seeded dataset:

- **10 named developers** (Alice Carter, Bob Hernandez, Sarah Chen, …)
- **7 projects** (Customer Portal Redesign, Mobile App v2, Public REST API, …)
- **~50 realistic issues**, **~200 comments**, **10 colored tags**

#### Step 4 — Verify

```bash
docker compose ps
```

Expect three `Up` containers with `pritech-db` reporting `(healthy)`.

<details>
<summary><strong>What's actually happening on first boot?</strong></summary>

1. **Build** the `pritech/app:latest` image from the `Dockerfile`:
   - Stage 1 (`vendor`) — `composer install` produces `vendor/`
   - Stage 2 (`runtime`) — `php:8.3-fpm-alpine` with `pdo_mysql`, `bcmath`,
     `intl`, `opcache`, `zip`, plus a custom `php.ini` and `opcache.ini`
2. **Start** three containers on a private Docker network:

   | Service | Container       | Image                | Host port |
   |---------|-----------------|----------------------|-----------|
   | nginx   | `pritech-web`   | `nginx:1.27-alpine`  | `8080`    |
   | PHP-FPM | `pritech-app`   | `pritech/app:latest` | – (internal `9000`) |
   | MySQL 8 | `pritech-db`    | `mysql:8.4`          | `3307 → 3306` |

3. **Bootstrap** via `docker/entrypoint.sh`:
   - Copies `.env.example` → `.env` if missing
   - Generates `APP_KEY` if not already set
   - Polls MySQL until reachable
   - Runs `php artisan migrate --force`
   - Runs `php artisan db:seed --force` *(controlled by `RUN_SEED=1`)*
   - Caches config / routes / views
   - Launches `php-fpm`

Total first-boot time: ~30–60 seconds. Tail progress with
`docker compose logs -f app`.

</details>

<details>
<summary><strong>Day-to-day commands</strong></summary>

```bash
# Lifecycle
docker compose up -d                  # start (existing images)
docker compose up -d --build          # rebuild app image, then start
docker compose stop                   # stop containers, keep volumes
docker compose down                   # stop and remove containers
docker compose down -v                # ALSO wipe MySQL data
docker compose restart                # restart all
docker compose restart app            # restart just PHP

# Visibility
docker compose ps                     # what's running
docker compose logs -f                # tail all logs
docker compose logs -f app            # tail just PHP

# Artisan inside the container
docker compose exec app php artisan tinker
docker compose exec app php artisan route:list
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app sh            # interactive shell

# Connect to MySQL from your host
mysql -h 127.0.0.1 -P 3307 -u pritech -psecret pritech
```

</details>

<details>
<summary><strong>When do you need to rebuild?</strong></summary>

| Changed file(s) | What to run |
|---|---|
| `public/css/app.css`, `public/js/app.js` | Refresh the browser *(bind-mounted)* |
| `resources/views/**` | Refresh the browser *(live)* |
| `app/**`, `routes/**`, `database/**`, `composer.json` | `docker compose up -d --build` |
| `compose.yaml` | `docker compose up -d` |
| `Dockerfile`, `docker/**` | `docker compose up -d --build` |

</details>

<details>
<summary><strong>Troubleshooting</strong></summary>

- **Port already in use** — change `"8080:80"` or `"3307:3306"` in
  `compose.yaml` to a free port, then `docker compose up -d`.
- **`Connection refused` from app to db** — the entrypoint waits up to 30 s.
  If the DB is slow, `docker compose restart app` after it boots.
- **Reset everything** — `docker compose down -v && docker compose up -d --build`
  destroys the volume and re-seeds from scratch.
- **Permission errors on `storage/`** — the image already chowns it to
  `www-data`. If you added a host bind-mount, remove it from `compose.yaml`.

</details>

### Option B — Local PHP / Laragon

#### Prerequisites

- PHP 8.3+
- Composer
- MySQL (Laragon ships it) or SQLite

#### Steps

```bash
git clone https://github.com/DionMirena/PRITECH.git
cd PRITECH
composer install
cp .env.example .env
php artisan key:generate

# MySQL — create the database first
mysql -uroot -e "CREATE DATABASE pritech CHARACTER SET utf8mb4;"
# or switch DB_CONNECTION=sqlite in .env

php artisan migrate --seed
php artisan serve
```

Visit **http://127.0.0.1:8000**.

---

## Routes

| Method   | URI                                   | Purpose |
|----------|---------------------------------------|---------|
| `GET`    | `/projects`                           | List projects |
| `GET`    | `/projects/{project}`                 | Project detail with its issues |
| `GET`    | `/issues?status=&priority=&tag=&q=`   | Issue index (HTML, or JSON for AJAX) |
| `GET`    | `/issues/{issue}`                     | Issue detail (tags, assignees, comments) |
| `POST`   | `/issues/{issue}/tags`                | AJAX attach tag |
| `DELETE` | `/issues/{issue}/tags/{tag}`          | AJAX detach tag |
| `GET`    | `/issues/{issue}/comments?page=N`     | AJAX paginated comments |
| `POST`   | `/issues/{issue}/comments`            | AJAX add comment (validated) |
| `POST`   | `/issues/{issue}/assignees`           | AJAX assign user |
| `DELETE` | `/issues/{issue}/assignees/{user}`    | AJAX unassign |
| `PATCH`  | `/issues/{issue}/status`              | AJAX inline status/priority update |
| `GET`    | `/tags`                               | List + create tags |

Full list at runtime:
```bash
docker compose exec app php artisan route:list
```

---

## Project structure

```text
app/
├── Http/
│   ├── Controllers/    ProjectController, IssueController, TagController,
│   │                   CommentController, IssueTagController, IssueAssigneeController
│   └── Requests/       Store/Update Form Requests for every resource
├── Models/             Project, Issue, Tag, Comment, User
├── Policies/           ProjectPolicy
└── Providers/          AppServiceProvider (Paginator::useBootstrapFive)

database/
├── migrations/         One migration per table + a dedicated one for start_date/deadline
├── factories/          Project/Issue/Tag/Comment factories with curated English content
└── seeders/            DatabaseSeeder builds the demo dataset

resources/views/
├── layouts/app.blade.php
├── partials/           nav, errors
├── projects/           index, create, edit, show, _form
├── issues/             index, create, edit, show, _form, _table, _comment
└── tags/               index

public/
├── css/app.css         Design system on top of Bootstrap
└── js/app.js           AJAX modules (tags, assignees, comments, filters, quick-edit)

docker/
├── entrypoint.sh
├── nginx/default.conf
└── php/                php.ini, opcache.ini

routes/web.php          Route::resource + nested AJAX routes
```

---

## Notes for reviewers

- **No N+1.** Every list query eager-loads what the view will render
  (`with('owner:id,name')`, `with(['project:id,name', 'tags:id,name,color'])`, etc.).
- **Form Requests everywhere.** All write endpoints validate via dedicated
  Form Request classes. AJAX endpoints get **422 JSON** with field-keyed
  errors; full-page submits get **flashed errors** displayed inline.
- **One endpoint, two delivery modes.** `IssueController@index` returns the
  full Blade page for regular requests and rendered partial + pagination HTML
  for `XMLHttpRequest` calls — same template, no duplicated logic.
- **CSRF everywhere.** Every AJAX POST/PATCH/DELETE reads the CSRF token
  from the `<meta name="csrf-token">` tag and forwards it as `X-CSRF-TOKEN`.
- **Constants over magic strings.** `Issue::STATUSES` / `PRIORITIES` are the
  single source of truth for the migration's `enum()`, the FormRequest's
  `Rule::in(...)`, and the Blade dropdowns.
- **Optimistic UI with rollback** on the inline status/priority dropdowns —
  the badge updates immediately on click and rolls back if the server rejects.

---

## License

[MIT](LICENSE) — feel free to use this as a reference for your own Laravel
work.
