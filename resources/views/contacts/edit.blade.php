@extends('layouts.app')

@section('title', 'Edit Contact - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}">Contacts</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Edit: {{ $contact->full_name }}</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $contact->email }}</p>
    </div>
    <a href="{{ route('contacts.index') }}" class="btn btn-outline-primary btn-sm">Back to Contacts</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Contact Information</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('contacts.update', $contact) }}">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="email" class="form-label">Email Address <span style="color:#ef4444;">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $contact->email) }}" required placeholder="email@example.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                   id="first_name" name="first_name" value="{{ old('first_name', $contact->first_name) }}" placeholder="First name">
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                   id="last_name" name="last_name" value="{{ old('last_name', $contact->last_name) }}" placeholder="Last name">
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone', $contact->phone) }}" placeholder="Phone number">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="company" class="form-label">Company</label>
                            <input type="text" class="form-control @error('company') is-invalid @enderror"
                                   id="company" name="company" value="{{ old('company', $contact->company) }}" placeholder="Company name">
                            @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span style="color:#ef4444;">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $contact->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $contact->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="bounced" {{ old('status', $contact->status) == 'bounced' ? 'selected' : '' }}>Bounced</option>
                                <option value="unsubscribed" {{ old('status', $contact->status) == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tags</label>
                            @if($tags->count() > 0)
                                <div class="row g-2">
                                    @foreach($tags as $tag)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="tags[]" value="{{ $tag->id }}"
                                                       id="tag_{{ $tag->id }}" {{ in_array($tag->id, old('tags', $contact->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tag_{{ $tag->id }}" style="font-size:13px;">
                                                    <span class="badge me-1" style="background-color:{{ $tag->color }};color:#fff;">{{ $tag->name }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="font-size:13px;color:#94a3b8;">No tags yet. <a href="{{ route('tags.create') }}">Create one</a>.</p>
                            @endif
                            @error('tags')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3" placeholder="Additional notes...">{{ old('notes', $contact->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('contacts.index') }}" class="btn btn-outline-primary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
