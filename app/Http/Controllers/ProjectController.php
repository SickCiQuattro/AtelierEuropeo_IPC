<?php

namespace App\Http\Controllers;

use App\Models\DataLayer;
use App\Http\Requests\ProjectRequest;
use App\Models\Category;
use App\Models\Project;
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
        $data = $request->validated();
        
        // Gestisci l'upload dell'immagine
        if ($request->hasFile('image_path')) {
            try {
                $imagePath = $request->file('image_path')->store('projects', 'public');
                $data['image_path'] = $imagePath;
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Errore durante il caricamento dell\'immagine. Riprova.');
            }
        }
        
        $project = $dl->addProject($data);

        if ($project) {
            return redirect()->route('project.show', $project->id)->with('success', 'Progetto creato con successo!');
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

        if ($project != null) {
            // Controlla se il progetto è completato
            if ($project->status === 'completed') {
                return redirect()->route('project.show', $id)
                    ->with('warning', 'Questo progetto è stato completato e non può più essere modificato.');
            }

            $data = $request->validated();
            
            // Se si tenta di impostare lo status come 'completed', reindirizza alla pagina di conferma
            if (isset($data['status']) && $data['status'] === 'completed' && $project->status !== 'completed') {
                // Salva i dati del form in sessione per riutilizzarli dopo la conferma
                session(['completion_form_data' => $data]);
                return redirect()->route('project.confirm.completion', ['id' => $id]);
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
                // Se non c'è una nuova immagine, mantieni quella esistente
                unset($data['image_path']);
            }
            
            $updatedProject = $dl->editProject($id, $data);

            if ($updatedProject) {
                return redirect()->route('project.show', $id)->with('success', 'Progetto aggiornato con successo!');
            } else {
                return redirect()->back()->withInput()->with('error', 'Errore nell\'aggiornamento del progetto. Riprova più tardi.');
            }
        } else {
            return redirect()->route('project.index')->with('error', 'Progetto non trovato. Impossibile aggiornare.');
        }
    }

    /**
     * Mostra la pagina di conferma per il completamento del progetto
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

        // Recupera i dati del form dalla sessione
        $formData = session('completion_form_data', []);
        
        if (empty($formData)) {
            return redirect()->route('project.edit', $id)
                ->with('error', 'Sessione scaduta. Riprova a modificare il progetto.');
        }
        
        return view('project.confirmCompletion')
            ->with('project', $project)
            ->with('formData', $formData);
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

        // Recupera i dati del form dalla sessione
        $formData = session('completion_form_data', []);
        
        if (empty($formData)) {
            return redirect()->route('project.edit', $id)
                ->with('error', 'Dati di sessione mancanti. Riprova.');
        }

        // Assicurati che lo status sia impostato su 'completed'
        $formData['status'] = 'completed';

        // Non gestire l'upload dell'immagine qui - sarà gestito separatamente se necessario
        // Rimuovi sempre il campo image_path dai dati della sessione
        unset($formData['image_path']);

        $updatedProject = $dl->editProject($id, $formData);

        // Rimuovi i dati dalla sessione
        session()->forget('completion_form_data');

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
            return view('project.deleteProject')->with('project', $project);
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
