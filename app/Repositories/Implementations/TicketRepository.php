<?php

namespace App\Repositories\Implementations;

use App\Helpers\General;
use App\Models\Ticket;
use Illuminate\Support\Str;
use App\Repositories\Interfaces\TicketRepositoryInterface;

class TicketRepository implements TicketRepositoryInterface
{
    protected const TICKET_NOT_FOUND = 'Ticket not found';

    /**
     * Get all tickets query builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAll()
    {
        return Ticket::with(['createdBy', 'assignedTo']);
    }

    /**
     * Get tickets by created_by user
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getByCreatedBy($userId)
    {
        return Ticket::with(['createdBy', 'assignedTo'])->where('created_by', $userId);
    }

    /**
     * Find ticket by uuid
     *
     * @param string $uuid
     * @return Ticket
     */
    public function findByUuid($uuid)
    {
        return Ticket::with(['createdBy', 'assignedTo'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    /**
     * Store new ticket
     *
     * @param array $data
     * @return Ticket
     */
    public function store($data)
    {
        try {
            $data += [
                'uuid' => Str::uuid(),
                'ticket_code' => General::generateTicketCode(),
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ];

            return Ticket::create($data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update ticket by uuid
     *
     * @param string $uuid
     * @param array $data
     * @return Ticket
     */
    public function update($uuid, $data)
    {
        try {
            $ticket = $this->findByUuid($uuid);
            $ticket->update($data);
            return $ticket;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Delete ticket by uuid
     *
     * @param string $uuid
     * @return Ticket
     */
    public function destroy($uuid)
    {
        try {
            $ticket = $this->findByUuid($uuid);
            $ticket->delete();
            return $ticket;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
