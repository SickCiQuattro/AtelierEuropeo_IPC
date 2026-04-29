<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use App\Models\Application;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApplicationSeeder extends Seeder
{
    /**
     * Genera un path CV coerente e unico
     */
    private function generateCvPath(string $userName, string $projectTitle): string
    {
        $userSlug = Str::slug(str_replace(' ', '_', $userName));
        $projectSlug = Str::slug(substr($projectTitle, 0, 15));
        $uuid = Str::uuid();
        return "documents/cv_{$userSlug}_{$projectSlug}_{$uuid}.pdf";
    }

    /**
     * Nome CV da visualizzare
     */
    private function generateCvName(string $userName): string
    {
        return "CV_" . str_replace(' ', '_', $userName) . '.pdf';
    }

    /**
     * Numero telefonico italiano coerente
     */
    private function generatePhoneNumber(): string
    {
        $prefixes = ['320', '328', '329', '333', '338', '347', '348', '349',
                     '366', '370', '380', '388', '389', '391', '392', '393'];
        $prefix = $prefixes[array_rand($prefixes)];
        $number = sprintf('%03d %04d', random_int(1, 999), random_int(1, 9999));
        return "+39 {$prefix} {$number}";
    }

    /**
     * Messaggi admin in base allo status
     */
    private function randomAdminMessage(string $status): ?string
    {
        if ($status === 'pending') {
            return null;
        }

        $messages = [
            'approved' => [
                'Candidato idoneo, procediamo con la valutazione.',
                'Qualifiche corrispondenti al profilo richiesto.',
                'Esperienza internazionale riscontrabile. Approvato.',
                'Competenze linguistiche adeguate. Accettazione proposta.',
                'Motivazioni convincenti e background coerente.',
                'Profilo conforme ai requisiti. OK per selezione.',
                'Candidato promettente, confermiamo accettazione.',
            ],
            'rejected' => [
                'Candidato non idoneo per mancanza di esperienza richiesta.',
                'Esperienze insufficienti rispetto ai requisiti.',
                'Competenze linguistiche non adeguate ai fini del progetto.',
                'Candidato già in partecipazione ad altro progetto.',
                'Disponibilità temporale non coerente con progetto.',
                'Prerequisiti non soddisfatti. Rifiuto.',
                'Candidato non idoneo per questo ruolo specifico.',
            ],
        ];

        if (isset($messages[$status])) {
            return $messages[$status][array_rand($messages[$status])];
        }

        return null;
    }

    /**
     * Randomizza lo status di una candidatura
     */
    private function randomStatus(): string
    {
        $rand = random_int(1, 100);
        if ($rand <= 40) {
            return 'pending';      // 40% in attesa
        } elseif ($rand <= 70) {
            return 'approved';     // 30% approvate
        } else {
            return 'rejected';     // 30% rifiutate
        }
    }

    public function run(): void
    {
        $projects = Project::all();
        $users = User::where('role', 'registered_user')->get();

        if ($projects->isEmpty() || $users->isEmpty()) {
            $this->command->warn('⚠️  Nessun utente registrato o progetto trovato');
            return;
        }

        // Traccia candidature per evitare duplicati
        $userProjectApplications = [];
        $applicationsCreated = 0;

        foreach ($projects as $project) {
            // Numero di candidature rispetta il limite di requested_people
            // Non deve mai superare requested_people
            $maxApplications = $project->requested_people;
            
            if ($project->status === 'completed') {
                // Progetti completati: almeno 50% dei posti, al massimo il numero di posti
                $numApplications = random_int(
                    max(1, intval(ceil($maxApplications / 2))),
                    $maxApplications
                );
            } else {
                // Progetti in corso: 40-80% dei posti disponibili
                $numApplications = random_int(
                    max(1, intval(ceil($maxApplications * 0.4))),
                    intval(ceil($maxApplications * 0.8))
                );
            }

            // Seleziona utenti unici per questo progetto
            $projectUsers = $users->shuffle()->take($numApplications);

            foreach ($projectUsers as $user) {
                // Evita duplicati
                $key = "{$user->id}_{$project->id}";
                if (isset($userProjectApplications[$key])) {
                    continue;
                }
                $userProjectApplications[$key] = true;

                // Date coerenti con expire_date del progetto
                $expireDate = Carbon::parse($project->expire_date);
                $now = Carbon::now();

                if ($expireDate > $now) {
                    // Progetto ancora aperto: candidatura 1-60 giorni prima della scadenza
                    $latestDate = (clone $expireDate)->subDay();
                    $latestDate = min($latestDate, $now);
                    $earliestDate = (clone $expireDate)->subDays(60);
                } else {
                    // Progetto scaduto
                    $latestDate = (clone $expireDate)->subDay();
                    $earliestDate = (clone $expireDate)->subDays(60);
                }

                // Assicura earliest < latest
                if ($earliestDate >= $latestDate) {
                    $earliestDate = (clone $latestDate)->subDays(7);
                }

                $createdAt = $earliestDate->copy()->addDays(random_int(0, $earliestDate->diffInDays($latestDate)));

                // Per progetti completati: candidatura prima della fine
                if ($project->status === 'completed') {
                    $endDate = Carbon::parse($project->end_date);
                    if ($createdAt > $endDate) {
                        $createdAt = $endDate->copy()->subDays(random_int(5, 30));
                    }
                }

                // Genera CV path unico
                $cvPath = $this->generateCvPath($user->name, $project->title);
                $cvName = $this->generateCvName($user->name);

                // Determina status e metadata
                $status = $this->randomStatus();
                $statusUpdatedAt = null;
                $updatedByAdminId = null;

                if ($status !== 'pending') {
                    // Solo approved/rejected hanno update
                    $statusUpdatedAt = $createdAt->copy()->addDays(random_int(1, 30));
                    $admin = User::where('role', 'admin')->inRandomOrder()->first();
                    if ($admin) {
                        $updatedByAdminId = $admin->id;
                    }
                }

                // Crea candidatura con TUTTI i dati coerenti
                Application::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'project_id' => $project->id,
                    ],
                    [
                        'status' => $status,
                        'phone' => $this->generatePhoneNumber(),
                        'document_path' => $cvPath,
                        'document_name' => $cvName,
                        'admin_message' => $this->randomAdminMessage($status),
                        'status_updated_at' => $statusUpdatedAt,
                        'updated_by_admin_id' => $updatedByAdminId,
                        'created_at' => $createdAt,
                        'updated_at' => $statusUpdatedAt ?? $createdAt,
                    ]
                );

                $applicationsCreated++;
            }
        }

        $this->command->info("✓ Generate {$applicationsCreated} candidature");
        $this->command->info("  - Progetti elaborati: {$projects->count()}");
        $this->command->info("  - Utenti candidati: {$users->count()}");
        $this->command->info("  - Approvate: " . Application::where('status', 'approved')->count());
        $this->command->info("  - Rifiutate: " . Application::where('status', 'rejected')->count());
        $this->command->info("  - In attesa: " . Application::where('status', 'pending')->count());
    }
}
