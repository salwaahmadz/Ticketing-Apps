@extends('cms.layouts.app')

@section('title', 'Tickets')

@section('contents')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold">Tickets List</h4>
                @can('tickets-create')
                    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                        + Create Ticket
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
                            <th scope="col">Ticket Code</th>
                            <th scope="col">Title</th>
                            <th scope="col">Status</th>
                            <th scope="col">Priority</th>
                            <th scope="col">Created By</th>
                            <th scope="col">Assigned To</th>
                            <th scope="col">Due Date</th>
                            @if (auth()->user()->can('tickets-update') || auth()->user()->can('tickets-delete'))
                                <th scope="col" class="text-end">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($tickets->count() > 0)
                            @foreach ($tickets as $ticket)
                                <tr>
                                    <td class="fw-semibold">{{ $ticket->ticket_code }}</td>

                                    <td>{{ $ticket->title }}</td>

                                    <td>
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
                                    </td>

                                    <td>
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
                                    </td>

                                    <td class="text-muted">{{ $ticket->createdBy->name ?? '-' }}</td>

                                    <td class="text-muted">{{ $ticket->assignedTo->name ?? 'Unassigned' }}</td>

                                    <td class="text-muted">
                                        {{ $ticket->due_date ? \Carbon\Carbon::parse($ticket->due_date)->format('d M Y') : '-' }}
                                    </td>

                                    @php
                                        $user = auth()->user();
                                        $userRole = $user->roles->first()->name ?? null;
                                        $isCreator = $ticket->created_by === $user->id;

                                        $hasUpdatePermission = auth()->user()->can('tickets-update');
                                        $hasDeletePermission = auth()->user()->can('tickets-delete');

                                        $canEdit = false;
                                        $canDelete = false;
                                        $canTake = false;
                                        $canReopen = false;
                                        $canClose = false;
                                        $canView = false;

                                        if ($hasUpdatePermission) {
                                            if ($userRole === 'Admin' || $userRole === 'Support') {
                                                $canEdit = $ticket->status !== 'closed';
                                            } elseif ($userRole === 'User') {
                                                $canEdit = $isCreator && $ticket->status === 'open';
                                            } else {
                                                $canEdit = $ticket->status !== 'closed';
                                            }
                                        }

                                        if ($hasDeletePermission) {
                                            $canDelete = true;
                                        }

                                        if ($hasUpdatePermission && ($userRole === 'Admin' || $userRole === 'Support')) {
                                            $canTake = !$ticket->assigned_to || $ticket->status === 'open';
                                        }

                                        if ($ticket->status === 'open' || $ticket->status === 'in_progress' || $ticket->status === 'resolved' || $ticket->status === 'closed') {
                                            if ($userRole === 'Admin' || $userRole === 'Support') {
                                                $canView = true;
                                            } elseif ($isCreator) {
                                                $canView = true;
                                            }
                                        }

                                        if ($isCreator && $ticket->status === 'resolved') {
                                            $canReopen = true;
                                            $canClose = true;
                                        }
                                    @endphp

                                    @if ($canEdit || $canDelete || $canTake || $canReopen || $canClose || $canView)
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-2">
                                                @if($canView)
                                                    <a href="{{ route('tickets.show', $ticket->uuid) }}"
                                                        class="btn btn-sm btn-outline-info">
                                                        View Ticket
                                                    </a>
                                                @endif

                                                @if($canTake)
                                                    <form action="{{ route('tickets.take', $ticket->uuid) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            onclick="return confirm('Are you sure you want to take this ticket?')">
                                                            Take Ticket
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($canReopen)
                                                    <form action="{{ route('tickets.reopen', $ticket->uuid) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning"
                                                            onclick="return confirm('Reopen this ticket? Status will change to In Progress.')">
                                                            Reopen
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($canClose)
                                                    <form action="{{ route('tickets.close', $ticket->uuid) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            onclick="return confirm('Close this ticket? This confirms the issue is resolved.')">
                                                            Close
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($canEdit)
                                                    <a href="{{ route('tickets.edit', $ticket->uuid) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        Edit
                                                    </a>
                                                @endif

                                                @if($canDelete)
                                                    <form action="{{ route('tickets.destroy', $ticket->uuid) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to delete this ticket?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="mb-2">No tickets found</div>
                                    <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-outline-primary">Create New
                                        Ticket</a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

            </div>

            @if ($tickets->hasPages())
                <div class="card-footer clearfix">
                    {!! $tickets->appends(request()->except('page'))->links() !!}
                </div>
            @endif
        </div>
    </div>
@endsection