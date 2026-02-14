<?php

namespace App\Http\Controllers\Cms;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

class TicketController extends Controller
{
    private $ticketRepository;
    private $userRepository;

    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $user = auth()->user();
        $userRole = $user->roles->first()->name ?? null;

        if ($userRole === 'Admin' || $userRole === 'Support') {
            $tickets = $this->ticketRepository
                ->getAll()
                ->orderBy('id', 'desc')
                ->paginate(10);
        } else {
            $tickets = $this->ticketRepository
                ->getByCreatedBy($user->id)
                ->orderBy('id', 'desc')
                ->paginate(10);
        }

        return view('cms.pages.tickets.index', compact('tickets'));
    }

    public function show($uuid)
    {
        $user = auth()->user();
        $userRole = $user->roles->first()->name ?? null;
        $ticket = $this->ticketRepository->findByUuid($uuid);

        if ($userRole === 'User' && $ticket->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('cms.pages.tickets.show', compact('ticket'));
    }

    public function create()
    {
        $users = $this->userRepository->getAll()->get();

        return view('cms.pages.tickets.create', compact('users'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->roles->first()->name ?? null;

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
        ];

        if ($userRole === 'Admin') {
            $rules['status'] = 'required|in:open,in_progress,resolved,closed';
            $rules['assigned_to'] = 'nullable|exists:users,id';
        }

        $validated = $request->validate($rules, [
            'title.required' => 'Title is required',
            'priority.required' => 'Priority is required',
            'priority.in' => 'Invalid priority value',
            'status.in' => 'Invalid status value',
            'assigned_to.exists' => 'Selected user does not exist',
            'due_date.date' => 'Due date must be a valid date',
        ]);

        if ($userRole !== 'Admin') {
            $validated['status'] = 'open';
            $validated['assigned_to'] = null;
        }

        $this->ticketRepository->store($validated);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket created successfully');
    }

    public function edit($uuid)
    {
        $user = auth()->user();
        $userRole = $user->roles->first()->name ?? null;
        $ticket = $this->ticketRepository->findByUuid($uuid);

        if ($userRole === 'User' && $ticket->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        if ($userRole === 'User' && $ticket->status !== 'open') {
            return redirect()->route('tickets.index')
                ->with('error', 'You can only edit tickets with Open status.');
        }

        if ($ticket->status === 'closed') {
            return redirect()->route('tickets.index')
                ->with('error', 'Closed tickets are read-only and cannot be edited.');
        }

        $users = $this->userRepository->getAll()->get();
        return view('cms.pages.tickets.update', compact('ticket', 'users'));
    }

    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $userRole = $user->roles->first()->name ?? null;
        $ticket = $this->ticketRepository->findByUuid($uuid);

        if ($userRole === 'User' && $ticket->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        if ($userRole === 'User' && $ticket->status !== 'open') {
            return redirect()->route('tickets.index')
                ->with('error', 'You can only edit tickets with Open status.');
        }

        if ($ticket->status === 'closed') {
            return redirect()->route('tickets.index')
                ->with('error', 'Closed tickets are read-only and cannot be edited.');
        }
        $rules = [];

        if ($userRole === 'Admin') {
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:open,in_progress,resolved,closed',
                'priority' => 'required|in:low,medium,high,urgent',
                'assigned_to' => 'nullable|exists:users,id',
                'due_date' => 'nullable|date',
            ];
        } elseif ($userRole === 'Support') {
            $rules = [
                'status' => 'required|in:open,in_progress,resolved,closed',
            ];
        } else {
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'required|in:low,medium,high,urgent',
                'due_date' => 'nullable|date',
            ];
        }

        $validated = $request->validate($rules, [
            'title.required' => 'Title is required',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status value',
            'priority.required' => 'Priority is required',
            'priority.in' => 'Invalid priority value',
            'assigned_to.exists' => 'Selected user does not exist',
            'due_date.date' => 'Due date must be a valid date',
        ]);

        $this->ticketRepository->update($uuid, $validated);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket updated successfully');
    }

    public function destroy($uuid)
    {
        if (!auth()->user()->can('tickets-delete')) {
            abort(403, 'Unauthorized action.');
        }

        $this->ticketRepository->destroy($uuid);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully');
    }

    public function takeTicket($uuid)
    {
        $user = auth()->user();
        $userRole = $user->roles->first()->name ?? null;

        if ($userRole !== 'Admin' && $userRole !== 'Support') {
            abort(403, 'Unauthorized action.');
        }

        $ticket = $this->ticketRepository->findByUuid($uuid);

        $this->ticketRepository->update($uuid, [
            'assigned_to' => $user->id,
            'status' => 'in_progress',
        ]);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket has been assigned to you and status changed to In Progress.');
    }

    public function reopenTicket($uuid)
    {
        $user = auth()->user();
        $ticket = $this->ticketRepository->findByUuid($uuid);

        if ($ticket->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        if ($ticket->status !== 'resolved') {
            return redirect()->route('tickets.index')
                ->with('error', 'Only resolved tickets can be reopened.');
        }

        $this->ticketRepository->update($uuid, [
            'status' => 'in_progress',
        ]);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket has been reopened and status changed to In Progress.');
    }

    public function closeTicket($uuid)
    {
        $user = auth()->user();
        $ticket = $this->ticketRepository->findByUuid($uuid);

        if ($ticket->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        if ($ticket->status !== 'resolved') {
            return redirect()->route('tickets.index')
                ->with('error', 'Only resolved tickets can be closed.');
        }

        $this->ticketRepository->update($uuid, [
            'status' => 'closed',
        ]);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket has been closed successfully.');
    }
}
