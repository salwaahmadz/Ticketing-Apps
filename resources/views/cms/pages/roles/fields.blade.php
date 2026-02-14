<input type="hidden" value="{{ @$role->uuid }}" name="uuid" autocomplete="off">

<div class="mb-3">
    <label for="name" class="form-label">Role Name</label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', @$role->name) }}" placeholder="Enter role name" required {{ isset($role) && $role->name === 'Admin' ? 'readonly' : '' }}>

    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if(isset($role) && $role->name === 'Admin')
        <small class="text-muted">The Admin role name cannot be changed.</small>
    @endif
</div>


<div class="mb-3">
    <label class="form-label d-block fw-bold">Permissions</label>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">No.</th>
                    <th width="20%">Access List</th>
                    <th width="15%" class="text-center">Read</th>
                    <th width="15%" class="text-center">Create</th>
                    <th width="15%" class="text-center">Update</th>
                    <th width="15%" class="text-center">Delete</th>
                    <th width="15%" class="text-center">All</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $no => $permission)
                    <tr>
                        <td class="text-center">{{ $no + 1 }}</td>
                        <td class="fw-semibold">{{ $permission['group'] }}</td>

                        @php
                            $checkedCount = 0;
                        @endphp

                        @foreach ($permission['list'] as $perm)
                            @php
                                $isChecked = in_array($perm->name, $rolePermissionNames);
                                if ($isChecked) {
                                    $checkedCount++;
                                }
                            @endphp
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input permission-checkbox" type="checkbox"
                                        value="{{ $perm->name }}" name="permission[]" data-group="{{ $no }}" {{ $isChecked ? 'checked' : '' }}>
                                </div>
                            </td>
                        @endforeach

                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input check-all" type="checkbox" data-group="{{ $no }}" {{ $checkedCount == count($permission['list']) && $checkedCount > 0 ? 'checked' : '' }}>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end">
    <button type="submit" class="btn btn-primary px-4">
        {{ @$role ? 'Save Changes' : 'Create Role' }}
    </button>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle "Check All" for each row
            const checkAllBoxes = document.querySelectorAll('.check-all');

            checkAllBoxes.forEach(function (checkAllBox) {
                checkAllBox.addEventListener('change', function () {
                    const groupId = this.getAttribute('data-group');
                    const groupCheckboxes = document.querySelectorAll('.permission-checkbox[data-group="' + groupId + '"]');

                    groupCheckboxes.forEach(function (checkbox) {
                        checkbox.checked = checkAllBox.checked;
                    });
                });
            });

            // Handle individual checkbox changes to update "Check All" state
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

            permissionCheckboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const groupId = this.getAttribute('data-group');
                    const groupCheckboxes = document.querySelectorAll('.permission-checkbox[data-group="' + groupId + '"]');
                    const checkAllBox = document.querySelector('.check-all[data-group="' + groupId + '"]');

                    if (checkAllBox) {
                        const allChecked = Array.from(groupCheckboxes).every(cb => cb.checked);
                        checkAllBox.checked = allChecked;
                    }
                });
            });
        });
    </script>
@endpush