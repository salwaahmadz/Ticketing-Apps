<?php

namespace App\Repositories\Interfaces;

interface TicketRepositoryInterface
{
    public function getAll();

    public function getByCreatedBy($userId);

    public function findByUuid($uuid);

    public function store($data);

    public function update($uuid, $data);

    public function destroy($uuid);
}
