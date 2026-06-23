@include('partials.errors')

<div class="row g-3">
    <div class="col-md-12">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', $project->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $project->description) }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Start date</label>
        <input type="date" name="start_date"
               value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}"
               class="form-control @error('start_date') is-invalid @enderror">
        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Deadline</label>
        <input type="date" name="deadline"
               value="{{ old('deadline', optional($project->deadline)->format('Y-m-d')) }}"
               class="form-control @error('deadline') is-invalid @enderror">
        @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary">
        <i class="bi bi-check2"></i> {{ $project->exists ? 'Save changes' : 'Create project' }}
    </button>
    <a href="{{ $project->exists ? route('projects.show', $project) : route('projects.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
