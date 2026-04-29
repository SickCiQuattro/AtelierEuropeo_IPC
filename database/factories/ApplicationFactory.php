<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Genera un document_path coerente
     */
    private function generateCvPath(string $userName = null, string $projectTitle = null): string
    {
        $userSlug = $userName ? Str::slug(str_replace(' ', '_', $userName)) : 'user_' . random_int(1000, 9999);
        $projectSlug = $projectTitle ? Str::slug(substr($projectTitle, 0, 15)) : 'project_' . random_int(100, 999);
        $uuid = Str::uuid();
        return "documents/cv_{$userSlug}_{$projectSlug}_{$uuid}.pdf";
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentTypes = ['CV', 'Lettera di Motivazione', 'Portfolio', 'Certificati'];
        $documentExtensions = ['pdf', 'doc', 'docx'];
        
        // Date di default (saranno sovrascritte quando viene specificato un progetto)
        $createdAt = $this->faker->dateTimeBetween('-60 days', '-1 day');
        $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now');
        
        return [
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'user_id' => User::where('role', 'registered_user')->inRandomOrder()->first()?->id ?? User::factory()->create(['role' => 'registered_user'])->id,
            'project_id' => Project::inRandomOrder()->first()?->id ?? Project::factory()->create()->id,
            'phone' => '+39 ' . $this->faker->randomElement(['320','328','329','333','338','347','349','366','370','380','388','389','391','392']) . ' ' . $this->faker->numerify('### ####'),
            'document_path' => $this->generateCvPath(),  // ✓ Genera sempre un path valido
            'document_name' => $this->faker->randomElement($documentTypes) . '_' . $this->faker->lastName() . '.' . $this->faker->randomElement($documentExtensions),
            'admin_message' => $this->faker->optional(0.3)->sentence(),
            'status_updated_at' => $this->faker->optional(0.7)->dateTimeBetween($createdAt, 'now'),
            'updated_by_admin_id' => $this->faker->optional(0.7)->randomElement(User::where('role', 'admin')->pluck('id')->toArray()),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
    
    /**
     * Configure the application for a specific project.
     */
    public function forProject(Project $project): static
    {
        $expireDate = new \DateTime($project->expire_date);
        $now = new \DateTime();
        
        // Calcola una data di creazione logica
        $earliestDate = (clone $expireDate)->modify('-60 days');
        
        // Se la scadenza è nel futuro
        if ($expireDate > $now) {
            // Può candidarsi da 60 giorni prima fino a 1 giorno prima della scadenza
            $latestDate = (clone $expireDate)->modify('-1 day');
            // Ma non può candidarsi nel futuro rispetto ad oggi
            if ($latestDate > $now) {
                $latestDate = clone $now;
            }
        } else {
            // Se la scadenza è nel passato, la candidatura doveva essere fatta prima
            $latestDate = (clone $expireDate)->modify('-1 day');
        }
        
        // Assicurati che earliest sia prima di latest
        if ($earliestDate >= $latestDate) {
            $earliestDate = (clone $latestDate)->modify('-7 days');
        }
        
        $createdAt = $this->faker->dateTimeBetween($earliestDate, $latestDate);
        
        // Updated può essere fino ad oggi, ma non prima di created
        $updatedAtMax = max($latestDate, $now);
        $updatedAt = $this->faker->dateTimeBetween($createdAt, $updatedAtMax);
        
        return $this->state(fn (array $attributes) => [
            'project_id' => $project->id,
            'document_path' => $this->generateCvPath(
                User::find($attributes['user_id'])?->name,
                $project->title
            ),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'status_updated_at' => $this->faker->optional(0.7)->dateTimeBetween($createdAt, $updatedAt),
        ]);
    }
    
    /**
     * Create a pending application.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'admin_message' => null,
            'status_updated_at' => null,
            'updated_by_admin_id' => null,
        ]);
    }
    
    /**
     * Create an approved application.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'admin_message' => $this->faker->randomElement([
                'Complimenti! La tua candidatura è stata approvata.',
                'Siamo felici di averti nel nostro progetto!',
                'Candidatura eccellente, benvenuto/a nel team.',
                'La tua esperienza è perfetta per questo progetto.'
            ]),
            'status_updated_at' => $this->faker->dateTimeBetween('-15 days', 'now'),
            'updated_by_admin_id' => User::where('role', 'admin')->inRandomOrder()->first()?->id,
        ]);
    }
    
    /**
     * Create a rejected application.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'admin_message' => $this->faker->randomElement([
                'Grazie per il tuo interesse, ma abbiamo selezionato altri candidati.',
                'Il profilo non corrisponde ai requisiti richiesti per questo progetto.',
                'Ti incoraggiamo a candidarti per altri progetti in futuro.',
                'Posizioni già coperte, ma ti terremo in considerazione per futuri progetti.'
            ]),
            'status_updated_at' => $this->faker->dateTimeBetween('-20 days', 'now'),
            'updated_by_admin_id' => User::where('role', 'admin')->inRandomOrder()->first()?->id,
        ]);
    }
}
