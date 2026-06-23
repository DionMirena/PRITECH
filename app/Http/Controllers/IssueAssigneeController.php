<?php

namespace App\Http\Controllers;

use App\Http\Requests\SyncIssueAssigneesRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;

class IssueAssigneeController extends Controller
{
    public function store(SyncIssueAssigneesRequest $request, Issue $issue): JsonResponse
    {
        $issue->assignees()->syncWithoutDetaching([$request->integer('user_id')]);

        return response()->json([
            'assignees' => $issue->assignees()->get(['users.id', 'name']),
        ]);
    }

    public function destroy(Issue $issue, int $user): JsonResponse
    {
        $issue->assignees()->detach($user);

        return response()->json([
            'assignees' => $issue->assignees()->get(['users.id', 'name']),
        ]);
    }
}
