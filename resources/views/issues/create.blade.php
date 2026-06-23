@extends('layouts.app')
@section('title', 'New issue')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <h1 class="h4 mb-3"><i class="bi bi-bug me-2"></i>New issue</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('issues.store') }}" method="POST">
                    @csrf
                    @include('issues._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
