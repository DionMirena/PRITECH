# PRITECH – Mini Issue Tracker

A small team issue tracker built with **Laravel 13**, demonstrating clean Eloquent
relationships, Form Request validation, resource controllers, Blade with
partials, and AJAX-driven interactions for tags, comments, assignees and
filtering.

This repository was produced as a technical task: see
[`docs/TASK.md`](docs/TASK.md) for the original brief.

---

## Domain

| Entity   | Notes |
|----------|-------|
| Project  | `name`, `description`, plus `start_date` and `deadline` added in a follow-up migration. Has many Issues. |
| Issue    | `title`, `description`, `status` (`open / in_progress / closed`), `priority` (`low / medium / high`), `due_date`. Belongs to Project, has many Comments, many-to-many with Tags and (bonus) Users. |
| Tag      | `name` (unique) + optional `color`. Many-to-many with Issues. |
| Comment  | `author_name`, `body`. Belongs to Issue. |

A separate migration `add_start_date_and_deadline_to_projects_table` adds the
two extra columns to the `projects` table — exactly as required by the task.

## Features

- **Projects** — list, create, edit, delete, show with associated issues.
- **Issues** — list with **AJAX-debounced** search and filtering by status, priority and tag. Full CRUD with Form Request validation.
- **Tags** — create unique tags with a color; attach/detach to issues over **AJAX**, with no page reload.
- **Comments** — paginated AJAX load (5 per page) on the issue page, AJAX submit that prepends the new comment and clears the form. Server-side validation errors are surfaced inline on the page.
- **Assignees (bonus)** — many-to-many `issue_user` pivot, attached/detached via AJAX on the issue page.
- **Authorization (bonus)** — `ProjectPolicy` lets only a project's owner edit or delete it.
- **Search (bonus)** — debounced text search on issue title/description with AJAX result swap and URL state.

## Tech

- Laravel 13.x (PHP 8.3)
- MySQL (configured for Laragon by default; SQLite works just as well)
- Bootstrap 5 + Bootstrap Icons via CDN — no JS build step needed
- Vanilla ES, no jQuery dependency

## Setup

You can run PRITECH two ways. Pick whichever you have on your machine.

---

### Option A — Docker (recommended, zero local dependencies)

#### Prerequisites

- **Docker Desktop** (Windows/macOS) or **Docker Engine + Compose v2** (Linux).
  Verify with:
  ```bash
  docker --version
  docker compose version
  ```
- Ports **8080** (web) and **3307** (MySQL) free on your host.

#### Step 1 — Clone the repository

```bash
git clone <repo>
cd PRITECH
```

#### Step 2 — Build and start the stack

```bash
docker compose up -d --build
```

What this does:

1. Builds the `pritech/app:latest` image from the `Dockerfile`:
   - **Stage 1** (`vendor`) runs `composer install` to produce `vendor/`.
   - **Stage 2** (`runtime`) is `php:8.3-fpm-alpine` with the required PHP
     extensions (`pdo_mysql`, `bcmath`, `intl`, `opcache`, `zip`) plus a
     custom `php.ini` and `opcache.ini`.
2. Starts three containers wired together on a private Docker network:

   | Service | Container       | Image                | Host port |
   |---------|-----------------|----------------------|-----------|
   | nginx   | `pritech-web`   | `nginx:1.27-alpine`  | `8080`    |
   | PHP-FPM | `pritech-app`   | `pritech/app:latest` | – (internal `9000`) |
   | MySQL 8 | `pritech-db`    | `mysql:8.4`          | `3307` → container `3306` |

3. On first boot, `docker/entrypoint.sh` inside the app container:
   - copies `.env.example` to `.env` if missing
   - generates an `APP_KEY` if one isn't already set
   - polls MySQL until it answers on port `3306`
   - runs `php artisan migrate --force`
   - runs `php artisan db:seed --force` (because `RUN_SEED=1` in
     `compose.yaml`)
   - caches config / routes / views
   - launches `php-fpm`

Total first-boot time: ~30–60 seconds while the DB warms up and the seeder
runs. Watch progress with `docker compose logs -f app`.

#### Step 3 — Open the app

Visit **http://localhost:8080** in your browser.

You should land on the projects index with the full seeded demo dataset:
- **10 named developers** (Alice Carter, Bob Hernandez, Sarah Chen, …)
- **7 projects** (Customer Portal Redesign, Mobile App v2, Public REST API, …)
- **~50 realistic issues** + **~200 comments** + **10 colored tags**

#### Step 4 — Verify everything is healthy

```bash
docker compose ps
```

You should see three containers, all `Up`, with `pritech-db` reporting
`(healthy)`.

---

### Docker — day-to-day commands

```bash
# --- lifecycle ---
docker compose up -d                 # start (uses existing images)
docker compose up -d --build         # rebuild app image, then start
docker compose stop                  # stop containers, keep volumes
docker compose down                  # stop and remove containers
docker compose down -v               # stop, remove containers AND wipe MySQL data
docker compose restart               # restart all
docker compose restart app           # restart just PHP

# --- visibility ---
docker compose ps                    # what's running
docker compose logs -f               # tail all logs
docker compose logs -f app           # tail just PHP
docker compose logs -f web           # tail just nginx
docker compose logs -f db            # tail just MySQL

# --- run artisan inside the container ---
docker compose exec app php artisan tinker
docker compose exec app php artisan route:list
docker compose exec app php artisan migrate:fresh --seed   # reset to fresh demo data
docker compose exec app php artisan config:clear
docker compose exec app sh                                  # interactive shell

# --- talk to MySQL from your host ---
mysql -h 127.0.0.1 -P 3307 -u pritech -psecret pritech
```

### When do you need to rebuild?

| Changed file(s)                            | What to run                          |
|--------------------------------------------|--------------------------------------|
| `public/css/app.css`, `public/js/app.js`   | Just refresh the browser (bind-mounted) |
| `resources/views/**`                       | Live — refresh the browser           |
| `app/**`, `routes/**`, `database/**`, `composer.json` | `docker compose up -d --build`       |
| `compose.yaml`                             | `docker compose up -d`               |
| `Dockerfile`, `docker/**`                  | `docker compose up -d --build`       |

### Troubleshooting

- **Port already in use** — change `"8080:80"` (or `"3307:3306"`) in
  `compose.yaml` to a free port and `docker compose up -d` again.
- **`Connection refused` from app to db** — the entrypoint waits up to 30s.
  If the DB is slow on your machine, run `docker compose restart app` once
  the DB is up.
- **Want to reset everything** — `docker compose down -v && docker compose up -d --build`
  destroys the MySQL volume and re-seeds from scratch.
- **Permission errors on `storage/`** — the image already chowns it to
  `www-data`. If you mounted the host directory by mistake, remove that
  mount from `compose.yaml`.

---

### Option B — Run locally with Laragon / native PHP

#### Prerequisites
- PHP 8.3+
- Composer
- MySQL (Laragon ships it) or SQLite

#### Steps

```bash
git clone <repo>
cd PRITECH
composer install
cp .env.example .env
php artisan key:generate

# MySQL: create the database first, e.g.
mysql -uroot -e "CREATE DATABASE pritech CHARACTER SET utf8mb4;"
# or swap DB_CONNECTION=sqlite in .env

php artisan migrate --seed
php artisan serve
```

Then visit `http://127.0.0.1:8000`.

### Seeded data

The seeder creates:

- 5 users (including `alice@pritech.test` and `bob@pritech.test`)
- 5 projects with random owners
- ~30 issues distributed across them
- 7 colored tags
- A few comments per issue, with random assignees

## Routes overview

| Method | URI | Purpose |
|--------|-----|---------|
| `GET`  | `/projects` | List projects |
| `GET`  | `/projects/{project}` | Project detail w/ its issues |
| `GET`  | `/issues?status=&priority=&tag=&q=` | Issue index (HTML or JSON for AJAX) |
| `GET`  | `/issues/{issue}` | Issue detail (tags, assignees, comments) |
| `POST` | `/issues/{issue}/tags` | AJAX attach tag |
| `DELETE` | `/issues/{issue}/tags/{tag}` | AJAX detach tag |
| `GET`  | `/issues/{issue}/comments?page=N` | AJAX paginated comments |
| `POST` | `/issues/{issue}/comments` | AJAX add comment (validated) |
| `POST` | `/issues/{issue}/assignees` | AJAX assign user |
| `DELETE` | `/issues/{issue}/assignees/{user}` | AJAX unassign |
| `GET`  | `/tags` | List + create tags |

## Project structure

```
app/
  Http/
    Controllers/   ProjectController, IssueController, TagController,
                   CommentController, IssueTagController, IssueAssigneeController
    Requests/      Store/Update FormRequests for every resource
  Models/          Project, Issue, Tag, Comment, User
  Policies/        ProjectPolicy
database/
  migrations/      One migration per table + a dedicated one for start_date/deadline
  factories/       Project/Issue/Tag/Comment factories
  seeders/         DatabaseSeeder builds a realistic demo dataset
resources/
  views/
    layouts/app.blade.php
    partials/
    projects/  (index, create, edit, show, _form)
    issues/    (index, create, edit, show, _form, _table, _comment)
    tags/      (index)
public/
  css/app.css    – design system
  js/app.js      – AJAX modules (tags, assignees, comments, filters)
routes/web.php
```

## Notes for reviewers

- Eager loading is used everywhere a list renders (`with('owner')`, `with('project', 'tags')`, etc.) to keep query counts flat — no N+1.
- All write endpoints use Form Request classes; client-side errors come back as `422` JSON for AJAX and as flashed errors for full-page submits.
- Issue filters share one endpoint (`IssueController@index`) — when called with `XMLHttpRequest`, it returns rendered partials + pagination HTML; otherwise it returns the full page. This keeps the view template DRY.
- All AJAX POST/DELETE requests use the CSRF token from the `<meta name="csrf-token">` tag.
#   P R I T E C H  
 #   P R I T E C H  
 