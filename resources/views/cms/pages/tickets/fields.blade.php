<input type="hidden" value="{{ @$ticket->uuid }}" name="uuid" autocomplete="off">

@php
    $user = auth()->user();
    $userRole = $user->roles->first()->name ?? null;
    $isAdmin = $userRole === 'Admin';
    $isSupport = $userRole === 'Support';
    $isUser = $userRole === 'User';
@endphp

@if(isset($ticket))
    <div class="alert alert-info mb-3">
        <strong>Created By:</strong> {{ $ticket->createdBy->name ?? 'N/A' }}<br>
        <strong>Ticket Code:</strong> {{ $ticket->ticket_code }}<br>
        <strong>Created At:</strong>
        {{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->format('d M Y H:i') : '-' }}
    </div>
@endif

@if($isSupport)
    <div class="mb-3">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="">Select Status</option>
            <option value="open" {{ old('status', @$ticket->status) == 'open' ? 'selected' : '' }}>Open</option>
            <option value="in_progress" {{ old('status', @$ticket->status) == 'in_progress' ? 'selected' : '' }}>In
                Progress</option>
            <option value="resolved" {{ old('status', @$ticket->status) == 'resolved' ? 'selected' : '' }}>Resolved
            </option>
            <option value="closed" {{ old('status', @$ticket->status) == 'closed' ? 'selected' : '' }}>Closed</option>
        </select>

        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="alert alert-warning">
        <small><strong>Note:</strong> As Support staff, you can only update the ticket status.</small>
    </div>
@else
    <div class="mb-3">
        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', @$ticket->title) }}" placeholder="Enter ticket title" required>

        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" rows="5"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="Enter ticket description">{{ old('description', @$ticket->description) }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row">
        @if($isAdmin)
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="">Select Status</option>
                    <option value="open" {{ old('status', @$ticket->status ?? 'open') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ old('status', @$ticket->status) == 'in_progress' ? 'selected' : '' }}>In
                        Progress</option>
                    <option value="resolved" {{ old('status', @$ticket->status) == 'resolved' ? 'selected' : '' }}>Resolved
                    </option>
                    <option value="closed" {{ old('status', @$ticket->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>

                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @elseif($isUser && isset($ticket))
            <div class="col-md-6 mb-3">
                <label for="status_display" class="form-label">Status</label>
                <input type="text" id="status_display" class="form-control" 
                    value="{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}" readonly>
                <small class="text-muted">You cannot change the ticket status.</small>
            </div>
        @endif

        <div class="col-md-{{ $isAdmin ? '6' : '12' }} mb-3">
            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
            <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                <option value="">Select Priority</option>
                <option value="low" {{ old('priority', @$ticket->priority ?? 'low') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ old('priority', @$ticket->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ old('priority', @$ticket->priority) == 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ old('priority', @$ticket->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>

            @error('priority')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row">
        @if($isAdmin)
            <div class="col-md-6 mb-3">
                <label for="assigned_to" class="form-label">Assign To</label>
                <select name="assigned_to" id="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                    <option value="">Unassigned</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to', @$ticket->assigned_to) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>

                @error('assigned_to')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @elseif(isset($ticket) && $ticket->assignedTo)
            <div class="col-md-6 mb-3">
                <label for="assigned_display" class="form-label">Assigned To</label>
                <input type="text" id="assigned_display" class="form-control" 
                    value="{{ $ticket->assignedTo->name ?? 'Unassigned' }}" readonly>
            </div>
        @endif

        <div class="col-md-{{ $isAdmin ? '6' : '12' }} mb-3">
            <label for="due_date" class="form-label">Due Date</label>
            <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror"
                value="{{ old('due_date', @$ticket->due_date) }}">

            @error('due_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    @if($isUser && isset($ticket))
        <div class="alert alert-info">
            <small><strong>Note:</strong> You can only edit tickets with "Open" status. Once the ticket is in progress, you cannot edit it.</small>
        </div>
    @endif
@endif

<div class="d-grid gap-2 d-md-flex justify-content-md-end">
    <button type="submit" class="btn btn-primary px-4">
        {{ @$ticket ? 'Save Changes' : 'Create Ticket' }}
    </button>
</div>
