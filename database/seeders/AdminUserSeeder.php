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
            ['email' => 'hola@artacho.org'],
            [
                'name' => 'Administrador',
                'role' => 'admin',
                'password' => Hash::make(env('SEEDER_ADMIN_PASSWORD')),
                'email_verified_at' => now(),
            ]
        );
        
    }
}
