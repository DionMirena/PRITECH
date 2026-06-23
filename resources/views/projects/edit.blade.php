@extends('layouts.app')
@section('title', 'Edit ' . $project->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1 class="h4 mb-3"><i class="bi bi-pencil-square me-2"></i>Edit project</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('projects.update', $project) }}" method="POST">
                    @csrf @method('PUT')
                    @include('projects._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
