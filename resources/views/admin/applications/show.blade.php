@extends('layouts.master')

@section('page_title', 'Dettaglio Candidatura - ' . $application->user->name)

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
                        <a href="{{ route('project.show', $application->project) }}"
                            class="text-decoration-none">{{ $application->project->title }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.applications.index', $application->project) }}"
                            class="text-decoration-none">Candidature</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $application->user->name }}</li>
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
                    Dettaglio Candidatura
                </h1>
            </div>
        </div>

        <div class="row">
            <!-- Informazioni Candidato -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-person me-2"></i>
                            Informazioni Candidato
                        </h5>
                        @php
                            $statusClasses = [
                                'pending' => 'bg-warning text-dark',
                                'approved' => 'bg-success',
                                'rejected' => 'bg-danger'
                            ];
                            $statusIcons = [
                                'pending' => 'bi-clock-history',
                                'approved' => 'bi-check-circle',
                                'rejected' => 'bi-x-circle'
                            ];
                            $statusTexts = [
                                'pending' => 'In Attesa',
                                'approved' => 'Approvata',
                                'rejected' => 'Rifiutata'
                            ];
                        @endphp
                        <span class="badge {{ $statusClasses[$application->status] }} fs-6">
                            <i class="bi {{ $statusIcons[$application->status] }} me-1"></i>
                            {{ $statusTexts[$application->status] }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Nome Completo</label>
                                    <p class="form-control-plaintext">{{ $application->user->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Email</label>
                                    <p class="form-control-plaintext">
                                        <a href="mailto:{{ $application->user->email }}" class="text-decoration-none">
                                            <i class="bi bi-envelope me-1"></i>
                                            {{ $application->user->email }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Telefono</label>
                                    <p class="form-control-plaintext">
                                        @if($application->phone)
                                            <a href="tel:{{ $application->phone }}" class="text-decoration-none">
                                                <i class="bi bi-telephone me-1"></i>
                                                {{ $application->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted">Non fornito</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Data Candidatura</label>
                                    <p class="form-control-plaintext">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $application->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($application->document_path)
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Documento Allegato</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-pdf text-danger me-2 fs-4"></i>
                                    <div>
                                        <a href="{{ asset('storage/' . $application->document_path) }}" target="_blank"
                                            class="btn btn-ae btn-ae-outline-primary btn-sm">
                                            <i class="bi bi-download me-1"></i>
                                            Visualizza/Scarica CV
                                        </a>
                                        <small class="d-block text-muted mt-1">
                                            File PDF - Caricato il {{ $application->created_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informazioni Progetto -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-folder-open me-2"></i>
                            Progetto di Candidatura
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">{{ $application->project->title }}</h6>
                        <p class="card-text text-muted">{{ Str::limit($application->project->description, 200) }}</p>

                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    Categoria: {{ $application->project->category->name }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    Scadenza:
                                    {{ $application->project->expire_date ? $application->project->expire_date->format('d/m/Y') : 'Non specificata' }}
                                </small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('project.show', $application->project) }}"
                                class="btn btn-ae btn-ae-outline-secondary btn-sm">
                                <i class="bi bi-eye me-1"></i>
                                Visualizza Progetto
                            </a>
                        </div>
                    </div>
                </div>

                @if($application->admin_message)
                    <!-- Messaggio Admin -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-chat-square-text me-2"></i>
                                Messaggio dell'Amministratore
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="border-start border-primary border-4 ps-3">
                                {!! nl2br(e($application->admin_message)) !!}
                            </div>
                            @if($application->status_updated_at)
                                <small class="text-muted mt-2 d-block">
                                    <i class="bi bi-clock me-1"></i>
                                    Aggiornato il {{ $application->status_updated_at->format('d/m/Y H:i') }}
                                    @if($application->updatedByAdmin)
                                        da {{ $application->updatedByAdmin->name }}
                                    @endif
                                </small>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Azioni -->
            <div class="col-md-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-gear me-2"></i>
                            Azioni Amministratore
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($application->status === 'pending')
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-ae btn-ae-success" data-bs-toggle="modal"
                                    data-bs-target="#approveModal">
                                    <i class="bi bi-check-lg me-2"></i>
                                    Approva Candidatura
                                </button>
                                <button type="button" class="btn btn-ae btn-ae-danger" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    <i class="bi bi-x-lg me-2"></i>
                                    Rifiuta Candidatura
                                </button>
                            </div>
                        @else
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-ae btn-ae-primary" data-bs-toggle="modal"
                                    data-bs-target="#updateModal">
                                    <i class="bi bi-pencil me-2"></i>
                                    Modifica Stato
                                </button>
                            </div>
                        @endif

                        <hr>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.applications.index', $application->project) }}"
                                class="btn btn-ae btn-ae-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Torna alle Candidature
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal per approvazione -->
    @if($application->status === 'pending')
        <div class="modal fade" id="approveModal" tabindex="-1">
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
                            <div class="mb-3">
                                <label for="admin_message" class="form-label">Messaggio per il candidato (opzionale)</label>
                                <textarea class="form-control" id="admin_message" name="admin_message" rows="3"
                                    placeholder="Inserisci un messaggio personalizzato per il candidato...">La tua candidatura è stata approvata! Ti contatteremo presto per i prossimi passi.</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-ae btn-ae-secondary" data-bs-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-ae btn-ae-success">
                                <i class="bi bi-check-lg me-1"></i> Approva
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal per rifiuto -->
        <div class="modal fade" id="rejectModal" tabindex="-1">
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
                                <label for="reject_message" class="form-label">Messaggio per il candidato <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="reject_message" name="admin_message" rows="3" required
                                    placeholder="Spiega i motivi del rifiuto..."></textarea>
                                <div class="form-text">È importante spiegare i motivi del rifiuto per aiutare il candidato a
                                    migliorare.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-ae btn-ae-secondary" data-bs-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-ae btn-ae-danger">
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
        <div class="modal fade" id="updateModal" tabindex="-1">
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
                                <label for="status" class="form-label">Stato</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>In Attesa
                                    </option>
                                    <option value="approved" {{ $application->status === 'approved' ? 'selected' : '' }}>Approvata
                                    </option>
                                    <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rifiutata
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="update_message" class="form-label">Messaggio per il candidato</label>
                                <textarea class="form-control" id="update_message" name="admin_message" rows="3"
                                    placeholder="Aggiorna il messaggio per il candidato...">{{ $application->admin_message }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-ae btn-ae-secondary" data-bs-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-ae btn-ae-primary">
                                <i class="bi bi-check-lg me-1"></i> Aggiorna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection