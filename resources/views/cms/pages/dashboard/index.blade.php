@extends('cms.layouts.app')

@section('title', 'Dashboard')

@section('contents')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <h5 class="card-title">Dashboard</h5>
                    <p class="card-text">Welcome, {{ Auth::user()->name }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection