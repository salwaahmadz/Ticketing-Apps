@extends('cms.layouts.app')

@section('title', 'Update Role')

@section('contents')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">Update Role</h5>
                    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-secondary">
                        &larr; Back to List
                    </a>
                </div>

                <div class="card-body p-4">
                    <form id="formRole" action="{{ route('roles.update', $role->uuid) }}" method="POST" novalidate
                        enctype="multipart/form-data">
                        @csrf
                        @include('cms.pages.roles.fields')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection