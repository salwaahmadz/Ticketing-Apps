@extends('cms.layouts.app')

@section('title', 'View Ticket')

@section('contents')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold">Ticket Details</h4>
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                    ← Back to List
                </a>
            </div>

            @php
                $user = auth()->user();
                $userRole = $user->roles->first()->name ?? null;
                $isCreator = $ticket->created_by === $user->id;
            @endphp

            @if($ticket->status === 'closed')
                <div class="alert alert-secondary mb-4">
                    <strong>🔒 Ticket Closed</strong><br>
                    This ticket is closed and is in read-only mode.
                </div>
            @endif

            {{-- Ticket Information Card --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title fw-bold mb-3">Ticket Information</h6>
                            
                            <div class="mb-2">
                                <small class="text-muted">Ticket Code</small>
                                <div class="fw-semibold">{{ $ticket->ticket_code }}</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Title</small>
                                <div class="fw-semibold">{{ $ticket->title }}</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Status</small>
                                <div>
                                    @php
                                        $statusClasses = [
                                            'open' => 'bg-info',
                                            'in_progress' => 'bg-primary',
                                            'resolved' => 'bg-success',
                                            'closed' => 'bg-secondary'
                                        ];
                                        $statusLabels = [
                                            'open' => 'Open',
                                            'in_progress' => 'In Progress',
                                            'resolved' => 'Resolved',
                                            'closed' => 'Closed'
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$ticket->status] ?? 'bg-secondary' }}">
                                        {{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Priority</small>
                                <div>
                                    @php
                                        $priorityClasses = [
                                            'low' => 'bg-success',
                                            'medium' => 'bg-warning',
                                            'high' => 'bg-orange',
                                            'urgent' => 'bg-danger'
                                        ];
                                    @endphp
                                    <span class="badge {{ $priorityClasses[$ticket->priority] ?? 'bg-secondary' }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title fw-bold mb-3">Assignment & Timeline</h6>
                            
                            <div class="mb-2">
                                <small class="text-muted">Created By</small>
                                <div class="fw-semibold">{{ $ticket->createdBy->name ?? 'N/A' }}</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Assigned To</small>
                                <div class="fw-semibold">{{ $ticket->assignedTo->name ?? 'Unassigned' }}</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Created At</small>
                                <div>{{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->format('d M Y H:i') : '-' }}</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Due Date</small>
                                <div>{{ $ticket->due_date ? \Carbon\Carbon::parse($ticket->due_date)->format('d M Y') : '-' }}</div>
                            </div>

                            @if($ticket->updated_at)
                                <div class="mb-2">
                                    <small class="text-muted">Last Updated</small>
                                    <div>{{ \Carbon\Carbon::parse($ticket->updated_at)->format('d M Y H:i') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="card bg-light mb-3">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">Description</h6>
                    <div class="text-muted" style="white-space: pre-wrap;">{{ $ticket->description ?? 'No description provided.' }}</div>
                </div>
            </div>

            {{-- Action Buttons for Resolved Ticket --}}
            @if($ticket->status === 'resolved' && $isCreator)
                <div class="alert alert-warning">
                    <strong>⚠️ Action Required</strong><br>
                    This ticket has been marked as resolved. Please confirm:
                </div>
                
                <div class="d-flex gap-2 mb-3">
                    <form action="{{ route('tickets.reopen', $ticket->uuid) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning"
                            onclick="return confirm('Reopen this ticket? Status will change to In Progress.')">
                            Reopen Ticket
                        </button>
                    </form>

                    <form action="{{ route('tickets.close', $ticket->uuid) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success"
                            onclick="return confirm('Close this ticket? This confirms the issue is resolved.')">
                            Close Ticket
                        </button>
                    </form>
                </div>
            @endif

            {{-- Edit Button for Non-Closed Ticket --}}
            @if($ticket->status !== 'closed')
                @php
                    $canEdit = false;
                    if ($userRole === 'Admin' || $userRole === 'Support') {
                        $canEdit = auth()->user()->can('tickets-update');
                    } elseif ($userRole === 'User' && $isCreator && $ticket->status === 'open') {
                        $canEdit = auth()->user()->can('tickets-update');
                    }
                @endphp

                @if($canEdit)
                    <div class="d-flex gap-2">
                        <a href="{{ route('tickets.edit', $ticket->uuid) }}" class="btn btn-primary">
                            Edit Ticket
                        </a>
                    </div>
                @endif
            @endif

        </div>
    </div>
@endsection
