<div class="comment" data-comment-id="{{ $comment->id }}">
    <div class="d-flex justify-content-between">
        <span class="comment-author"><i class="bi bi-person-circle"></i> {{ $comment->author_name }}</span>
        <span class="comment-meta">{{ $comment->created_at->diffForHumans() }}</span>
    </div>
    <div class="mt-1">{!! nl2br(e($comment->body)) !!}</div>
</div>
