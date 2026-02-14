@extends('cms.layouts.app')

@section('title', 'Users')

@section('contents')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold">Users List</h4>
                @can('users-create')
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        + Create User
                    </a>
                @endcan
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col" class="text-center">Status</th>
                            @if (
                                    (auth()->user()->roles->isNotEmpty() && @auth()->user()->roles[0]->name == 'Admin') ||
                                    auth()->user()->canany([
                                        'users-update',
                                        'users-delete',
                                    ])
                                )
                                <th scope="col" class="text-end">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($users->count() > 0)
                            @foreach ($users as $user)
                                <tr>
                                    <td class="fw-semibold">{{ $user->name }}</td>

                                    <td class="text-muted">{{ $user->email }}</td>

                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $user->roles->first()?->name ?? 'No Role' }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        @if($user->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>

                                    @if (
                                            (auth()->user()->roles->isNotEmpty() && @auth()->user()->roles[0]->name == 'Admin') ||
                                            auth()->user()->canany([
                                                'users-update',
                                                'users-delete',
                                            ])
                                        )
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-2">
                                                @can('users-update')
                                                    <a href="{{ route('users.edit', $user->uuid) }}" class="btn btn-sm btn-outline-primary">
                                                        Edit
                                                    </a>
                                                @endcan

                                                @can('users-delete')
                                                    <form action="{{ route('users.destroy', $user->uuid) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-2">No users found</div>
                                    <a href="{{ route('users.create') }}" class="btn btn-sm btn-outline-primary">Create New
                                        User</a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

            </div>

            @if ($users->hasPages())
                <div class="card-footer clearfix">
                    {!! $users->appends(request()->except('page'))->links() !!}
                </div>
            @endif
        </div>
    </div>
@endsection