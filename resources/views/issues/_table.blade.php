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

@if ($issues->isEmpty())
    <div class="empty-state">
        <i class="bi bi-search"></i>
        <p class="mt-2 mb-0">No issues match these filters.</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th class="d-none d-md-table-cell">Project</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th class="d-none d-lg-table-cell">Tags</th>
                    <th class="d-none d-md-table-cell">Due</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($issues as $issue)
                <tr class="issue-row">
                    <td>
                        <a href="{{ route('issues.show', $issue) }}" class="text-decoration-none fw-semibold">
                            {{ $issue->title }}
                        </a>
                    </td>
                    <td class="d-none d-md-table-cell text-muted small">
                        <a href="{{ route('projects.show', $issue->project) }}" class="text-decoration-none text-muted">
                            {{ $issue->project->name }}
                        </a>
                    </td>
                    <td><span class="badge {{ $statusBadges[$issue->status][1] }}">{{ $statusBadges[$issue->status][0] }}</span></td>
                    <td><span class="badge {{ $priorityBadges[$issue->priority][1] }}">{{ $priorityBadges[$issue->priority][0] }}</span></td>
                    <td class="d-none d-lg-table-cell">
                        @foreach ($issue->tags as $tag)
                            <span class="tag-chip mb-1" style="background: {{ $tag->color ? $tag->color . '22' : '#ecf0f1' }};">
                                <span class="color-swatch" style="background: {{ $tag->color ?? '#cccccc' }}"></span>{{ $tag->name }}
                            </span>
                        @endforeach
                    </td>
                    <td class="d-none d-md-table-cell text-muted small">{{ $issue->due_date?->format('M j, Y') ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
