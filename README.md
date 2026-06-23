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

### Option A — Docker (recommended, zero local dependencies)

Requires Docker Desktop / Docker Engine + Compose v2.

```bash
git clone <repo>
cd PRITECH
docker compose up -d --build
```

Then visit `http://127.0.0.1:8080`.

The first boot waits for MySQL, runs `php artisan migrate --force` and
`db:seed --force`, then caches config/routes/views. Subsequent restarts
re-run migrations (idempotent) but skip seeding (set `RUN_SEED=1` in
`compose.yaml` if you want a fresh demo dataset).

Services:

| Service | Container       | Host port |
|---------|-----------------|-----------|
| nginx   | `pritech-web`   | `8080`    |
| PHP-FPM | `pritech-app`   | -         |
| MySQL 8 | `pritech-db`    | `3307` (mapped from container's 3306) |

Useful commands:
```bash
docker compose logs -f app           # tail PHP logs
docker compose exec app php artisan tinker
docker compose exec app php artisan migrate:fresh --seed
docker compose down -v               # wipe DB volume and start over
```

### Option B — Run locally with Laragon / native PHP

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
