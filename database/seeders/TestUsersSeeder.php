<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios de prueba si no existen
        $testUsers = [
            [
                'name' => 'Autor Ejemplo',
                'email' => 'autor@ejemplo.com',
                'password' => bcrypt('password'),
                'role' => 'editor'
            ],
            [
                'name' => 'Lector Activo',
                'email' => 'lector@ejemplo.com', 
                'password' => bcrypt('password'),
                'role' => 'lector'
            ],
            [
                'name' => 'Seguidor Fiel',
                'email' => 'seguidor@ejemplo.com',
                'password' => bcrypt('password'),
                'role' => 'lector'
            ]
        ];

        foreach ($testUsers as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $this->command->info('Usuarios de prueba creados correctamente');
    }
}
