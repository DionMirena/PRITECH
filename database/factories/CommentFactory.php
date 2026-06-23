<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    private const AUTHORS = [
        'Sarah Chen',
        'Marcus Rivera',
        'Priya Shah',
        'Tom Müller',
        'Lena Kowalski',
        'David Park',
        'Elena Rossi',
        'Jordan Bailey',
        'Yusuf Demir',
        'Hannah Schmidt',
    ];

    private const BODIES = [
        'I can reproduce this on the latest build — I will pick it up after the standup.',
        'Looks like the issue is in the controller. Pushing a fix branch shortly.',
        'Confirmed on staging too. Bumping the priority to high.',
        'Could we add a regression test for this before merging?',
        'I started a draft PR — it still needs review from the backend team.',
        'I traced the root cause to a missing index on the `created_at` column.',
        'This works for me locally on PHP 8.3. Which environment is failing?',
        'Marking this as blocked until the API contract is finalised.',
        'Closing this — the migration ran cleanly on production after the latest fix.',
        'Adding a quick sanity check in the request validation should be enough.',
        'I would prefer to ship a feature flag for this, just to be safe.',
        'Pair-programmed this with David earlier — patch incoming.',
        'Double-checked the logs: only authenticated users hit this code path.',
        'We should also update the README to reflect the new env variable.',
        'Looks good to me. Once tests are green I will merge.',
        'The N+1 is fixed — added `with(\'project\', \'tags\')` on the index query.',
        'Please rebase on top of main, there is a small conflict in `routes/web.php`.',
        'I wrote a Pest test that captures the regression — see commit `c3aa21c`.',
        'Reverting for now, will reopen with a smaller scope.',
        'Documented the change in `docs/CHANGELOG.md`. Ready for QA.',
    ];

    public function definition(): array
    {
        return [
            'issue_id'    => Issue::factory(),
            'author_name' => $this->faker->randomElement(self::AUTHORS),
            'body'        => $this->faker->randomElement(self::BODIES),
        ];
    }
}
