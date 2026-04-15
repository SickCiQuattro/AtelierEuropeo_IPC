<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Aggiungi o rimuovi un progetto dai preferiti (endpoint REST)
     */
    public function toggle(Project $project, Request $request)
    {
        try {
            $user = $request->user();
            
            // Verifica che l'utente sia loggato e non sia admin
            if (!$user || $user->role === 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Azione non consentita.',
                ], 403);
            }

            $user->favorites()->toggle($project->id);
            $isFavorited = $user->favorites()->where('project_id', $project->id)->exists();
            
            return response()->json([
                'status' => 'success',
                'is_favorited' => $isFavorited,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore. Riprova più tardi.',
            ], 500);
        }
    }

    /**
     * Endpoint legacy: mantiene compatibilita con le viste che inviano project_id nel body.
     */
    public function toggleLegacy(Request $request)
    {
        try {
            $projectId = (int) $request->input('project_id');
            $user = $request->user();

            if (!$user || $user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Azione non consentita.',
                ], 403);
            }

            $project = Project::findOrFail($projectId);

            $user->favorites()->toggle($project->id);
            $isFavorited = $user->favorites()->where('project_id', $project->id)->exists();

            return response()->json([
                'success' => true,
                'message' => $isFavorited ? 'Progetto aggiunto ai preferiti!' : 'Progetto rimosso dai preferiti!',
                'action' => $isFavorited ? 'added' : 'removed',
                'is_favorite' => $isFavorited,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Si è verificato un errore. Riprova più tardi.',
            ], 500);
        }
    }
    
    /**
     * Mostra la lista dei progetti preferiti dell'utente
     */
    public function index(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user || $user->role === 'admin') {
            return redirect()->route('home')->with('error', 'Azione non consentita.');
        }
        
        $hasAnyFavorites = $user->favorites()
            ->where('status', 'published')
            ->exists();

        $favoriteProjectsQuery = $user->favorites()
            ->with(['category', 'association'])
            ->where('status', 'published');

        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $favoriteProjectsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('sum_description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && is_array($request->category)) {
            $favoriteProjectsQuery->whereIn('category_id', $request->category);
        }

        if ($request->filled('duration') && is_array($request->duration)) {
            $durations = array_filter($request->duration, fn($item) => in_array($item, ['short', 'medium', 'long', 'very_long'], true));

            if (!empty($durations)) {
                $favoriteProjectsQuery->where(function ($durationQuery) use ($durations) {
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
            $favoriteProjectsQuery->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $favoriteProjectsQuery->whereDate('start_date', '<=', $request->date_to);
        }

        $sort = $request->input('sort', 'relevance');

        if ($sort === 'expiring_soon') {
            $favoriteProjectsQuery->orderBy('expire_date', 'asc');
        } elseif ($sort === 'latest') {
            $favoriteProjectsQuery->orderBy('projects.created_at', 'desc');
        } else {
            $favoriteProjectsQuery->orderBy('user_favorites.created_at', 'desc');
        }

        $favoriteProjects = $favoriteProjectsQuery->paginate(6);
        $favoriteProjects->appends($request->all());

        $categories = Category::orderBy('name')->get();

        return view('favorites.index', compact('favoriteProjects', 'categories', 'hasAnyFavorites'));
    }
}
