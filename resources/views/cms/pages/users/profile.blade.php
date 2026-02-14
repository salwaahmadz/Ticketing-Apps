@extends('cms.layouts.app')

@section('title', 'My Profile')

@section('contents')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle"></i> My Profile
                    </h5>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('users.profile.update', $user->uuid) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" placeholder="Enter your full name" required>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" placeholder="name@example.com" required>

                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">Role</label>
                            <input type="text" class="form-control" value="{{ $user->roles->first()->name ?? 'No Role' }}"
                                disabled>
                            <small class="text-muted">Role cannot be changed from profile page.</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">Account Status</label>
                            <input type="text" class="form-control" value="{{ $user->is_active ? 'Active' : 'Inactive' }}"
                                disabled>
                            <small class="text-muted">Status cannot be changed from profile page.</small>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3 text-muted">Change Password</h6>
                        <p class="text-muted small">Leave blank if you don't want to change your password.</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-bold">New Password</label>
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Enter new password">

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">Confirm New Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="Re-enter new password">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer text-muted small">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Account Created:</strong> {{ $user->created_at->format('M d, Y') }}
                        </div>
                        <div class="col-md-6 text-md-end">
                            <strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    @endpush
@endsection