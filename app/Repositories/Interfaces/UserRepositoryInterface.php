<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function getAll();

    public function getByUuid(string $uuid);

    public function create(array $data);

    public function update(string $uuid, array $data);

    public function delete(string $uuid);
}
