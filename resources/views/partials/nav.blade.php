@php
    $isActive = fn (string $name) => request()->routeIs($name) ? 'active' : '';
@endphp
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('projects.index') }}">
            <i class="bi bi-kanban-fill text-warning me-1"></i>
            PRITECH
            <small class="opacity-75 fw-normal d-none d-md-inline">Issue Tracker</small>
        </a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#mainnav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="mainnav" class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ $isActive('projects.*') }}" href="{{ route('projects.index') }}">
                        <i class="bi bi-folder2-open"></i> Projects
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $isActive('issues.*') }}" href="{{ route('issues.index') }}">
                        <i class="bi bi-bug"></i> Issues
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $isActive('tags.*') }}" href="{{ route('tags.index') }}">
                        <i class="bi bi-tag"></i> Tags
                    </a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                <a href="{{ route('projects.create') }}" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-plus-lg"></i> New project
                </a>
                <a href="{{ route('issues.create') }}" class="btn btn-warning btn-sm fw-semibold">
                    <i class="bi bi-plus-lg"></i> New issue
                </a>
            </div>
        </div>
    </div>
</nav>
