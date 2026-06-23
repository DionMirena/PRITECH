@include('partials.errors')

<div class="row g-3">
    <div class="col-md-12">
        <label class="form-label">Project <span class="text-danger">*</span></label>
        <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
            <option value="">— Select a project —</option>
            @foreach ($projects as $p)
                <option value="{{ $p->id }}" @selected(old('project_id', $issue->project_id) == $p->id)>{{ $p->name }}</option>
            @endforeach
        </select>
        @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" value="{{ old('title', $issue->title) }}"
               class="form-control @error('title') is-invalid @enderror" required>
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description', $issue->description) }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror">
            @foreach (\App\Models\Issue::STATUSES as $s)
                <option value="{{ $s }}" @selected(old('status', $issue->status ?? 'open') === $s)>
                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                </option>
            @endforeach
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Priority</label>
        <select name="priority" class="form-select @error('priority') is-invalid @enderror">
            @foreach (\App\Models\Issue::PRIORITIES as $p)
                <option value="{{ $p }}" @selected(old('priority', $issue->priority ?? 'medium') === $p)>{{ ucfirst($p) }}</option>
            @endforeach
        </select>
        @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Due date</label>
        <input type="date" name="due_date"
               value="{{ old('due_date', optional($issue->due_date)->format('Y-m-d')) }}"
               class="form-control @error('due_date') is-invalid @enderror">
        @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    @isset($tags)
        <div class="col-md-12">
            <label class="form-label">Tags</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($tags as $t)
                    @php $checked = collect(old('tags', []))->contains($t->id); @endphp
                    <label class="tag-chip" style="background: {{ $t->color ? $t->color . '22' : '#ecf0f1' }}; cursor:pointer;">
                        <input type="checkbox" name="tags[]" value="{{ $t->id }}" {{ $checked ? 'checked' : '' }}
                               class="form-check-input me-1">
                        <span class="color-swatch" style="background: {{ $t->color ?? '#cccccc' }}"></span>{{ $t->name }}
                    </label>
                @endforeach
            </div>
        </div>
    @endisset
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary">
        <i class="bi bi-check2"></i> {{ $issue->exists ? 'Save changes' : 'Create issue' }}
    </button>
    <a href="{{ $issue->exists ? route('issues.show', $issue) : route('issues.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
