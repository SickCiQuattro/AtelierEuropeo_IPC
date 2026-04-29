<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use App\Models\Testimonial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ottieni solo i progetti completati
        $completedProjects = Project::where('status', 'completed')->get();
        
        if ($completedProjects->isEmpty()) {
            $this->command->info('Nessun progetto completato trovato. Creazione di alcuni progetti completati per le testimonianze...');
            
            // Crea alcuni progetti completati se non esistono
            $users = User::all();
            if ($users->isEmpty()) {
                $users = User::factory(3)->create();
            }
            
            $completedProjects = collect();
            for ($i = 0; $i < 5; $i++) {
                $project = Project::factory()->create([
                    'status' => 'completed',
                    'title' => 'Progetto Completato ' . ($i + 1),
                    'start_date' => now()->subMonths(rand(2, 12)),
                    'end_date' => now()->subMonths(rand(1, 6)),
                ]);
                $completedProjects->push($project);
            }
        }
        
        // Array di testimonianze realistiche e coerenti per progetti europei
        $testimonialTemplates = [
            [
                'content' => 'Partecipare a questo progetto è stata un\'esperienza trasformativa. Ho avuto l\'opportunità di collaborare con giovani da tutta Europa e di sviluppare competenze che porterò con me per tutta la vita. L\'organizzazione è stata eccellente e il supporto costante.',
                'type' => 'transformative'
            ],
            [
                'content' => 'Un\'esperienza incredibile che mi ha permesso di crescere sia professionalmente che personalmente. Il progetto era ben strutturato, gli obiettivi chiari e il team di supporto sempre disponibile. Consiglio vivamente questa opportunità a tutti i giovani.',
                'type' => 'growth'
            ],
            [
                'content' => 'Grazie a questo progetto ho potuto mettere in pratica le mie competenze in un contesto internazionale e multiculturale. L\'impatto sulla comunità locale è stato tangibile e questo mi ha dato una grande soddisfazione personale.',
                'type' => 'impact'
            ],
            [
                'content' => 'La qualità dell\'organizzazione e la professionalità del team di coordinamento hanno reso questa esperienza unica. Ho imparato molto sui temi europei e ho sviluppato una rete di contatti internazionale preziosa.',
                'type' => 'networking'
            ],
            [
                'content' => 'Questo progetto mi ha aperto gli occhi su realtà diverse dalla mia e mi ha insegnato l\'importanza della solidarietà europea. L\'esperienza pratica acquisita è stata fondamentale per il mio percorso professionale.',
                'type' => 'awareness'
            ],
            [
                'content' => 'Partecipare a questo progetto è stato il modo perfetto per mettere in pratica i miei studi e vedere come le politiche europee si traducono in azioni concrete sul territorio. Un\'esperienza formativa completa.',
                'type' => 'practical'
            ],
            [
                'content' => 'L\'atmosfera di collaborazione e scambio culturale è stata straordinaria. Ho lavorato con persone motivate e competenti, imparando non solo nuove skills tecniche ma anche soft skills preziose.',
                'type' => 'collaboration'
            ],
            [
                'content' => 'Il progetto ha superato le mie aspettative. La formazione ricevuta, l\'accompagnamento durante tutto il percorso e l\'impatto positivo che abbiamo generato insieme rendono questa esperienza indimenticabile.',
                'type' => 'exceeded_expectations'
            ],
            [
                'content' => 'Consiglio caldamente questa esperienza a chiunque voglia fare la differenza e crescere in un ambiente internazionale. Il progetto mi ha dato strumenti concreti per il mio futuro e una nuova prospettiva sull\'Europa.',
                'type' => 'recommendation'
            ],
            [
                'content' => 'La dimensione europea del progetto mi ha permesso di comprendere meglio le sfide e le opportunità del nostro continente. È stata un\'esperienza di cittadinanza attiva che mi ha arricchito enormemente.',
                'type' => 'european_dimension'
            ]
        ];
        
        // Ottieni tutti gli utenti disponibili per le testimonianze
        $users = User::all();
        if ($users->isEmpty()) {
            $users = User::factory(10)->create();
        }
        
        // Crea testimonianze per ogni progetto completato
        foreach ($completedProjects as $project) {
            // Crea ESATTAMENTE 3 testimonianze per ogni progetto completato
            $numTestimonials = 3;
            
            for ($i = 0; $i < $numTestimonials; $i++) {
                // Seleziona una testimonianza casuale
                $template = $testimonialTemplates[array_rand($testimonialTemplates)];
                
                // Seleziona un utente casuale (diverso dall'autore del progetto se possibile)
                $availableUsers = $users->where('id', '!=', $project->user_id);
                if ($availableUsers->isEmpty()) {
                    $availableUsers = $users;
                }
                
                $author = $availableUsers->random();
                
                // Personalizza leggermente il contenuto
                $content = $template['content'];
                
                // Aggiungi variazioni basate sul tipo di progetto
                if ($project->category) {
                    switch ($project->category->tag) {
                        case 'ESC':
                            $content = str_replace('progetto', 'progetto di Corpo Europeo di Solidarietà', $content);
                            break;
                        case 'YTH':
                            $content = str_replace('progetto', 'programma giovanile europeo', $content);
                            break;
                        case 'TRG':
                            $content = str_replace('progetto', 'corso di formazione', $content);
                            break;
                    }
                }
                
                Testimonial::create([
                    'author_id' => $author->id,
                    'project_id' => $project->id,
                    'content' => $content,
                    'created_at' => $project->end_date ? 
                        \Carbon\Carbon::parse($project->end_date)->addDays(rand(1, 30)) : 
                        now()->subDays(rand(1, 30)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
