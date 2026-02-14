<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    protected $fillable = ['uuid', 'name', 'guard_name'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
