<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminApplicationController extends Controller
{
    /**
     * Mostra tutte le candidature di tutti i progetti (vista globale admin)
     */
    public function all(Request $request)
    {

        $query = Application::with(['user', 'project.category', 'updatedByAdmin'])
            ->orderBy('created_at', 'desc');

        // Filtro per stato
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        // Filtro per progetto
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Ricerca per nome o email candidato, o titolo progetto
        if ($request->filled('search')) {
            $search = $request->search;
            // Raggruppato: senza il where annidato l'OR annullerebbe i filtri status/project
            $query->where(function ($outer) use ($search) {
                $outer->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('project', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            });
        }

        $applications = $query->paginate(20)->withQueryString();

        $stats = [
            'total'    => Application::count(),
            'pending'  => Application::where('status', 'pending')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];

        // Lista progetti per il filtro a tendina
        $projects = Project::orderBy('title')->get(['id', 'title']);

        return view('admin.applications.all', compact('applications', 'stats', 'projects'));
    }

    /**
     * Mostra tutte le candidature per un progetto specifico
     */
    public function index($projectId, Request $request)
    {

        $project = Project::findOrFail($projectId);

        $query = Application::with(['user', 'project.category', 'updatedByAdmin'])
            ->where('project_id', $projectId)
            ->orderBy('created_at', 'desc');

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $stats = [
            'pending'  => Application::where('project_id', $projectId)->where('status', 'pending')->count(),
            'approved' => Application::where('project_id', $projectId)->where('status', 'approved')->count(),
            'rejected' => Application::where('project_id', $projectId)->where('status', 'rejected')->count(),
            'total'    => Application::where('project_id', $projectId)->count(),
        ];

        $applications = $query->paginate(15)->withQueryString();

        return view('admin.applications.index', compact('project', 'applications', 'stats'));
    }

    /**
     * Mostra i dettagli di una candidatura
     */
    public function show(Application $application)
    {
        $application->load(['user', 'project', 'updatedByAdmin']);
        return view('admin.applications.show', compact('application'));
    }

    /**
     * Aggiorna lo stato di una candidatura
     */
    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_message' => 'nullable|string|max:1000'
        ]);

        $error = $this->applyDecision($application, [
            'status' => $request->status,
            'admin_message' => $request->admin_message,
        ]);

        if ($error) {
            return redirect()->back()->with('error', $error);
        }

        $statusText = [
            'approved' => 'approvata',
            'rejected' => 'rifiutata',
            'pending' => 'rimessa in attesa'
        ];

        return redirect()->back()->with('success', 
            'Candidatura ' . $statusText[$request->status] . ' con successo!');
    }

    /**
     * Approva una candidatura
     */
    public function approve(Request $request, Application $application)
    {
        $request->validate([
            'admin_message' => 'nullable|string|max:1000'
        ]);

        $error = $this->applyDecision($application, [
            'status' => 'approved',
            'admin_message' => $request->admin_message ?? 'La tua candidatura è stata approvata! Ti contatteremo presto per i prossimi passi.',
        ]);

        if ($error) {
            return redirect()->back()->with('error', $error);
        }

        return redirect()->back()->with('success', 'Candidatura approvata con successo!');
    }

    /**
     * Rifiuta una candidatura
     */
    public function reject(Request $request, Application $application)
    {
        $request->validate([
            'admin_message' => 'required|string|max:1000'
        ]);

        $application->update([
            'status' => 'rejected',
            'admin_message' => $request->admin_message,
            'status_updated_at' => now(),
            'updated_by_admin_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Candidatura rifiutata con messaggio inviato all\'utente.');
    }

    /**
     * Salva la decisione. Per 'approved' blocca la riga del progetto e controlla i posti
     * nella stessa transazione, così due admin non superano il limite insieme.
     * La candidatura corrente è esclusa dal conteggio. Ritorna un errore o null.
     */
    private function applyDecision(Application $application, array $attributes): ?string
    {
        return DB::transaction(function () use ($application, $attributes) {
            if ($attributes['status'] === 'approved') {
                $project = Project::whereKey($application->project_id)->lockForUpdate()->first();
                $approved = Application::where('project_id', $project->id)
                    ->where('status', 'approved')
                    ->whereKeyNot($application->getKey())
                    ->count();

                if ($approved >= $project->requested_people) {
                    return "Non è possibile approvare questa candidatura. Il progetto ha già raggiunto il numero massimo di partecipanti richiesti ({$project->requested_people}).";
                }
            }

            $application->update($attributes + [
                'status_updated_at' => now(),
                'updated_by_admin_id' => Auth::id(),
            ]);

            return null;
        });
    }
}
