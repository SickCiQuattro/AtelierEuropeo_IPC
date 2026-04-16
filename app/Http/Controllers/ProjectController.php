<?php

namespace App\Http\Controllers;

use App\Models\DataLayer;
use App\Http\Requests\ProjectRequest;
use App\Models\Category;
use App\Models\Project;
use App\Models\Association;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'published');
        $allowedStatuses = ['published', 'completed', 'draft', 'all'];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'published';
        }

        $query = Project::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('sum_description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && is_array($request->category)) {
            $query->whereIn('category_id', $request->category);
        }

        if ($request->filled('duration') && is_array($request->duration)) {
            $durations = array_filter($request->duration, fn($item) => in_array($item, ['short', 'medium', 'long', 'very_long'], true));

            if (!empty($durations)) {
                $query->where(function ($durationQuery) use ($durations) {
                    foreach ($durations as $duration) {
                        if ($duration === 'short') {
                            $durationQuery->orWhereRaw('DATEDIFF(end_date, start_date) < 15');
                        }

                        if ($duration === 'medium') {
                            $durationQuery->orWhereRaw('DATEDIFF(end_date, start_date) BETWEEN 15 AND 60');
                        }

                        if ($duration === 'long') {
                            $durationQuery->orWhereRaw('DATEDIFF(end_date, start_date) BETWEEN 60 AND 180');
                        }

                        if ($duration === 'very_long') {
                            $durationQuery->orWhereRaw('DATEDIFF(end_date, start_date) > 180');
                        }
                    }
                });
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        $sort = $request->input('sort', 'relevance');

        if ($sort === 'expiring_soon') {
            $query->orderBy('expire_date', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $projects = $query->paginate(6);
        $projects->appends($request->all());

        $categories = Category::orderBy('name')->get();

        return view('project.projects')
            ->with('projects', $projects)
            ->with('categories', $categories)
            ->with('currentStatus', $status);
    }

    /**
     * Metodo helper per filtrare i progetti
     */
    private function getFilteredProjects($dl, $status)
    {
        switch ($status) {
            case 'published':
                return $dl->listProjectsByStatus('published');

            case 'draft':
                // Il middleware ha già verificato che solo admin arrivino qui
                return $dl->listProjectsByStatus('draft');

            case 'completed':
                return $dl->listProjectsByStatus('completed');
           
            default:
                // Solo per il caso 'all' verifichiamo il ruolo per decidere cosa mostrare
                $isAdmin = Auth::check() && Auth::user()->role === 'admin';
                if ($isAdmin) {
                    return $dl->listProjects(); // Tutti i progetti
                } else {
                    return $dl->listProjectsByStatus('published'); // Solo pubblicati per non-admin
                }
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dl = new DataLayer();
        $categories = $dl->listCategories();
        $associations = $dl->listAssociations();

        return view('project.editProject')
            ->with('categories', $categories)
            ->with('associations', $associations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request)
    {
        $dl = new DataLayer();
        $isDraftMode = $request->input('form_submit_mode') === 'draft';
        $data = $request->validated();

        if ($isDraftMode) {
            $data = $this->applyDraftDefaults($data);
        }

        if (empty($data['category_id']) || empty($data['association_id'])) {
            return redirect()->back()->withInput()->with('error', 'Impossibile salvare la bozza: serve almeno una categoria e un\'associazione configurate.');
        }
        
        // Gestisci l'upload dell'immagine
        if ($request->hasFile('image_path')) {
            try {
                $imagePath = $request->file('image_path')->store('projects', 'public');
                $data['image_path'] = $imagePath;
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Errore durante il caricamento dell\'immagine. Riprova.');
            }
        } elseif ($isDraftMode && empty($data['image_path'])) {
            $data['image_path'] = 'img/projects/default.png';
        }
        
        $project = $dl->addProject($data);

        if ($project) {
            $successMessage = $isDraftMode
                ? 'Bozza salvata con successo!'
                : 'Progetto creato con successo!';

            return redirect()->route('project.show', $project->id)->with('success', $successMessage);
        } else {
            return redirect()->back()->withInput()->with('error', 'Errore nella creazione del progetto. Riprova più tardi.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dl = new DataLayer();
        $project = $dl->findProjectByID($id);

        if ($project != null) {
            // Carica le testimonianze se il progetto è completato
            if ($project->status === 'completed') {
                $project->load(['testimonial.author']);
            }

            return view('project.details')->with('project', $project);
        } else {
            return redirect()->route('project.index')->with('error', 'Progetto non trovato. Potrebbe essere stato eliminato.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dl = new DataLayer();
        $project = $dl->findProjectByID($id);

        if ($project != null) {
            // Controlla se il progetto è completato
            if ($project->status === 'completed') {
                return redirect()->route('project.show', $id)
                    ->with('warning', 'Questo progetto è stato completato e non può più essere modificato.');
            }

            $categories = $dl->listCategories();
            $associations = $dl->listAssociations();

            return view('project.editProject')
                ->with('project', $project)
                ->with('categories', $categories)
                ->with('associations', $associations);
        } else {
            return redirect()->route('project.index')->with('error', 'Progetto non trovato. Impossibile modificare.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectRequest $request, string $id)
    {
        $dl = new DataLayer();
        $project = $dl->findProjectByID($id);
        $isDraftMode = $request->input('form_submit_mode') === 'draft';
        $isCompletionConfirmed = $request->boolean('completion_confirmed');

        if ($project != null) {
            // Controlla se il progetto è completato
            if ($project->status === 'completed') {
                return redirect()->route('project.show', $id)
                    ->with('warning', 'Questo progetto è stato completato e non può più essere modificato.');
            }

            $data = $request->validated();

            if ($isDraftMode) {
                $data = $this->applyDraftDefaults($data, $project);
            }
            
            // Il completamento richiede conferma esplicita dal modale nella pagina di modifica
            if (
                !$isDraftMode
                && isset($data['status'])
                && $data['status'] === 'completed'
                && $project->status !== 'completed'
                && !$isCompletionConfirmed
            ) {
                return redirect()->back()->withInput()->with('warning', 'Conferma il completamento del progetto dal modale per procedere.');
            }
            
            // Gestisci l'upload dell'immagine se presente
            if ($request->hasFile('image_path')) {
                try {
                    // Elimina la vecchia immagine SOLO se è un file di storage (non URL o path di default)
                    if ($project->image_path && 
                        !str_starts_with($project->image_path, 'http') && 
                        !str_starts_with($project->image_path, 'img/') &&
                        Storage::disk('public')->exists($project->image_path)) {
                        Storage::disk('public')->delete($project->image_path);
                    }
                    
                    // Salva la nuova immagine
                    $imagePath = $request->file('image_path')->store('projects', 'public');
                    $data['image_path'] = $imagePath;
                } catch (\Exception $e) {
                    return redirect()->back()->withInput()->with('error', 'Errore durante il caricamento dell\'immagine. Riprova.');
                }
            } else {
                if ($isDraftMode) {
                    $data['image_path'] = $project->image_path ?: 'img/projects/default.png';
                } else {
                    // Se non c'è una nuova immagine, mantieni quella esistente
                    unset($data['image_path']);
                }
            }
            
            $updatedProject = $dl->editProject($id, $data);

            if ($updatedProject) {
                $successMessage = $isDraftMode
                    ? 'Bozza aggiornata con successo!'
                    : 'Progetto aggiornato con successo!';

                return redirect()->route('project.show', $id)->with('success', $successMessage);
            } else {
                return redirect()->back()->withInput()->with('error', 'Errore nell\'aggiornamento del progetto. Riprova più tardi.');
            }
        } else {
            return redirect()->route('project.index')->with('error', 'Progetto non trovato. Impossibile aggiornare.');
        }
    }

    /**
     * Applica fallback consistenti per il salvataggio bozza.
     */
    private function applyDraftDefaults(array $data, ?Project $existingProject = null): array
    {
        $now = Carbon::now();

        $title = trim((string) ($data['title'] ?? ($existingProject?->title ?? '')));
        if ($title === '') {
            $title = 'Bozza progetto ' . $now->format('YmdHis');
        }

        $titleQuery = Project::query()->where('title', $title);
        if ($existingProject) {
            $titleQuery->where('id', '!=', $existingProject->id);
        }
        if ($titleQuery->exists()) {
            $title .= ' ' . strtoupper(substr(uniqid(), -4));
        }

        $startDate = $data['start_date']
            ?? ($existingProject?->start_date ? Carbon::parse($existingProject->start_date)->toDateString() : $now->copy()->addDay()->toDateString());

        $endDate = $data['end_date']
            ?? ($existingProject?->end_date ? Carbon::parse($existingProject->end_date)->toDateString() : Carbon::parse($startDate)->copy()->addDays(7)->toDateString());

        $expireDate = $data['expire_date']
            ?? ($existingProject?->expire_date ? Carbon::parse($existingProject->expire_date)->toDateString() : Carbon::parse($startDate)->copy()->subDay()->toDateString());

        return array_merge($data, [
            'title' => $title,
            'status' => 'draft',
            'user_id' => $data['user_id'] ?? $existingProject?->user_id ?? Auth::id(),
            'category_id' => $data['category_id'] ?? $existingProject?->category_id ?? Category::query()->value('id'),
            'association_id' => $data['association_id'] ?? $existingProject?->association_id ?? Association::query()->value('id'),
            'requested_people' => (int) ($data['requested_people'] ?? $existingProject?->requested_people ?? 0),
            'location' => $data['location'] ?? $existingProject?->location ?? 'Da definire',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'expire_date' => $expireDate,
            'sum_description' => $data['sum_description'] ?? $existingProject?->sum_description ?? 'Bozza in lavorazione',
            'full_description' => $data['full_description'] ?? $existingProject?->full_description ?? 'Contenuto in preparazione',
            'requirements' => $data['requirements'] ?? $existingProject?->requirements ?? 'Requisiti in definizione',
            'travel_conditions' => $data['travel_conditions'] ?? $existingProject?->travel_conditions ?? 'Condizioni in definizione',
            'image_path' => $data['image_path'] ?? $existingProject?->image_path ?? 'img/projects/default.png',
        ]);
    }

    /**
     * Endpoint legacy: reindirizza alla pagina modifica con apertura modale di completamento.
     */
    public function confirmCompletion(string $id)
    {
        $dl = new DataLayer();
        $project = $dl->findProjectByID($id);

        if ($project == null) {
            return redirect()->route('project.index')->with('error', 'Progetto non trovato. Impossibile completare.');
        }

        // Controlla se il progetto è già completato
        if ($project->status === 'completed') {
            return redirect()->route('project.show', $id)
                ->with('info', 'Questo progetto è già stato completato.');
        }

        return redirect()->route('project.edit', [
            'id' => $id,
            'openCompletionModal' => 1,
        ]);
    }

    /**
     * Completa il progetto dopo la conferma
     */
    public function complete(string $id)
    {
        $dl = new DataLayer();
        $project = $dl->findProjectByID($id);

        if ($project == null) {
            return redirect()->route('project.index')->with('error', 'Progetto non trovato. Impossibile completare.');
        }

        // Controlla se il progetto è già completato
        if ($project->status === 'completed') {
            return redirect()->route('project.show', $id)
                ->with('info', 'Questo progetto è già stato completato.');
        }

        $updatedProject = $dl->editProject($id, ['status' => 'completed']);

        if ($updatedProject) {
            return redirect()->route('project.show', $id)
                ->with('success', 'Progetto completato con successo! Non sarà più possibile modificarlo.');
        } else {
            return redirect()->route('project.edit', $id)
                ->with('error', 'Errore nel completamento del progetto. Riprova più tardi.');
        }
    }

    public function confirmDestroy($id)
    {
        $dl = new DataLayer();
        $project = $dl->findProjectByID($id);

        if ($project != null) {
            return redirect()->route('project.show', [
                'project' => $project->id,
                'openDeleteModal' => 1,
            ]);
        } else {
            return redirect()->route('project.index')->with('error', 'Progetto non trovato. Impossibile eliminare.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dl = new DataLayer();
        $project = $dl->findProjectByID($id);
        
        if ($project == null) {
            return redirect()->route('project.index')->with('error', 'Progetto non trovato. Impossibile eliminare.');
        }
        
        $deleted = $dl->deleteProject($id);
        
        if ($deleted) {
            return redirect()->route('project.index')->with('success', 'Progetto eliminato con successo!');
        } else {
            return redirect()->route('project.index')->with('error', 'Si è verificato un errore durante l\'eliminazione del progetto.');
        }
    }

    /**
     * Display portfolio of completed projects with testimonials.
     */
    public function portfolio(Request $request)
    {
        $query = Project::query()->where('status', 'completed');

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('sum_description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && is_array($request->category)) {
            $query->whereIn('category_id', $request->category);
        }

        if ($request->filled('duration') && is_array($request->duration)) {
            $durations = array_filter($request->duration, fn($item) => in_array($item, ['short', 'medium', 'long', 'very_long'], true));

            if (!empty($durations)) {
                $query->where(function ($durationQuery) use ($durations) {
                    foreach ($durations as $duration) {
                        if ($duration === 'short') {
                            $durationQuery->orWhereRaw('DATEDIFF(end_date, start_date) < 15');
                        }

                        if ($duration === 'medium') {
                            $durationQuery->orWhereRaw('DATEDIFF(end_date, start_date) BETWEEN 15 AND 60');
                        }

                        if ($duration === 'long') {
                            $durationQuery->orWhereRaw('DATEDIFF(end_date, start_date) BETWEEN 60 AND 180');
                        }

                        if ($duration === 'very_long') {
                            $durationQuery->orWhereRaw('DATEDIFF(end_date, start_date) > 180');
                        }
                    }
                });
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        $sort = $request->input('sort', 'relevance');

        if ($sort === 'oldest') {
            $query->orderBy('end_date', 'asc');
        } elseif ($sort === 'latest') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('end_date', 'desc');
        }

        $projects = $query->paginate(6);
        $projects->appends($request->all());

        $categories = Category::orderBy('name')->get();

        return view('project.portfolio')
            ->with('projects', $projects)
            ->with('categories', $categories);
    }

    /**
     * Validate project data via AJAX
     */
    public function validateAjax(ProjectRequest $request)
    {
        // Se arriviamo qui, la validazione è passata
        return response()->json([
            'success' => true,
            'message' => 'Validazione completata con successo'
        ]);
    }

    /**
     * Check if project title is unique via AJAX
     */
    public function checkTitleUnique(Request $request)
    {
        $title = $request->input('title');
        $projectId = $request->input('project_id'); // Per escludere il progetto corrente in modifica
        
        $query = Project::where('title', $title);
        
        // Se stiamo modificando un progetto, escludi quello corrente
        if ($projectId) {
            $query->where('id', '!=', $projectId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Esiste già un progetto con questo titolo.' : 'Titolo disponibile.'
        ]);
    }

    /**
     * Admin dashboard - Display all projects with management actions
     */
    public function dashboard()
    {
        $activeProjectsCount = Project::where('status', 'published')->count();
        $pendingApplicationsCount = Application::where('status', 'pending')->count();
        $draftProjectsCount = Project::where('status', 'draft')->count();

        $expiringProjectsBaseQuery = Project::query()
            ->where('status', 'published')
            ->whereNotNull('expire_date')
            ->whereDate('expire_date', '>=', Carbon::today())
            ->whereDate('expire_date', '<=', Carbon::today()->copy()->addDays(30))
            ->orderBy('expire_date', 'asc');

        $expiringProjectsCount = (clone $expiringProjectsBaseQuery)->count();

        $expiringProjects = $expiringProjectsBaseQuery
            ->take(3)
            ->get();

        $latestPendingApplications = Application::with(['user', 'project'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'activeProjectsCount',
            'pendingApplicationsCount',
            'expiringProjectsCount',
            'draftProjectsCount',
            'latestPendingApplications',
            'expiringProjects'
        ));
    }
}
