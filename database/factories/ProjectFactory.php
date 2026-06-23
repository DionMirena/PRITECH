<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+1 month');

        return [
            'owner_id'    => User::factory(),
            'name'        => $this->faker->unique()->catchPhrase(),
            'description' => $this->faker->paragraph(),
            'start_date'  => $start,
            'deadline'    => (clone $start)->modify('+'.$this->faker->numberBetween(14, 90).' days'),
        ];
    }
}
