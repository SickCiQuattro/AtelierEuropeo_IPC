@extends('layouts.master')

@section('page_title', 'Dettaglio Candidatura - ' . $application->user->name)

@section('breadcrumb')
    <div class="bg-light py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.projects.index') }}" class="text-decoration-none">Progetti</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('project.show', ['project' => $application->project, 'adminContext' => 1]) }}"
                           class="text-decoration-none">{{ $application->project->title }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.applications.all') }}"
                           class="text-decoration-none">Candidature</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $application->user->name }}</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('body')
@php
    $appStatus = $application->status;

    // Mappa stati → testo italiano (P. 76 – nessun termine inglese)
    $statusMap = [
        'pending'  => ['text' => 'In Attesa',  'badge' => 'bg-warning text-dark', 'icon' => 'bi-clock-history'],
        'approved' => ['text' => 'Approvata',  'badge' => 'bg-success',            'icon' => 'bi-check-circle-fill'],
        'rejected' => ['text' => 'Rifiutata',  'badge' => 'bg-danger',             'icon' => 'bi-x-circle-fill'],
    ];
    $st = $statusMap[$appStatus] ?? ['text' => ucfirst($appStatus), 'badge' => 'bg-secondary', 'icon' => 'bi-question-circle'];

    $limitReached = false; // Sarà calcolato nel controller — per ora placeholder
@endphp

<div class="container mt-4 mb-5">

    {{-- ── HEADER ──────────────────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h1 class="mb-1">Dettaglio Candidatura</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-person me-1"></i>
                <strong>{{ $application->user->name }}</strong>
                &mdash;
                Inviata il {{ $application->created_at->format('d/m/Y \a\l\l\e H:i') }}
            </p>
        </div>
        {{-- Badge stato — solo testo, nessun effetto hover (P. 75 – falsa affordance rimossa) --}}
        <span class="badge fs-6 px-3 py-2 {{ $st['badge'] }}" style="font-weight:600; pointer-events:none;">
            <i class="bi {{ $st['icon'] }} me-2"></i>{{ $st['text'] }}
        </span>
    </div>

    <div class="row g-4">

        {{-- ── COLONNA PRINCIPALE (2/3) ────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Informazioni candidato --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge me-2 text-primary"></i>
                        Informazioni del Candidato
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase"
                                   style="letter-spacing:.04em">Nome completo</label>
                            <p class="form-control-plaintext fw-semibold mb-0">
                                {{ $application->user->name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase"
                                   style="letter-spacing:.04em">Email</label>
                            <p class="form-control-plaintext mb-0">
                                <a href="mailto:{{ $application->user->email }}"
                                   class="text-decoration-none">
                                    <i class="bi bi-envelope me-1"></i>
                                    {{ $application->user->email }}
                                </a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase"
                                   style="letter-spacing:.04em">Telefono</label>
                            <p class="form-control-plaintext mb-0">
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
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase"
                                   style="letter-spacing:.04em">Data candidatura</label>
                            <p class="form-control-plaintext mb-0">
                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                {{ $application->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    {{-- Documento allegato --}}
                    @if($application->document_path)
                        <hr class="my-3">
                        <label class="form-label fw-medium text-muted small text-uppercase"
                               style="letter-spacing:.04em">Documento allegato</label>
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3"
                             style="background:#f8fafc;border:1px dashed #cbd5e1">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-2 flex-shrink-0"></i>
                            <div>
                                <p class="fw-semibold mb-0">{{ $application->document_name ?? 'Documento' }}</p>
                                <small class="text-muted">
                                    Caricato il {{ $application->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                            <a href="{{ asset('storage/' . $application->document_path) }}"
                               target="_blank" rel="noopener"
                               class="btn btn-ae btn-ae-outline-primary btn-sm ms-auto">
                                <i class="bi bi-download me-1"></i> Scarica CV
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Progetto di candidatura --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-folder2-open me-2 text-primary"></i>
                        Progetto di Candidatura
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-1">{{ $application->project->title }}</h6>
                    <p class="text-muted mb-3">
                        {{ Str::limit($application->project->description, 250) }}
                    </p>
                    <div class="d-flex flex-wrap gap-3 mb-3">
                        <span class="text-muted small">
                            <i class="bi bi-tag me-1"></i>
                            <strong>Categoria:</strong> {{ $application->project->category->name }}
                        </span>
                        @if($application->project->expire_date)
                            <span class="text-muted small">
                                <i class="bi bi-calendar-event me-1"></i>
                                <strong>Scadenza:</strong>
                                {{ $application->project->expire_date->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('project.show', ['project' => $application->project, 'adminContext' => 1]) }}"
                       class="btn btn-ae btn-ae-outline-secondary btn-sm">
                        <i class="bi bi-eye me-1"></i> Visualizza pagina progetto
                    </a>
                </div>
            </div>

            {{-- Messaggio amministratore (se presente) --}}
            @if($application->admin_message)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-left-text me-2 text-primary"></i>
                            Messaggio dell'Amministratore
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="p-3 rounded-3"
                             style="background:#f8fafc;border-left:4px solid var(--bs-primary)">
                            <p class="mb-0">{!! nl2br(e($application->admin_message)) !!}</p>
                        </div>
                        @if($application->status_updated_at)
                            <p class="text-muted small mt-2 mb-0">
                                <i class="bi bi-clock me-1"></i>
                                Aggiornato il {{ $application->status_updated_at->format('d/m/Y H:i') }}
                                @if($application->updatedByAdmin)
                                    da <strong>{{ $application->updatedByAdmin->name }}</strong>
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- ── SIDEBAR AZIONI (1/3) ────────────────────────────── --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-sliders me-2 text-primary"></i>
                        Azioni
                    </h5>
                </div>
                <div class="card-body">

                    {{-- Azioni primarie in base allo stato corrente --}}
                    @if($appStatus === 'pending')
                        {{-- Stato: In Attesa → Approva o Rifiuta --}}
                        <div class="d-grid gap-2 mb-3">
                            <button type="button"
                                    class="btn btn-ae btn-ae-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#approveModal">
                                <i class="bi bi-check-lg me-2"></i>Approva Candidatura
                            </button>
                            <button type="button"
                                    class="btn btn-ae btn-ae-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                <i class="bi bi-x-lg me-2"></i>Rifiuta Candidatura
                            </button>
                        </div>

                    @elseif($appStatus === 'approved')
                        {{-- Stato: Approvata --}}
                        <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3">
                            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                            <span class="small">Candidatura approvata.</span>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="button"
                                    class="btn btn-ae btn-ae-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#updateModal">
                                <i class="bi bi-pencil me-2"></i>Modifica Stato
                            </button>
                        </div>

                    @elseif($appStatus === 'rejected')
                        {{-- Stato: Rifiutata --}}
                        <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3">
                            <i class="bi bi-x-circle-fill flex-shrink-0"></i>
                            <span class="small">Candidatura rifiutata.</span>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="button"
                                    class="btn btn-ae btn-ae-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#updateModal">
                                <i class="bi bi-pencil me-2"></i>Modifica Stato
                            </button>
                        </div>
                    @endif

                    <hr class="my-1">

                    <div class="d-grid">
                        <a href="{{ route('admin.applications.all') }}"
                           class="btn btn-ae btn-ae-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Torna alle Candidature
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     MODALI
══════════════════════════════════════════════════════════ --}}

{{-- Modale Approva --}}
@if($appStatus === 'pending')
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.applications.approve', $application) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center"
                                 style="width:40px;height:40px">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            </div>
                            <h5 class="modal-title mb-0">Approva Candidatura</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <p>
                            Stai per approvare la candidatura di
                            <strong>{{ $application->user->name }}</strong>.
                        </p>
                        <div class="mb-0">
                            <label for="admin_message_approve" class="form-label fw-medium">
                                Messaggio per il candidato
                                <span class="text-muted fw-normal">(opzionale)</span>
                            </label>
                            <textarea class="form-control" id="admin_message_approve"
                                      name="admin_message" rows="3"
                                      placeholder="Inserisci un messaggio personalizzato…">La tua candidatura è stata approvata! Ti contatteremo presto per i prossimi passi.</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-ae btn-ae-secondary" data-bs-dismiss="modal">
                            Annulla
                        </button>
                        <button type="submit" class="btn btn-ae btn-ae-success">
                            <i class="bi bi-check-lg me-1"></i> Approva
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modale Rifiuta --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.applications.reject', $application) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center"
                                 style="width:40px;height:40px">
                                <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                            </div>
                            <h5 class="modal-title mb-0">Rifiuta Candidatura</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <p>
                            Stai per rifiutare la candidatura di
                            <strong>{{ $application->user->name }}</strong>.
                        </p>
                        <div class="mb-0">
                            <label for="reject_message" class="form-label fw-medium">
                                Motivo del rifiuto
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="reject_message"
                                      name="admin_message" rows="3" required
                                      placeholder="Spiega i motivi del rifiuto…"></textarea>
                            <div class="form-text">
                                Spiegare i motivi aiuta il candidato a migliorare per le prossime opportunità.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-ae btn-ae-secondary" data-bs-dismiss="modal">
                            Annulla
                        </button>
                        <button type="submit" class="btn btn-ae btn-ae-danger">
                            <i class="bi bi-x-lg me-1"></i> Rifiuta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- Modale Modifica Stato (per candidature già valutate) --}}
@if($appStatus !== 'pending')
    <div class="modal fade" id="updateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.applications.update-status', $application) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                 style="width:40px;height:40px">
                                <i class="bi bi-pencil-square text-primary fs-5"></i>
                            </div>
                            <h5 class="modal-title mb-0">Modifica Stato Candidatura</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <p>
                            Candidatura di <strong>{{ $application->user->name }}</strong>
                        </p>
                        <div class="mb-3">
                            <label for="status" class="form-label fw-medium">
                                Nuovo stato <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="status" name="status" required
                                    onchange="updateMessagePlaceholder(this.value)">
                                <option value="pending"
                                    {{ $appStatus === 'pending' ? 'selected' : '' }}>
                                    In Attesa
                                </option>
                                <option value="approved"
                                    {{ $appStatus === 'approved' ? 'selected' : '' }}>
                                    Approvata
                                </option>
                                <option value="rejected"
                                    {{ $appStatus === 'rejected' ? 'selected' : '' }}>
                                    Rifiutata
                                </option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label for="update_message" class="form-label fw-medium">
                                Messaggio per il candidato
                            </label>
                            <textarea class="form-control" id="update_message"
                                      name="admin_message" rows="3"
                                      placeholder="Scrivi un messaggio per il candidato…"></textarea>
                            <div class="form-text">Il messaggio verrà aggiornato insieme allo stato.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-ae btn-ae-secondary" data-bs-dismiss="modal">
                            Annulla
                        </button>
                        <button type="submit" class="btn btn-ae btn-ae-primary">
                            <i class="bi bi-check-lg me-1"></i> Aggiorna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<style>
    /* Il badge di stato è solo decorativo, non interagisce (P. 75 – falsa affordance rimossa) */
    [style*="pointer-events:none"] { cursor: default !important; }
</style>

<script>
    const defaultMessages = {
        approved: 'La tua candidatura è stata approvata! Ti contatteremo presto per i prossimi passi.',
        rejected: '',
        pending: ''
    };

    const placeholders = {
        approved: 'Messaggio di approvazione per il candidato…',
        rejected: 'Spiega i motivi del rifiuto…',
        pending: 'Scrivi un messaggio per il candidato…'
    };

    function updateMessagePlaceholder(status) {
        const textarea = document.getElementById('update_message');
        if (!textarea) return;
        // Svuota sempre il campo quando si cambia stato
        textarea.value = '';
        textarea.placeholder = placeholders[status] ?? placeholders.pending;
    }

    // Inizializza al momento dell'apertura del modal
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('updateModal');
        if (!modal) return;
        modal.addEventListener('show.bs.modal', () => {
            const select = document.getElementById('status');
            const textarea = document.getElementById('update_message');
            if (!select || !textarea) return;
            // Al primo apertura mostra il messaggio corrente salvato, se esiste
            textarea.value = @json($application->admin_message ?? '');
            textarea.placeholder = placeholders[select.value] ?? placeholders.pending;
        });
    });
</script>
@endsection