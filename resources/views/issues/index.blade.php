@extends('layouts.app')
@section('title', 'Issues')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 class="h4 mb-0"><i class="bi bi-bug me-2"></i>Issues</h1>
        <p class="text-muted mb-0 small">Search, filter and triage across all projects.</p>
    </div>
    <a href="{{ route('issues.create') }}" class="btn btn-warning">
        <i class="bi bi-plus-lg"></i> New issue
    </a>
</div>

<form class="filter-bar" data-issues-filter onsubmit="return false;">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small text-muted mb-1">Search title / description</label>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search issues…">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Status</label>
            <select name="status" class="form-select">
                <option value="">Any</option>
                @foreach (\App\Models\Issue::STATUSES as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Priority</label>
            <select name="priority" class="form-select">
                <option value="">Any</option>
                @foreach (\App\Models\Issue::PRIORITIES as $priority)
                    <option value="{{ $priority }}" @selected(request('priority') === $priority)>
                        {{ ucfirst($priority) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Tag</label>
            <select name="tag" class="form-select">
                <option value="">Any tag</option>
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}" @selected((int) request('tag') === $tag->id)>{{ $tag->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>

<div class="card">
    <div data-issues-target>
        @include('issues._table')
    </div>
</div>

<div class="mt-3 d-flex justify-content-center" data-issues-pager>
    {{ $issues->links() }}
</div>
@endsection
