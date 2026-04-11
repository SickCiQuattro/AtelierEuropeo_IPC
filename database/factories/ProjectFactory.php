<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use App\Models\Association;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $europeanCities = [
            'Milano, Italia', 'Roma, Italia', 'Napoli, Italia', 'Firenze, Italia',
            'Barcelona, Spagna', 'Madrid, Spagna', 'Valencia, Spagna',
            'Parigi, Francia', 'Lione, Francia', 'Marsiglia, Francia',
            'Berlino, Germania', 'Monaco, Germania', 'Amburgo, Germania',
            'Amsterdam, Paesi Bassi', 'Rotterdam, Paesi Bassi',
            'Vienna, Austria', 'Salisburgo, Austria',
            'Lisbona, Portogallo', 'Porto, Portogallo',
            'Praga, Repubblica Ceca', 'Budapest, Ungheria',
            'Varsavia, Polonia', 'Cracovia, Polonia',
            'Stoccolma, Svezia', 'Copenaghen, Danimarca'
        ];
        
        $projectTitles = [
            // CES – Corpo Europeo di Solidarietà
            'Ecosistema Urbano',
            'Mani Solidali',
            'Orto Sociale',
            'Biblioteca Vivente',
            'Senza Barriere',
            'Verde in Città',
            'Casa di Quartiere',
            'Radici e Futuro',
            'Cucina Solidale',
            'Bosco Urbano',
            'Aule Aperte',
            'Filo di Comunità',
            // Youth
            'Dialogo Giovani UE',
            'Scambio tra Culture',
            'Giovani e Democrazia',
            'Arte Giovane Europea',
            'Campo Natura Giovani',
            'Media Giovani Europa',
            'Forum Pace Giovani',
            'Mobilità e Inclusione',
            'Storie di Quartiere',
            'Teatro per Insegnare',
            // Training
            'Formazione Non Formale',
            'Gestione Progetti per ONG',
            'Digitale per Operatori',
            'Mediazione Interculturale',
            'Raccolta Fondi Erasmus+',
            'Leadership Giovanile',
            'Impatto Sociale Progetti',
            'Benessere per Operatori',
            // Trasversali
            'Inclusione e Sport',
            'Patrimonio Digitale',
            'Giovani per il Clima',
            'Forum Cittadini Attivi',
            'Innovazione Sociale Sprint',
            'Volontariato in Rete',
            'Italiano per Migranti',
        ];

        // Genera date coerenti distribuite su 2024–2026: expire_date < start_date < end_date
        $startDate = $this->faker->dateTimeBetween('-24 months', '+12 months');
        $duration = $this->faker->numberBetween(7, 365);
        $endDate = (clone $startDate)->modify("+{$duration} days");
        
        // La data di scadenza per le candidature deve essere PRIMA della data di inizio
        $expireDaysBeforeStart = $this->faker->numberBetween(1, 30);
        $expireDate = (clone $startDate)->modify("-{$expireDaysBeforeStart} days");
        
        // Immagini di default disponibili
        $defaultImages = [
            'img/projects/default1.png',
            'img/projects/default2.png',
            'img/projects/default3.png',
            'img/projects/default4.png',
            'img/projects/default5.png',
            'img/projects/default6.png',
            'img/projects/default7.png',
            'img/projects/default8.png',
            'img/projects/default9.png',
            'img/projects/default10.png',
            'img/projects/default11.png',
            'img/projects/default12.png',
            'img/projects/default13.png',
            'img/projects/default14.png',
            'img/projects/default15.png',
            'img/projects/default16.png',
            'img/projects/default17.png',
            'img/projects/default18.png',
            'img/projects/default19.png',
            'img/projects/default20.png',
            'img/projects/default21.png',
            'img/projects/default22.png',
            'img/projects/default23.png',
            'img/projects/default24.png',
            'img/projects/default25.png',
            'img/projects/default26.jpg',
        ];
        
        return [
            'title' => $this->faker->randomElement($projectTitles),
            'user_id' => User::where('role', 'admin')->inRandomOrder()->first()?->id ?? User::factory()->admin()->create()->id,
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory()->create()->id,
            'association_id' => Association::inRandomOrder()->first()?->id ?? Association::factory()->create()->id,
            'image_path' => $this->faker->randomElement($defaultImages),
            'status' => $this->faker->randomElement(['draft', 'published', 'completed']),
            'requested_people' => $this->faker->numberBetween(2, 15),
            'location' => $this->faker->randomElement($europeanCities),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'expire_date' => $expireDate,
            'sum_description' => $this->generateRealisticSummary(),
            'full_description' => $this->generateRealisticDescription(),
            'requirements' => $this->generateRealisticRequirements(),
            'travel_conditions' => $this->generateRealisticTravelConditions(),
        ];
    }
    
    /**
     * Generate realistic short summaries (sum_description) in Italian.
     */
    private function generateRealisticSummary(): string
    {
        $summaries = [
            'Volontariato ambientale locale con azioni pratiche e sensibilizzazione ecologica.',
            'Supporto sociale a persone fragili con attività di assistenza e animazione.',
            'Scambio giovanile europeo su dialogo interculturale e cittadinanza attiva.',
            'Progetto CES per valorizzare il patrimonio culturale con strumenti digitali.',
            'Formazione non formale per operatori giovanili su metodi partecipativi europei.',
            'Servizio di volontariato per l\'inclusione sociale di persone con disabilità.',
            'Cooperazione internazionale con sport e arte per integrare comunità miste.',
            'Laboratorio di innovazione sociale per giovani e soluzioni locali concrete.',
            'Formazione su gestione progetti e raccolta fondi per enti del terzo settore.',
            'Volontariato ecologico per riforestazione e cura di aree verdi urbane.',
            'Inclusione digitale per anziani e persone vulnerabili con supporto pratico.',
            'Volontariato educativo con doposcuola e laboratori creativi di quartiere.',
            'Progetto di comunicazione per giovani su diritti umani e giustizia sociale.',
            'Servizio CES in ambito culturale tra eventi, archivi e promozione locale.',
            'Formazione residenziale su mediazione dei conflitti e facilitazione gruppi.',
            'Rigenerazione urbana partecipata con residenti e associazioni del territorio.',
            'Iniziativa climatica giovanile con campagne, eventi e azioni nei quartieri.',
            'Volontariato in biblioteca con lettura, catalogazione e attività culturali.',
        ];

        return $this->faker->randomElement($summaries);
    }

    /**
     * Generate realistic project descriptions.
     */
    private function generateRealisticDescription(): string
    {
        $descriptions = [
            "Questo progetto mira a promuovere la sostenibilità ambientale attraverso azioni concrete nella comunità locale. I partecipanti avranno l'opportunità di lavorare su iniziative di educazione ambientale, pulizia di spazi pubblici e sensibilizzazione sui cambiamenti climatici.",
            "Un'iniziativa dedicata al supporto delle fasce più vulnerabili della popolazione. Il progetto prevede attività di assistenza sociale, organizzazione di eventi comunitari e sviluppo di reti di solidarietà tra i cittadini.",
            "Progetto di cooperazione internazionale che coinvolge giovani da tutta Europa. L'obiettivo è creare ponti culturali attraverso scambi, workshop creativi e attività di promozione del dialogo interculturale.",
            "Iniziativa focalizzata sulla preservazione e valorizzazione del patrimonio culturale locale. I volontari parteciperanno a attività di catalogazione, restauro e promozione di siti storici e tradizioni culturali.",
            "Progetto innovativo che utilizza la tecnologia per risolvere problematiche sociali. Include lo sviluppo di app per il volontariato, piattaforme digitali per l'inclusione e strumenti tech per migliorare la qualità della vita."
        ];
        
        return $this->faker->randomElement($descriptions);
    }
    
    /**
     * Generate realistic requirements.
     */
    private function generateRealisticRequirements(): string
    {
        $requirements = [
            "Età: 18-30 anni. Conoscenza base dell'inglese. Motivazione e entusiasmo per il volontariato. Capacità di lavorare in team multiculturali.",
            "Maggiore età. Esperienza precedente nel volontariato (preferibile). Buone capacità comunicative. Flessibilità e adattabilità.",
            "Età: 18-25 anni. Laurea o diploma in corso. Conoscenza di almeno una lingua straniera. Interesse per l'innovazione sociale.",
            "Nessun requisito specifico di esperienza. Passione per la cultura e il patrimonio. Disponibilità per l'intero periodo del progetto.",
            "Competenze informatiche di base. Creatività e problem-solving. Capacità di lavorare in autonomia e in gruppo."
        ];
        
        return $this->faker->randomElement($requirements);
    }
    
    /**
     * Generate realistic travel conditions.
     */
    private function generateRealisticTravelConditions(): string
    {
        $conditions = [
            "Viaggio coperto fino a 275€. Alloggio in condivisione fornito. Vitto e pocket money mensile di 150€. Assicurazione sanitaria inclusa.",
            "Rimborso viaggio al 100% fino a 180€. Sistemazione in famiglia ospitante. Tre pasti al giorno inclusi. Trasporti locali coperti.",
            "Viaggio a carico del partecipante. Alloggio in struttura condivisa. Indennità giornaliera di 8€. Certificato di partecipazione Youthpass.",
            "Rimborso viaggio parziale (70%). Vitto e alloggio completamente coperti. Attività ricreative e culturali incluse nel programma.",
            "Tutti i costi coperti dall'organizzazione. Alloggio in camera singola o doppia. Supporto linguistico e culturale durante tutto il periodo."
        ];
        
        return $this->faker->randomElement($conditions);
    }
    
    /**
     * Create a published project.
     */
    public function published(): static
    {
        $startDate = $this->faker->dateTimeBetween('+2 weeks', '+8 months');
        $duration = $this->faker->numberBetween(14, 180);
        $endDate = (clone $startDate)->modify("+{$duration} days");
        $expireDaysBeforeStart = $this->faker->numberBetween(7, 21);
        $expireDate = (clone $startDate)->modify("-{$expireDaysBeforeStart} days");

        return $this->state(fn (array $attributes) => [
            'status'      => 'published',
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'expire_date' => $expireDate,
        ]);
    }
    
    /**
     * Create a completed project.
     */
    public function completed(): static
    {
        $startDate = $this->faker->dateTimeBetween('-12 months', '-1 month');
        $duration = $this->faker->numberBetween(7, 90);
        $endDate = (clone $startDate)->modify("+{$duration} days");
        
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'expire_date' => (clone $startDate)->modify('-' . $this->faker->numberBetween(7, 30) . ' days'),
        ]);
    }
}
