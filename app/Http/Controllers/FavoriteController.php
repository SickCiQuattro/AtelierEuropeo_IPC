<?php

namespace App\Http\Controllers;

use App\Models\Project;
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
    public function index()
    {
        $user = Auth::user();
        
        if (!$user || $user->role === 'admin') {
            return redirect()->route('home')->with('error', 'Azione non consentita.');
        }
        
        $favoriteProjects = $user->favorites()
            ->with(['category', 'association'])
            ->where('status', 'published')
            ->orderBy('user_favorites.created_at', 'desc')
            ->paginate(12);
        
        return view('favorites.index', compact('favoriteProjects'));
    }
}
