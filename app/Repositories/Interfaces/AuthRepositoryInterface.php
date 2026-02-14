<?php

namespace App\Repositories\Interfaces;

interface AuthRepositoryInterface
{
    public function login(array $data);

    public function forgot(array $data);

    public function reset(array $data);

    public function validateResetToken($token, $email);

    public function logout();
}
