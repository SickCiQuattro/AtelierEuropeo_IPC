<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    /**
     * Mostra le candidature dell'utente autenticato
     */
    public function index()
    {
        // Gli admin non possono accedere alle candidature personali
        if (Auth::user()->role === 'admin') {
            return redirect()->route('home')->with('error', 'Gli amministratori non hanno candidature personali.');
        }

        $applications = Application::with(['project', 'user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('applications.index', compact('applications'));
    }

    /**
     * Mostra il form per candidarsi a un progetto
     */
    public function create($id)
    {
        // Verifica che l'utente sia autenticato
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Devi essere registrato per candidarti.');
        }

        // Gli admin non possono candidarsi
        if (Auth::user()->role === 'admin') {
            return redirect()->route('project.show', $id)->with('error', 'Gli amministratori non possono inviare candidature.');
        }

        // Trova il progetto
        $project = Project::findOrFail($id);

        // Verifica che l'utente non si sia già candidato
        $existingApplication = Application::where('user_id', Auth::id())
            ->where('project_id', $project->id)
            ->first();

        if ($existingApplication) {
            return redirect()->route('project.show', $project->id)
                ->with('info', 'Ti sei già candidato per questo progetto. Puoi controllare lo stato della tua candidatura.');
        }

        return view('applications.create', compact('project'));
    }

    /**
     * Salva una nuova candidatura
     */
    public function store(Request $request, $id)
    {
        // Verifica che l'utente sia autenticato
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Devi essere registrato per candidarti.');
        }

        // Gli admin non possono candidarsi
        if (Auth::user()->role === 'admin') {
            return redirect()->route('project.show', $id)->with('error', 'Gli amministratori non possono inviare candidature.');
        }

        // Trova il progetto
        $project = Project::findOrFail($id);

        // Verifica che l'utente non si sia già candidato
        $existingApplication = Application::where('user_id', Auth::id())
            ->where('project_id', $project->id)
            ->first();

        if ($existingApplication) {
            return redirect()->route('project.show', $project->id)
                ->with('info', 'Ti sei già candidato per questo progetto. Puoi controllare lo stato della tua candidatura.');
        }

        // Validazione dei dati
        $request->validate([
            'phone' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf|max:5120' // max 5MB
        ], [
            'phone.required' => 'Il numero di telefono è obbligatorio.',
            'document.required' => 'Il documento PDF è obbligatorio.',
            'document.mimes' => 'Il documento deve essere in formato PDF.',
            'document.max' => 'Il documento non può superare i 5MB.'
        ]);

        $documentPath = null;
        $documentName = null;

        // Gestione upload documento PDF
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentName = $file->getClientOriginalName();
            
            // Crea un nome file unico per evitare conflitti
            $uniqueName = time() . '_' . Auth::id() . '_' . $documentName;
            $documentPath = $file->storeAs('applications', $uniqueName, 'public');
        }

        // Crea la candidatura
        Application::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'phone' => $request->phone,
            'status' => 'pending',
            'document_path' => $documentPath,
            'document_name' => $documentName,
        ]);

        return redirect()->route('project.show', $project->id)
            ->with('success', 'La tua candidatura è stata inviata con successo! Riceverai una conferma via email e ti contatteremo presto per comunicarti l\'esito della selezione.');
    }

    /**
     * Ritira una candidatura (solo se in attesa)
     */
    public function withdraw(Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403, 'Non hai i permessi per ritirare questa candidatura.');
        }

        if ($application->status !== 'pending') {
            return redirect()->route('applications.index')
                ->with('error', 'Puoi ritirare solo candidature ancora in attesa di valutazione.');
        }

        // Elimina il documento allegato se presente
        if ($application->document_path) {
            Storage::disk('public')->delete($application->document_path);
        }

        $application->delete();

        return redirect()->route('applications.index')
            ->with('success', 'Candidatura ritirata con successo.');
    }

    /**
     * Mostra i dettagli di una candidatura
     */
    public function show(Application $application)
    {
        // Verifica che l'utente possa vedere questa candidatura
        if ($application->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Non hai i permessi per visualizzare questa candidatura.');
        }

        return view('applications.show', compact('application'));
    }
}