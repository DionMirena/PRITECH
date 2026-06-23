@extends('layouts.app')
@section('title', $issue->title)

@section('content')
@php
    $statusBadges = [
        'open'        => ['Open',        'bg-primary'],
        'in_progress' => ['In Progress', 'bg-info text-dark'],
        'closed'      => ['Closed',      'bg-success'],
    ];
    $priorityBadges = [
        'low'    => ['Low',    'bg-secondary'],
        'medium' => ['Medium', 'bg-warning text-dark'],
        'high'   => ['High',   'bg-danger'],
    ];
@endphp
<div class="d-flex flex-wrap align-items-start justify-content-between mb-3 gap-2">
    <div>
        <div class="text-muted small">
            <a href="{{ route('projects.index') }}" class="text-decoration-none">Projects</a> /
            <a href="{{ route('projects.show', $issue->project) }}" class="text-decoration-none">{{ $issue->project->name }}</a>
        </div>
        <h1 class="h3 mb-1">{{ $issue->title }}</h1>
        <div class="d-flex gap-2 align-items-center flex-wrap" data-issue-quick data-issue-id="{{ $issue->id }}">
            <div class="dropdown">
                <button type="button" class="badge {{ $statusBadges[$issue->status][1] }} dropdown-toggle border-0"
                        data-bs-toggle="dropdown" aria-expanded="false"
                        data-quick-trigger="status" data-current="{{ $issue->status }}">
                    {{ $statusBadges[$issue->status][0] }}
                </button>
                <ul class="dropdown-menu shadow-sm">
                    @foreach (\App\Models\Issue::STATUSES as $s)
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2"
                                    data-quick-set="status" data-value="{{ $s }}">
                                <span class="badge {{ $statusBadges[$s][1] }}">{{ $statusBadges[$s][0] }}</span>
                                @if ($issue->status === $s)
                                    <i class="bi bi-check2 ms-auto text-success"></i>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="dropdown">
                <button type="button" class="badge {{ $priorityBadges[$issue->priority][1] }} dropdown-toggle border-0"
                        data-bs-toggle="dropdown" aria-expanded="false"
                        data-quick-trigger="priority" data-current="{{ $issue->priority }}">
                    {{ $priorityBadges[$issue->priority][0] }}
                </button>
                <ul class="dropdown-menu shadow-sm">
                    @foreach (\App\Models\Issue::PRIORITIES as $p)
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2"
                                    data-quick-set="priority" data-value="{{ $p }}">
                                <span class="badge {{ $priorityBadges[$p][1] }}">{{ $priorityBadges[$p][0] }}</span>
                                @if ($issue->priority === $p)
                                    <i class="bi bi-check2 ms-auto text-success"></i>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if ($issue->due_date)
                <span class="text-muted small"><i class="bi bi-calendar-event"></i> Due {{ $issue->due_date->format('M j, Y') }}</span>
            @endif

            <span data-quick-feedback class="small"></span>
        </div>
    </div>
    <div class="btn-group">
        <a href="{{ route('issues.edit', $issue) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
        <form action="{{ route('issues.destroy', $issue) }}" method="POST" onsubmit="return confirm('Delete this issue?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">Description</div>
            <div class="card-body">
                @if ($issue->description)
                    {!! nl2br(e($issue->description)) !!}
                @else
                    <span class="text-muted">No description provided.</span>
                @endif
            </div>
        </div>

        <div class="card" data-comments data-issue-id="{{ $issue->id }}">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-chat-left-dots me-1"></i> Comments
                    <span class="badge bg-light text-dark border ms-1" data-comment-counter>0</span>
                </span>
            </div>
            <div class="card-body">
                <form data-comment-form class="mb-3">
                    <div data-comment-errors></div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="author_name" class="form-control" placeholder="Your name" required maxlength="100">
                        </div>
                        <div class="col-md-8">
                            <textarea name="body" rows="2" class="form-control" placeholder="Write a comment…" required maxlength="5000"></textarea>
                        </div>
                    </div>
                    <div class="mt-2 text-end">
                        <button class="btn btn-primary btn-sm"><i class="bi bi-send"></i> Post comment</button>
                    </div>
                </form>

                <div data-comment-list></div>

                <div class="text-center mt-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm d-none" data-comment-more>
                        <i class="bi bi-arrow-down-circle"></i> Load more
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3" data-issue-tags data-issue-id="{{ $issue->id }}">
            <div class="card-header"><i class="bi bi-tag me-1"></i> Tags</div>
            <div class="card-body">
                <div class="mb-2" data-tag-list>
                    @forelse ($issue->tags as $tag)
                        <span class="tag-chip me-1 mb-1"
                              style="background: {{ $tag->color ? $tag->color . '2e' : '#ecf0f1' }}; border-color: {{ $tag->color }};"
                              data-tag-id="{{ $tag->id }}">
                            <span class="color-swatch" style="background: {{ $tag->color ?? '#cccccc' }}"></span>
                            {{ $tag->name }}
                            <button type="button" class="btn-close" data-detach aria-label="Remove tag"></button>
                        </span>
                    @empty
                        <span class="text-muted small">No tags yet.</span>
                    @endforelse
                </div>

                <div class="input-group input-group-sm mt-2">
                    <select class="form-select" data-tag-select>
                        <option value="">— Add a tag —</option>
                        @foreach ($allTags as $tag)
                            @if (! in_array($tag->id, $attachedIds, true))
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button class="btn btn-primary" data-tag-add><i class="bi bi-plus-lg"></i></button>
                </div>
                <div data-tag-feedback class="mt-2"></div>
            </div>
        </div>

        <div class="card" data-issue-assignees data-issue-id="{{ $issue->id }}">
            <div class="card-header"><i class="bi bi-people me-1"></i> Assignees</div>
            <div class="card-body">
                <div data-assignee-list class="mb-2">
                    @forelse ($issue->assignees as $u)
                        <span class="tag-chip me-1 mb-1" data-user-id="{{ $u->id }}">
                            <i class="bi bi-person-circle"></i> {{ $u->name }}
                            <button type="button" class="btn-close" data-detach aria-label="Remove"></button>
                        </span>
                    @empty
                        <span class="text-muted small">Nobody assigned yet.</span>
                    @endforelse
                </div>

                <div class="input-group input-group-sm mt-2">
                    <select class="form-select" data-assignee-select>
                        <option value="">— Assign member —</option>
                        @foreach ($allUsers as $u)
                            @if (! in_array($u->id, $assigneeIds, true))
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button class="btn btn-primary" data-assignee-add><i class="bi bi-plus-lg"></i></button>
                </div>
                <div data-assignee-feedback class="mt-2"></div>
            </div>
        </div>
    </div>
</div>
@endsection
