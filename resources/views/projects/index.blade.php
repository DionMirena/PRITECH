@extends('layouts.app')
@section('title', 'Projects')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 class="h4 mb-0"><i class="bi bi-folder2-open me-2"></i>Projects</h1>
        <p class="text-muted mb-0 small">Manage your team's projects and their issues.</p>
    </div>
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> New project
    </a>
</div>

@if ($projects->isEmpty())
    <div class="card">
        <div class="card-body empty-state">
            <i class="bi bi-folder-x"></i>
            <p class="mt-2 mb-0">No projects yet — create your first one.</p>
        </div>
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th class="d-none d-md-table-cell">Owner</th>
                        <th class="text-center">Issues</th>
                        <th class="d-none d-md-table-cell">Start</th>
                        <th class="d-none d-md-table-cell">Deadline</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($projects as $project)
                    <tr>
                        <td>
                            <a href="{{ route('projects.show', $project) }}" class="fw-semibold text-decoration-none">
                                {{ $project->name }}
                            </a>
                            @if ($project->description)
                                <div class="text-muted small text-truncate" style="max-width: 360px;">
                                    {{ \Illuminate\Support\Str::limit($project->description, 100) }}
                                </div>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell text-muted">{{ $project->owner?->name ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $project->issues_count }}</span>
                        </td>
                        <td class="d-none d-md-table-cell text-muted small">
                            {{ $project->start_date?->format('M j, Y') ?? '—' }}
                        </td>
                        <td class="d-none d-md-table-cell text-muted small">
                            {{ $project->deadline?->format('M j, Y') ?? '—' }}
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete this project and all its issues?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">{{ $projects->links() }}</div>
@endif
@endsection
