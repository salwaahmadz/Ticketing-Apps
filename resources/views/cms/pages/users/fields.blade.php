<input type="hidden" value="{{ @$user->uuid }}" name="uuid" autocomplete="off">

<div class="mb-3">
    <label for="name" class="form-label">Full Name</label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', @$user->name) }}" placeholder="Enter full name" required>

    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email Address</label>
    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
        value="{{ old('email', @$user->email) }}" placeholder="name@example.com" required>

    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password"
            class="form-control @error('password') is-invalid @enderror"
            placeholder="{{ isset($user) ? 'Leave blank to keep current' : 'Enter password' }}">

        @if(isset($user))
            <small class="text-muted">Only fill if you want to change the password.</small>
        @endif

        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
            placeholder="Re-enter password">
    </div>
</div>

<div class="mb-3">
    <label for="role" class="form-label">Role</label>
    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
        <option value="">Select Role</option>
        @foreach ($roles as $role)
            <option value="{{ $role->id }}" {{ old('role', isset($user) && $user->roles->contains('id', $role->id) ? $role->id : '') == $role->id ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>

    @error('role')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label for="is_active" class="form-label">Status</label>
    <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror">
        <option value="">Select Status</option>
        <option value="1" {{ old('is_active', @$user->is_active) == '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ old('is_active', (isset($user) && $user->is_active == 0) ? '0' : '') === '0' ? 'selected' : '' }}>Inactive</option>
    </select>

    @error('is_active')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end">
    <button type="submit" class="btn btn-primary px-4">
        {{ @$user ? 'Save Changes' : 'Create User' }}
    </button>
</div>