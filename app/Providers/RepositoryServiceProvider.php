<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Implementations
use App\Repositories\Implementations\AuthRepository;
use App\Repositories\Implementations\UserRepository;
use App\Repositories\Implementations\RoleRepository;

// Interfaces
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
