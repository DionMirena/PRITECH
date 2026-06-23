@if ($errors->any())
    <div class="alert alert-danger">
        <strong><i class="bi bi-exclamation-triangle"></i> Please review the following:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
