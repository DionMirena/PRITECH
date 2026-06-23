@extends('layouts.app')
@section('title', 'New project')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1 class="h4 mb-3"><i class="bi bi-folder-plus me-2"></i>New project</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf
                    @include('projects._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
