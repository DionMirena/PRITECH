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
        $alice = User::factory()->create([
            'name'  => 'Alice Owner',
            'email' => 'alice@pritech.test',
        ]);

        $bob = User::factory()->create([
            'name'  => 'Bob Member',
            'email' => 'bob@pritech.test',
        ]);

        $teamPool = User::factory(3)->create();
        $members  = $teamPool->push($alice, $bob);

        $tagPalette = [
            ['name' => 'bug',         'color' => '#e74c3c'],
            ['name' => 'feature',     'color' => '#3498db'],
            ['name' => 'enhancement', 'color' => '#2ecc71'],
            ['name' => 'urgent',      'color' => '#f39c12'],
            ['name' => 'backend',     'color' => '#9b59b6'],
            ['name' => 'frontend',    'color' => '#1abc9c'],
            ['name' => 'docs',        'color' => '#7f8c8d'],
        ];
        $tags = collect($tagPalette)->map(fn ($t) => Tag::firstOrCreate(
            ['name' => $t['name']],
            ['color' => $t['color']]
        ));

        Project::factory(5)
            ->state(fn () => ['owner_id' => $members->random()->id])
            ->create()
            ->each(function (Project $project) use ($tags, $members) {
                Issue::factory(rand(4, 9))
                    ->for($project)
                    ->create()
                    ->each(function (Issue $issue) use ($tags, $members) {
                        $issue->tags()->sync(
                            $tags->random(rand(1, 3))->pluck('id')->all()
                        );

                        $issue->assignees()->sync(
                            $members->random(rand(1, 2))->pluck('id')->all()
                        );

                        Comment::factory(rand(2, 6))->for($issue)->create();
                    });
            });
    }
}
