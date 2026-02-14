@extends('cms.layouts.app')

@section('title', 'Roles')

@section('contents')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold">Roles List</h4>
                @can('roles-create')
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        + Create Role
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
                            <th scope="col" class="text-center">Users Count</th>
                            @if (
                                    (auth()->user()->roles->isNotEmpty() && auth()->user()->roles[0]->name == 'Admin') ||
                                    auth()->user()->canany([
                                        'roles-update',
                                        'roles-delete',
                                    ])
                                )
                                <th scope="col" class="text-end">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($roles->count() > 0)
                            @foreach ($roles as $role)
                                <tr>
                                    <td class="fw-semibold">{{ $role->name }}</td>
                                    <td class="text-center">{{ $role->users_count ?? 0 }}</td>
                                    @if (
                                            (auth()->user()->roles->isNotEmpty() && auth()->user()->roles[0]->name == 'Admin') ||
                                            auth()->user()->canany([
                                                'roles-update',
                                                'roles-delete',
                                            ])
                                        )
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-2">
                                                @can('roles-update')
                                                    <a href="{{ route('roles.edit', $role->uuid) }}" class="btn btn-sm btn-outline-primary">
                                                        Edit
                                                    </a>
                                                @endcan

                                                @can('roles-delete')
                                                    @if($role->name !== 'Admin')
                                                        <form action="{{ route('roles.destroy', $role->uuid) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Are you sure you want to delete this role?')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <div class="mb-2">No roles found</div>
                                    <a href="{{ route('roles.create') }}" class="btn btn-sm btn-outline-primary">Create New
                                        Role</a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($roles->hasPages())
                <div class="card-footer clearfix">
                    {!! $roles->appends(request()->except('page'))->links() !!}
                </div>
            @endif
        </div>
    </div>
@endsection