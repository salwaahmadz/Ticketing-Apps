<?php

namespace App\Repositories\Interfaces;

interface RoleRepositoryInterface
{
    public function getAll();

    public function findByUuid($uuid);

    public function store($data);

    public function update($uuid, $data);

    public function destroy($uuid);

    public function getPermission();
}
