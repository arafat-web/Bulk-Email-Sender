@extends('layouts.app')

@section('title', 'Create Keyword - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('keywords.index') }}">Keyword Config</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Create Keyword</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Define a dynamic placeholder for templates, e.g. <code>[city]</code>.</p>
    </div>
    <a href="{{ route('keywords.index') }}" class="btn btn-outline-primary btn-sm">Back to Keywords</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Keyword Details</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('keywords.store') }}">
                    @csrf
                    @include('keywords._form', ['keyword' => null, 'sources' => $sources])
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('keywords.index') }}" class="btn btn-outline-primary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Keyword</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
