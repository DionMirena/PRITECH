@extends('layouts.app')
@section('title', 'Tags')

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <h1 class="h4 mb-3"><i class="bi bi-tag me-2"></i>Create a tag</h1>
        <div class="card">
            <div class="card-body">
                @include('partials.errors')
                <form action="{{ route('tags.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               maxlength="50" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <input type="color" name="color" value="{{ old('color', '#3498db') }}"
                               class="form-control form-control-color">
                        @error('color') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <button class="btn btn-primary"><i class="bi bi-plus-lg"></i> Create tag</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <h1 class="h4 mb-3">All tags</h1>
        <div class="card">
            @if ($tags->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-tag"></i>
                    <p class="mt-2 mb-0">No tags yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tag</th>
                                <th class="text-center">Issues</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($tags as $tag)
                            <tr>
                                <td>
                                    <span class="tag-chip" style="background: {{ $tag->color ? $tag->color . '22' : '#ecf0f1' }};">
                                        <span class="color-swatch" style="background: {{ $tag->color ?? '#cccccc' }}"></span>
                                        {{ $tag->name }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $tag->issues_count }}</span>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('Delete tag “{{ $tag->name }}”?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
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
