<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Utenti fissi (credenziali certe per il testing) ----
        $fixedUsers = [
            ['name' => 'Admin AtelierEuropeo', 'email' => 'admin@atelier.it', 'role' => 'admin'],
            ['name' => 'Giulia Amministratore', 'email' => 'giulia.admin@atelier.it', 'role' => 'admin'],
            ['name' => 'Roberto Ferretti', 'email' => 'roberto.admin@atelier.it', 'role' => 'admin'],
            ['name' => 'Marco Esposito', 'email' => 'user@atelier.it', 'role' => 'registered_user'],
            ['name' => 'Chiara Romano', 'email' => 'chiara.romano@atelier.it', 'role' => 'registered_user'],
            ['name' => 'Luca Moretti', 'email' => 'luca.moretti@atelier.it', 'role' => 'registered_user'],
            ['name' => 'Sofia Bianchi', 'email' => 'sofia.bianchi@atelier.it', 'role' => 'registered_user'],
            ['name' => 'Andrea Conti', 'email' => 'andrea.conti@atelier.it', 'role' => 'registered_user'],
        ];

        foreach ($fixedUsers as $fixedUser) {
            User::query()->updateOrCreate(
                ['email' => $fixedUser['email']],
                [
                    'name'     => $fixedUser['name'],
                    'password' => Hash::make('password'),
                    'role'     => $fixedUser['role'],
                ]
            );
        }

        // ---- Utenti casuali con nomi italiani ----
        User::factory()->italian()->count(20)->create();

        // ---- Qualche utente non verificato ----
        User::factory()->unverified()->count(4)->create();

        $this->command->info('Creati ' . User::count() . ' utenti totali');
        $this->command->info('- Admin: '             . User::where('role', 'admin')->count());
        $this->command->info('- Utenti registrati: ' . User::where('role', 'registered_user')->count());
    }
}
