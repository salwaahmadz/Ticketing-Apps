@extends('cms.layouts.app')

@section('title', 'Create New Ticket')

@section('contents')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">Create New Ticket</h5>
                    <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                        &larr; Back to List
                    </a>
                </div>

                <div class="card-body p-4">
                    <form id="formTicket" action="{{ route('tickets.store') }}" method="POST" novalidate>
                        @csrf
                        @include('cms.pages.tickets.fields')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection