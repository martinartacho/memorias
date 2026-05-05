<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'hola@hartacho.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD')),
                'email_verified_at' => now(),
            ]
        );
        
    }
}
