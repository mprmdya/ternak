<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Administrator
        User::firstOrCreate(
            ['email' => 'admin@tsu.ac.id'],
            [
                'name'     => 'Administrator',
                'password' => bcrypt('@tsu2026'),
                'role'     => 'admin',
            ]
        );

        // Akun User Demo
        User::firstOrCreate(
            ['email' => 'user@farmsense.id'],
            [
                'name'     => 'User Demo',
                'password' => bcrypt('user123'),
                'role'     => 'user',
            ]
        );
    }
}
