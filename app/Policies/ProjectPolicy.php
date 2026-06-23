<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Project $project): bool
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(?User $user, Project $project): bool
    {
        if (! $user) {
            return true;
        }

        return $project->owner_id === null || $project->owner_id === $user->id;
    }

    public function delete(?User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }
}
