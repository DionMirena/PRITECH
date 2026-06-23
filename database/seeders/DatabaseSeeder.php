<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $team = $this->seedTeam();
        $tags = $this->seedTags();

        $projects = $this->seedProjects($team);

        foreach ($projects as $project) {
            $count = random_int(5, 9);

            Issue::factory($count)
                ->for($project)
                ->create()
                ->each(function (Issue $issue) use ($tags, $team) {
                    $issue->tags()->sync(
                        $tags->random(random_int(1, 3))->pluck('id')->all()
                    );

                    $issue->assignees()->sync(
                        $team->random(random_int(1, 2))->pluck('id')->all()
                    );

                    Comment::factory(random_int(2, 5))->for($issue)->create();
                });
        }
    }

    private function seedTeam(): \Illuminate\Support\Collection
    {
        $members = [
            ['name' => 'Alice Carter',   'email' => 'alice@pritech.test',   'role' => 'Tech Lead'],
            ['name' => 'Bob Hernandez',  'email' => 'bob@pritech.test',     'role' => 'Senior Backend Engineer'],
            ['name' => 'Sarah Chen',     'email' => 'sarah@pritech.test',   'role' => 'Full Stack Engineer'],
            ['name' => 'Marcus Rivera',  'email' => 'marcus@pritech.test',  'role' => 'Frontend Engineer'],
            ['name' => 'Priya Shah',     'email' => 'priya@pritech.test',   'role' => 'DevOps Engineer'],
            ['name' => 'Tom Müller',     'email' => 'tom@pritech.test',     'role' => 'QA Engineer'],
            ['name' => 'Lena Kowalski',  'email' => 'lena@pritech.test',    'role' => 'UI/UX Designer'],
            ['name' => 'David Park',     'email' => 'david@pritech.test',   'role' => 'Product Manager'],
            ['name' => 'Elena Rossi',    'email' => 'elena@pritech.test',   'role' => 'Mobile Engineer'],
            ['name' => 'Jordan Bailey',  'email' => 'jordan@pritech.test',  'role' => 'Site Reliability Engineer'],
        ];

        return collect($members)->map(fn (array $m) => User::factory()->create([
            'name'  => $m['name'],
            'email' => $m['email'],
        ]));
    }

    private function seedTags(): \Illuminate\Support\Collection
    {
        $palette = [
            ['name' => 'bug',          'color' => '#e74c3c'],
            ['name' => 'feature',      'color' => '#3498db'],
            ['name' => 'enhancement',  'color' => '#2ecc71'],
            ['name' => 'urgent',       'color' => '#f39c12'],
            ['name' => 'backend',      'color' => '#9b59b6'],
            ['name' => 'frontend',     'color' => '#1abc9c'],
            ['name' => 'docs',         'color' => '#7f8c8d'],
            ['name' => 'devops',       'color' => '#34495e'],
            ['name' => 'database',     'color' => '#e67e22'],
            ['name' => 'security',     'color' => '#c0392b'],
        ];

        return collect($palette)->map(fn (array $t) => Tag::firstOrCreate(
            ['name' => $t['name']],
            ['color' => $t['color']]
        ));
    }

    private function seedProjects(\Illuminate\Support\Collection $team): \Illuminate\Support\Collection
    {
        $catalog = [
            [
                'name'        => 'Customer Portal Redesign',
                'description' => 'Rebuild the customer-facing portal on a modern Laravel + Vue stack with improved accessibility and faster page loads.',
            ],
            [
                'name'        => 'Mobile App v2',
                'description' => 'Refresh the iOS and Android apps with offline support, push notifications and a new design language.',
            ],
            [
                'name'        => 'Public REST API',
                'description' => 'Expose a clean, versioned public REST API with OAuth2, rate limiting and full OpenAPI documentation.',
            ],
            [
                'name'        => 'CI/CD Modernization',
                'description' => 'Replace the existing Jenkins setup with GitHub Actions, parallelised test runs and preview environments per PR.',
            ],
            [
                'name'        => 'Search Service Migration',
                'description' => 'Migrate the search layer from MySQL LIKE queries to Meilisearch with typo tolerance, synonyms and per-tenant indexes.',
            ],
            [
                'name'        => 'Design System Foundation',
                'description' => 'Establish a shared design system: tokens, primitives, documented components and Storybook used across all products.',
            ],
            [
                'name'        => 'Reporting & Analytics Pipeline',
                'description' => 'Stream events into a warehouse, build dashboards in Metabase and define the core product KPIs leadership reviews weekly.',
            ],
        ];

        $start = now()->subDays(30);

        return collect($catalog)->map(function (array $p, int $i) use ($team, $start) {
            return Project::create([
                'owner_id'    => $team->random()->id,
                'name'        => $p['name'],
                'description' => $p['description'],
                'start_date'  => $start->copy()->addDays($i * 4),
                'deadline'    => $start->copy()->addDays($i * 4 + random_int(45, 90)),
            ]);
        });
    }
}
