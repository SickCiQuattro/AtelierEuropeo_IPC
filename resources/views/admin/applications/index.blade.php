@extends('layouts.master')

@section('page_title', 'Gestione Candidature - ' . $project->title)

@section('breadcrumb')
<div class="bg-light py-2">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.projects.index') }}" class="text-decoration-none">Progetti Disponibili</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('project.show', $project) }}" class="text-decoration-none">{{ $project->title }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Candidature</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('body')
<div class="container mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-0 text-gray-800">
                Gestione Candidature
            </h1>
        </div>
    </div>

    <!-- Informazioni Progetto -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title mb-2">
                        <i class="bi bi-folder-open me-2"></i>
                        {{ $project->title }}
                    </h5>
                    <p class="card-text text-muted mb-2">{{ Str::limit($project->description, 150) }}</p>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="bi bi-calendar-event me-1"></i>
                                Scadenza: {{ $project->expire_date ? $project->expire_date->format('d/m/Y') : 'Non specificata' }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="bi bi-eye me-1"></i>
                                Stato: 
                                <span class="badge bg-{{ $project->status === 'published' ? 'success' : ($project->status === 'draft' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert per messaggi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiche -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="text-primary">
                        <i class="bi bi-clock-history fa-2x"></i>
                    </div>
                    <div class="mt-2">
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['pending'] }}
                        </div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase">In Attesa</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="text-success">
                        <i class="bi bi-check-circle fa-2x"></i>
                    </div>
                    <div class="mt-2">
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['approved'] }}
                        </div>
                        <div class="text-xs font-weight-bold text-success text-uppercase">Approvate</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="text-danger">
                        <i class="bi bi-x-circle fa-2x"></i>
                    </div>
                    <div class="mt-2">
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['rejected'] }}
                        </div>
                        <div class="text-xs font-weight-bold text-danger text-uppercase">Rifiutate</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="text-info">
                        <i class="bi bi-people fa-2x"></i>
                    </div>
                    <div class="mt-2">
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['total'] }}
                        </div>
                        <div class="text-xs font-weight-bold text-info text-uppercase">Totali</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Avviso limite partecipanti -->
    @if($stats['approved'] >= $project->requested_people)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-warning border-left-warning" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                        <div>
                            <h6 class="alert-heading mb-1">
                                <strong>Limite partecipanti raggiunto!</strong>
                            </h6>
                            <p class="mb-0">
                                Il progetto ha raggiunto il numero massimo di partecipanti richiesti 
                                (<strong>{{ $stats['approved'] }}/{{ $project->requested_people }}</strong>). 
                                Non sarà possibile approvare ulteriori candidature.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($stats['approved'] > 0)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-info border-left-info" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                        <div>
                            <p class="mb-0">
                                <strong>Partecipanti approvati:</strong> {{ $stats['approved'] }}/{{ $project->requested_people }}
                                @if($project->requested_people - $stats['approved'] > 0)
                                    - Rimangono <strong>{{ $project->requested_people - $stats['approved'] }}</strong> posti disponibili.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Lista Candidature -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Candidature ({{ $stats['total'] }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($stats['total'] > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="bi bi-person me-1"></i> Candidato</th>
                                        <th><i class="bi bi-envelope me-1"></i> Email</th>
                                        <th><i class="bi bi-telephone me-1"></i> Telefono</th>
                                        <th><i class="bi bi-calendar me-1"></i> Data Candidatura</th>
                                        <th><i class="bi bi-flag me-1"></i> Stato</th>
                                        <th><i class="bi bi-gear me-1"></i> Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ strtoupper(substr($application->user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $application->user->name }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $application->user->email }}" class="text-decoration-none">
                                                    {{ $application->user->email }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($application->phone)
                                                    <a href="tel:{{ $application->phone }}" class="text-decoration-none">
                                                        {{ $application->phone }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Non fornito</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $application->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($application->status === 'pending')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-clock-history me-1"></i>
                                                        In Attesa
                                                    </span>
                                                @elseif($application->status === 'approved')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Approvata
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle me-1"></i>
                                                        Rifiutata
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.applications.show', $application) }}" 
                                                       class="btn btn-outline-primary btn-sm" 
                                                       title="Visualizza dettagli">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    @if($application->status === 'pending')
                                                        @php
                                                            $limitReached = $stats['approved'] >= $project->requested_people;
                                                        @endphp
                                                        <button type="button" 
                                                                class="btn btn-outline-success btn-sm{{ $limitReached ? ' disabled' : '' }}" 
                                                                @if(!$limitReached)
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#approveModal{{ $application->id }}"
                                                                @endif
                                                                title="{{ $limitReached ? 'Limite partecipanti raggiunto' : 'Approva' }}"
                                                                {{ $limitReached ? 'disabled' : '' }}>
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-outline-danger btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#rejectModal{{ $application->id }}"
                                                                title="Rifiuta">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                                class="btn btn-outline-secondary btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#updateModal{{ $application->id }}"
                                                                title="Modifica stato">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginazione -->
                        @if($applications->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $applications->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">Nessuna candidatura presente</h5>
                            <p class="text-muted">Non sono ancora state inviate candidature per questo progetto.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal per approvazione -->
@foreach($applications as $application)
    @if($application->status === 'pending')
        <div class="modal fade" id="approveModal{{ $application->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.applications.approve', $application) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Approva Candidatura
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Stai per approvare la candidatura di <strong>{{ $application->user->name }}</strong>.</p>
                            
                            @php
                                $remainingSpots = $project->requested_people - $stats['approved'];
                            @endphp
                            
                            @if($remainingSpots <= 3 && $remainingSpots > 0)
                                <div class="alert alert-warning" role="alert">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Attenzione:</strong> Approvando questa candidatura rimarranno solo 
                                    <strong>{{ $remainingSpots - 1 }}</strong> posti disponibili.
                                </div>
                            @elseif($remainingSpots === 1)
                                <div class="alert alert-info" role="alert">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Ultimo posto disponibile!</strong> Approvando questa candidatura 
                                    il progetto avrà raggiunto il numero massimo di partecipanti.
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                <label for="admin_message{{ $application->id }}" class="form-label">Messaggio per il candidato (opzionale)</label>
                                <textarea class="form-control" 
                                          id="admin_message{{ $application->id }}" 
                                          name="admin_message" 
                                          rows="3" 
                                          placeholder="Inserisci un messaggio personalizzato per il candidato...">La tua candidatura è stata approvata! Ti contatteremo presto per i prossimi passi.</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i> Approva
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal per rifiuto -->
        <div class="modal fade" id="rejectModal{{ $application->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.applications.reject', $application) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-x-circle text-danger me-2"></i>
                                Rifiuta Candidatura
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Stai per rifiutare la candidatura di <strong>{{ $application->user->name }}</strong>.</p>
                            <div class="mb-3">
                                <label for="reject_message{{ $application->id }}" class="form-label">Messaggio per il candidato <span class="text-danger">*</span></label>
                                <textarea class="form-control" 
                                          id="reject_message{{ $application->id }}" 
                                          name="admin_message" 
                                          rows="3" 
                                          required
                                          placeholder="Spiega i motivi del rifiuto..."></textarea>
                                <div class="form-text">È importante spiegare i motivi del rifiuto per aiutare il candidato a migliorare.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-lg me-1"></i> Rifiuta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($application->status !== 'pending')
        <!-- Modal per modifica stato -->
        <div class="modal fade" id="updateModal{{ $application->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.applications.update-status', $application) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-pencil me-2"></i>
                                Modifica Stato Candidatura
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Modifica lo stato della candidatura di <strong>{{ $application->user->name }}</strong>.</p>
                            <div class="mb-3">
                                <label for="status{{ $application->id }}" class="form-label">Stato</label>
                                <select class="form-select" id="status{{ $application->id }}" name="status" required>
                                    <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>In Attesa</option>
                                    @php
                                        $limitReached = $stats['approved'] >= $project->requested_people && $application->status !== 'approved';
                                    @endphp
                                    <option value="approved" 
                                            {{ $application->status === 'approved' ? 'selected' : '' }}
                                            {{ $limitReached ? 'disabled' : '' }}>
                                        Approvata{{ $limitReached ? ' (Limite raggiunto)' : '' }}
                                    </option>
                                    <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rifiutata</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="update_message{{ $application->id }}" class="form-label">Messaggio per il candidato</label>
                                <textarea class="form-control" 
                                          id="update_message{{ $application->id }}" 
                                          name="admin_message" 
                                          rows="3"
                                          placeholder="Aggiorna il messaggio per il candidato...">{{ $application->admin_message }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Aggiorna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

<style>
.border-left-primary {
    border-left: 4px solid #007bff !important;
}
.border-left-success {
    border-left: 4px solid #28a745 !important;
}
.border-left-danger {
    border-left: 4px solid #dc3545 !important;
}
.border-left-info {
    border-left: 4px solid #17a2b8 !important;
}
.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 14px;
}
</style>
@endsection
