<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Issue>
 */
class IssueFactory extends Factory
{
    private const TITLES = [
        'Login button does not respond on Safari',
        'Password reset email goes to spam',
        'Pagination breaks on the dashboard',
        'Slow database query on the orders endpoint',
        'Add OAuth2 authentication flow',
        'Refactor user controller into smaller services',
        'Switch caching layer from file to Redis',
        'Implement dark mode for the settings page',
        'Form validation messages are missing translations',
        'Add unit tests for invoice calculations',
        'Webhook signature verification fails intermittently',
        'Migration `add_tax_rate_to_invoices` fails on MySQL 5.7',
        'Empty state on the projects page is unstyled',
        'API returns 500 when filtering by an empty tag',
        'Add deploy script for the staging environment',
        'Audit log misses POST /api/v1/users events',
        'CSV export of issues truncates UTF-8 names',
        'Avatar upload silently fails on files > 2MB',
        'Update README with local Docker setup',
        'Notifications dropdown leaks memory on long sessions',
        'Add rate-limiting middleware to the public API',
        'Move secrets out of `.env.example`',
        'Compose file uses deprecated `version` key',
        'Improve test coverage for the search service',
        'Sidebar collapse animation stutters on Firefox',
        'Calendar widget fails to render on the 31st',
        'Sortable columns lose order after page refresh',
        'Replace `dd()` calls left in the codebase',
        'Add structured logging to the queue workers',
        'Document the new feature flag system',
    ];

    private const DESCRIPTIONS = [
        "Steps to reproduce:\n1. Open the login page on Safari 17.\n2. Enter valid credentials and press the login button.\n3. Nothing happens — no network request is fired.\n\nExpected: a successful login redirect to /dashboard.",
        "Customers report that the reset email lands in their spam folder. We should review SPF/DKIM/DMARC records and consider switching transactional sender.",
        "When the dashboard table reaches the second page, the layout overflows on mobile and the pagination buttons disappear behind the floating action bar.",
        "EXPLAIN shows a full table scan on `orders` when filtering by `status` and `created_at`. Add a composite index and verify the query plan again.",
        "Implement an OAuth2 flow alongside the current session auth: authorization code with PKCE, refresh tokens, and proper scope validation.",
        "`UserController` has grown past 600 lines. Split into thin actions or dedicated service classes, and add feature tests for each public method.",
        "We're hitting filesystem cache contention in production. Move sessions and cache to Redis and document the new connection in the README.",
        "Add a `prefers-color-scheme: dark` stylesheet for the settings panel. Honour the toggle stored in user preferences when set.",
        "All Blade error bags use English strings. Move them through `__()` and seed `lang/sq.json` for the Albanian locale.",
        "The invoice subtotal/VAT/total breakdown has no automated tests. Add cases for zero-rate, multiple line items, and rounding edge cases.",
        "Roughly 1 in 50 webhook requests are rejected with a signature mismatch. Investigate clock skew and the raw-body capture middleware.",
        "On MySQL 5.7, `ALTER TABLE invoices ADD COLUMN tax_rate ...` fails because the table contains JSON columns. Add a guard or split the migration.",
        "Currently shows the raw `<empty />` placeholder. Replace with the standard empty-state component we use across the app.",
        "Hitting `/api/issues?tag=` (empty string) raises a TypeError in `IssueController::index`. Coerce empty filter values to null in the request layer.",
        "Add a small `bin/deploy-staging.sh` that runs `git pull`, `composer install --no-dev`, `php artisan migrate --force` and clears all caches.",
        "Some endpoints under `/api/v1/users` don't write to the audit log. Move audit logging into a single middleware instead of per-controller calls.",
        "Names with diacritics (Ç, Ë, etc.) are cut off in the exported CSV. Add the BOM and ensure the response uses `text/csv; charset=utf-8`.",
        "PHP throws no error but the file never lands in S3. Increase `post_max_size` in `php.ini` for the upload endpoint and surface server-side errors.",
        "Add the new Sail/Docker steps to the README, including how to seed demo data and how to swap to MySQL or PostgreSQL.",
        "After a few hours the dropdown becomes unresponsive. Profile shows accumulated event listeners. Detach on close.",
        "Default to 60 requests/minute per IP with a higher tier for authenticated tokens. Return RFC-compliant `Retry-After` headers.",
        "`.env.example` currently contains real-looking API keys that confuse new contributors. Replace with placeholders and add a `SETUP.md` section.",
        "The top-level `version:` key in `compose.yml` is deprecated since Docker Compose v2.20. Remove it and verify the build still works.",
        "Coverage report shows ~40% for `SearchService`. Add tests for empty query, exact match, fuzzy match, and a tenant-isolated dataset.",
        "On Firefox 121 the sidebar collapse animation drops frames. Looks like a transform vs width mismatch — switch to `will-change: transform`.",
        "On months where the 31st exists, the calendar widget shows an off-by-one error. The bug is in `daysInMonth()` — write a regression test first.",
        "Clicking a column header sorts the table, but a full page refresh resets the order. Persist sort state in the URL query string.",
        "There are at least five `dd()` calls left in production code. Add a simple `pre-commit` hook (or PHPStan rule) to fail on them.",
        "All queue workers currently log unstructured strings. Wrap them with a Monolog processor that adds `job_id`, `attempt`, `queue` and `runtime_ms`.",
        "We adopted a `flags` config layer last sprint. Document the API, the conventions for naming flags, and the manual ramp procedure.",
    ];

    public function definition(): array
    {
        $idx = $this->faker->numberBetween(0, count(self::TITLES) - 1);

        return [
            'project_id'  => Project::factory(),
            'title'       => self::TITLES[$idx],
            'description' => self::DESCRIPTIONS[$idx],
            'status'      => $this->faker->randomElement(Issue::STATUSES),
            'priority'    => $this->faker->randomElement(Issue::PRIORITIES),
            'due_date'    => $this->faker->optional()->dateTimeBetween('now', '+2 months'),
        ];
    }
}
