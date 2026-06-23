<?php

namespace App\Http\Controllers;

use App\Http\Requests\SyncIssueTagsRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;

class IssueTagController extends Controller
{
    public function store(SyncIssueTagsRequest $request, Issue $issue): JsonResponse
    {
        $issue->tags()->syncWithoutDetaching([$request->integer('tag_id')]);

        return response()->json([
            'tags' => $issue->tags()->get(['id', 'name', 'color']),
        ]);
    }

    public function destroy(Issue $issue, int $tag): JsonResponse
    {
        $issue->tags()->detach($tag);

        return response()->json([
            'tags' => $issue->tags()->get(['id', 'name', 'color']),
        ]);
    }
}
