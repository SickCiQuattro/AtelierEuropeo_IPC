<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    private const NOMI_MASCHILI = [
        'Marco', 'Luca', 'Andrea', 'Matteo', 'Davide', 'Simone', 'Alessandro',
        'Francesco', 'Lorenzo', 'Riccardo', 'Filippo', 'Nicola', 'Giorgio',
        'Roberto', 'Stefano', 'Antonio', 'Giuseppe', 'Paolo', 'Leonardo', 'Sergio',
        'Emanuele', 'Gianluca', 'Tommaso', 'Daniele', 'Alberto', 'Fabio',
    ];

    private const NOMI_FEMMINILI = [
        'Sofia', 'Giulia', 'Chiara', 'Sara', 'Valentina', 'Martina', 'Federica',
        'Elisa', 'Laura', 'Francesca', 'Alessia', 'Paola', 'Claudia', 'Monica',
        'Silvia', 'Elena', 'Beatrice', 'Anna', 'Maria', 'Roberta',
        'Irene', 'Alice', 'Serena', 'Noemi', 'Camilla', 'Jessica',
    ];

    private const COGNOMI = [
        'Rossi', 'Bianchi', 'Ferrari', 'Esposito', 'Romano', 'Colombo', 'Ricci',
        'Marino', 'Greco', 'Bruno', 'Gallo', 'Conti', 'De Luca', 'Mancini',
        'Costa', 'Giordano', 'Rizzo', 'Lombardi', 'Moretti', 'Barbieri',
        'Fontana', 'Santoro', 'Mariani', 'Rinaldi', 'Caruso', 'Ferretti',
        'Pinto', 'Orlando', 'Longo', 'Fabbri', 'Villa', 'Coppola', 'Serra',
        'Ferrara', 'Testa', 'Palumbo', 'Caputo', 'Sanna', 'Messina', 'Gatti',
    ];

    private const DOMINI = [
        'gmail.com', 'libero.it', 'hotmail.it', 'yahoo.it', 'outlook.it',
        'tiscali.it', 'virgilio.it', 'fastmail.com', 'icloud.com',
    ];

    private function nomeCompleto(): string
    {
        $femminile = (bool) random_int(0, 1);
        $nome = $femminile
            ? self::NOMI_FEMMINILI[array_rand(self::NOMI_FEMMINILI)]
            : self::NOMI_MASCHILI[array_rand(self::NOMI_MASCHILI)];
        $cognome = self::COGNOMI[array_rand(self::COGNOMI)];
        return "$nome $cognome";
    }

    private function emailUnique(string $nome): string
    {
        $parti = explode(' ', mb_strtolower($nome));
        $first = iconv('UTF-8', 'ASCII//TRANSLIT', $parti[0]);
        $last  = iconv('UTF-8', 'ASCII//TRANSLIT', end($parti));
        $sep   = ['', '.', '_'][array_rand(['', '.', '_'])];
        $numero = random_int(1, 999);
        $dominio = self::DOMINI[array_rand(self::DOMINI)];
        return "{$first}{$sep}{$last}{$numero}@{$dominio}";
    }

    public function run(): void
    {
        // ---- Admin fissi ----
        $admins = [
            ['name' => 'Admin AtelierEuropeo', 'email' => 'admin@atelier.it'],
            ['name' => 'Giulia Amministratore', 'email' => 'giulia.admin@atelier.it'],
            ['name' => 'Roberto Ferretti', 'email' => 'roberto.admin@atelier.it'],
        ];

        foreach ($admins as $admin) {
            User::firstOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]
            );
        }

        // ---- 50 Utenti registrati coerenti (nomi italiani, email uniche) ----
        $existingRegistered = User::where('role', 'registered_user')->count();
        $usersNeeded = 50 - $existingRegistered;

        if ($usersNeeded > 0) {
            $usedNames = User::where('role', 'registered_user')->pluck('name')->toArray();
            $usedEmails = User::where('role', 'registered_user')->pluck('email')->toArray();

            for ($i = 0; $i < $usersNeeded; $i++) {
                // Genera nome unico
                do {
                    $nome = $this->nomeCompleto();
                } while (in_array($nome, $usedNames));
                $usedNames[] = $nome;

                // Genera email unica
                do {
                    $email = $this->emailUnique($nome);
                } while (in_array($email, $usedEmails) || User::where('email', $email)->exists());
                $usedEmails[] = $email;

                User::create([
                    'name' => $nome,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'registered_user',
                    'email_verified_at' => now()->subDays(random_int(1, 365)),
                ]);
            }
        }

        // ---- Info seeding ----
        $this->command->info('✓ Utenti creati/aggiornati: ' . User::count() . ' totali');
        $this->command->info('  - Admin: '             . User::where('role', 'admin')->count());
        $this->command->info('  - Registrati: '        . User::where('role', 'registered_user')->count());
    }
}
