@extends('layouts.app')

@section('title', 'Edit Contact - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}">Email Contacts</a></li>
    <li class="breadcrumb-item active">Edit Contact</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">Edit Contact</h1>
                    <p class="text-muted mb-0">Update contact information and tags</p>
                </div>
                <a href="{{ route('contacts.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Contacts
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark">
                                <i class="bi bi-pencil-square me-2 text-primary"></i>Contact Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('contacts.update', $contact) }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <!-- Email Address -->
                                    <div class="col-12">
                                        <label for="email" class="form-label text-dark fw-medium">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               id="email" name="email" value="{{ old('email', $contact->email) }}" required
                                               placeholder="Enter email address">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- First Name -->
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label text-dark fw-medium">First Name</label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                               id="first_name" name="first_name" value="{{ old('first_name', $contact->first_name) }}"
                                               placeholder="Enter first name">
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Last Name -->
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label text-dark fw-medium">Last Name</label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                               id="last_name" name="last_name" value="{{ old('last_name', $contact->last_name) }}"
                                               placeholder="Enter last name">
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label text-dark fw-medium">Phone</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" name="phone" value="{{ old('phone', $contact->phone) }}"
                                               placeholder="Enter phone number">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Company -->
                                    <div class="col-md-6">
                                        <label for="company" class="form-label text-dark fw-medium">Company</label>
                                        <input type="text" class="form-control @error('company') is-invalid @enderror"
                                               id="company" name="company" value="{{ old('company', $contact->company) }}"
                                               placeholder="Enter company name">
                                        @error('company')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-6">
                                        <label for="status" class="form-label text-dark fw-medium">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="active" {{ old('status', $contact->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $contact->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="bounced" {{ old('status', $contact->status) == 'bounced' ? 'selected' : '' }}>Bounced</option>
                                            <option value="unsubscribed" {{ old('status', $contact->status) == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Tags -->
                                    <div class="col-12">
                                        <label for="tags" class="form-label text-dark fw-medium">Tags</label>
                                        <div class="mb-2">
                                            <small class="text-muted">Select one or more tags to organize this contact</small>
                                        </div>

                                        @if($tags->count() > 0)
                                            <div class="row g-2">
                                                @foreach($tags as $tag)
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            @php
                                                                $isChecked = in_array($tag->id, old('tags', $contact->tags->pluck('id')->toArray()));
                                                            @endphp
                                                            <input type="checkbox" class="form-check-input"
                                                                   name="tags[]" value="{{ $tag->id }}"
                                                                   id="tag_{{ $tag->id }}"
                                                                   {{ $isChecked ? 'checked' : '' }}>
                                                            <label class="form-check-label text-dark" for="tag_{{ $tag->id }}">
                                                                <span class="badge me-2" style="background-color: {{ $tag->color }}; color: white;">>
                                                                    {{ $tag->name }}
                                                                </span>
                                                                @if($tag->description)
                                                                    <small class="text-muted d-block">{{ $tag->description }}</small>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>No tags available. Create tags to organize your contacts.</span>
                                                    <a href="{{ route('tags.create') }}" class="btn btn-sm btn-primary">Create Tag</a>
                                                </div>
                                            </div>
                                        @endif

                                        @error('tags')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Notes -->
                                    <div class="col-12">
                                        <label for="notes" class="form-label text-dark fw-medium">Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                                  id="notes" name="notes" rows="3" placeholder="Additional notes about this contact...">{{ old('notes', $contact->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Update Contact
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-control, .form-select {
        background-color: #ffffff;
        border: 1px solid #d1d5db;
        color: #374151;
        transition: all 0.2s ease-in-out;
    }

    .form-control:focus, .form-select:focus {
        background-color: #ffffff;
        border-color: var(--primary-color);
        color: #374151;
        box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
    }

    .form-control::placeholder {
        color: #9ca3af;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
    }

    .card {
        border-radius: var(--border-radius);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: var(--shadow-lg);
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, #6d28d9 100%);
        transform: translateY(-1px);
    }
</style>
@endpush
