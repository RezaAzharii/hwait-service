<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'admin123',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'admin',
        ]);
    }
}