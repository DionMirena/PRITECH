@extends('layouts.app')
@section('title', 'Edit ' . $issue->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <h1 class="h4 mb-3"><i class="bi bi-pencil-square me-2"></i>Edit issue</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('issues.update', $issue) }}" method="POST">
                    @csrf @method('PUT')
                    @include('issues._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
