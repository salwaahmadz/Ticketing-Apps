<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'uuid' => Str::uuid(),
                'name' => $faker->name(),
                'email' => $faker->email(),
                'password' => 'password',
            ]);
        }
    }
}
