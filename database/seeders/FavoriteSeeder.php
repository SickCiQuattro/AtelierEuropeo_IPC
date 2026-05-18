<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@atelier.it')->first();

        if (!$user) {
            $this->command->warn('Utente user@atelier.it non trovato: preferiti non assegnati.');
            return;
        }

        $favoriteTitles = ['Speak UP!', 'Act on Stage'];

        $projects = Project::whereIn('title', $favoriteTitles)->get(['id', 'title']);

        $missingTitles = array_values(array_diff($favoriteTitles, $projects->pluck('title')->all()));
        if (!empty($missingTitles)) {
            $this->command->warn('Progetti preferiti mancanti: ' . implode(', ', $missingTitles));
        }

        if ($projects->isEmpty()) {
            $this->command->warn('Nessun progetto preferito valido trovato: nessun preferito assegnato.');
            return;
        }

        $user->favoriteProjects()->syncWithoutDetaching($projects->pluck('id')->all());

        $this->command->info('✓ Preferiti assegnati a user@atelier.it: ' . $projects->pluck('title')->implode(', '));
    }
}
