<div class="comment" data-comment-id="{{ $comment->id }}">
    <div class="d-flex justify-content-between align-items-start">
        <span class="comment-author"><i class="bi bi-person-circle"></i> {{ $comment->author_name }}</span>
        <div class="d-flex gap-2 align-items-center comment-actions">
            <span class="comment-meta">{{ $comment->created_at->diffForHumans() }}</span>
            <button type="button" class="btn btn-link btn-sm p-0 text-secondary" data-comment-edit title="Edit">
                <i class="bi bi-pencil-square"></i>
            </button>
            <button type="button" class="btn btn-link btn-sm p-0 text-danger" data-comment-delete title="Delete">
                <i class="bi bi-trash3"></i>
            </button>
        </div>
    </div>
    <div class="mt-1" data-comment-body>{!! nl2br(e($comment->body)) !!}</div>

    <div class="mt-2 d-none" data-comment-edit-form>
        <div class="row g-1 mb-1">
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm" data-comment-edit-author
                       value="{{ $comment->author_name }}" maxlength="100" required>
            </div>
            <div class="col-md-8">
                <textarea class="form-control form-control-sm" data-comment-edit-body rows="3" maxlength="5000" required>{{ $comment->body }}</textarea>
            </div>
        </div>
        <div data-comment-edit-errors></div>
        <div class="d-flex gap-1 justify-content-end">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-comment-edit-cancel>Cancel</button>
            <button type="button" class="btn btn-sm btn-primary" data-comment-edit-save>
                <i class="bi bi-check2"></i> Save
            </button>
        </div>
    </div>
</div>
