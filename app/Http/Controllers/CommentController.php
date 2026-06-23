<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function index(Issue $issue): JsonResponse
    {
        $comments = $issue->comments()->paginate(10);

        return response()->json([
            'comments' => $comments->getCollection()->map(fn (Comment $c) => [
                'id'   => $c->id,
                'html' => view('issues._comment', ['comment' => $c])->render(),
            ]),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page'    => $comments->lastPage(),
                'total'        => $comments->total(),
                'has_more'     => $comments->hasMorePages(),
                'next_page'    => $comments->currentPage() + ($comments->hasMorePages() ? 1 : 0),
            ],
        ]);
    }

    public function store(StoreCommentRequest $request, Issue $issue): JsonResponse
    {
        $comment = $issue->comments()->create($request->validated());

        return response()->json([
            'comment' => [
                'id'   => $comment->id,
                'html' => view('issues._comment', ['comment' => $comment])->render(),
            ],
        ], 201);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $comment->update($request->validated());

        return response()->json([
            'comment' => [
                'id'   => $comment->id,
                'html' => view('issues._comment', ['comment' => $comment])->render(),
            ],
        ]);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json(['deleted' => true]);
    }
}
