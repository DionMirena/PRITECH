@extends('layouts.app')
@section('title', $project->name)

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
        </div>
        <h1 class="h3 mb-1">{{ $project->name }}</h1>
        <div class="text-muted small">
            @if ($project->owner) Owner: <strong>{{ $project->owner->name }}</strong> · @endif
            Created {{ $project->created_at->diffForHumans() }}
        </div>
    </div>
    <div class="btn-group">
        <a href="{{ route('issues.create', ['project_id' => $project->id]) }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> Add issue
        </a>
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete this project?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="section-title">Overview</div>
                <p class="mb-3">{{ $project->description ?: 'No description provided.' }}</p>
                <div class="small">
                    <div><i class="bi bi-calendar-event text-muted"></i> Start: <strong>{{ $project->start_date?->format('M j, Y') ?? '—' }}</strong></div>
                    <div><i class="bi bi-calendar-x text-muted"></i> Deadline: <strong>{{ $project->deadline?->format('M j, Y') ?? '—' }}</strong></div>
                    <div><i class="bi bi-bug text-muted"></i> Issues: <strong>{{ $project->issues->count() }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bug me-1"></i> Issues</span>
                <a href="{{ route('issues.index', ['project_id' => $project->id]) }}" class="small text-decoration-none">View all</a>
            </div>
            @if ($project->issues->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-clipboard-x"></i>
                    <p class="mt-2 mb-0">No issues in this project yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Due</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($project->issues as $issue)
                            <tr class="issue-row">
                                <td>
                                    <a href="{{ route('issues.show', $issue) }}" class="text-decoration-none fw-semibold">
                                        {{ $issue->title }}
                                    </a>
                                    <div class="mt-1">
                                        @foreach ($issue->tags as $tag)
                                            <span class="tag-chip" style="background: {{ $tag->color ? $tag->color . '22' : '#ecf0f1' }};">
                                                <span class="color-swatch" style="background: {{ $tag->color ?? '#cccccc' }}"></span>{{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td><span class="badge {{ $statusBadges[$issue->status][1] }}">{{ $statusBadges[$issue->status][0] }}</span></td>
                                <td><span class="badge {{ $priorityBadges[$issue->priority][1] }}">{{ $priorityBadges[$issue->priority][0] }}</span></td>
                                <td class="text-muted small">{{ $issue->due_date?->format('M j, Y') ?? '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
