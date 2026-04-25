@extends('layouts.master')

@section('page_title', 'Dettaglio Candidatura - ' . $application->user->name)

@section('breadcrumb')
<div class="bg-light py-2">
    <div class="container">
        <x-breadcrumb>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.projects.index') }}" class="text-decoration-none">Progetti</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('project.show', ['project' => $application->project, 'adminContext' => 1]) }}"
                    class="text-decoration-none">{{ Str::limit($application->project->title, 35) }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.applications.index', $application->project) }}"
                    class="text-decoration-none">Candidature</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $application->user->name }}</li>
        </x-breadcrumb>
    </div>
</div>
@endsection

@section('body')
@php
    $statusColors = [
        'pending'  => ['#f59e0b', 'bi-clock-history',     'In Attesa',  'rgba(253,197,0,.08)',  '#f59e0b'],
        'approved' => ['#10b981', 'bi-check-circle-fill', 'Approvata',  'rgba(16,185,129,.08)', '#10b981'],
        'rejected' => ['#ef4444', 'bi-x-circle-fill',     'Rifiutata',  'rgba(239,68,68,.08)',  '#ef4444'],
    ];
    [$stColor, $stIcon, $stLabel, $stBg, $stBorder] = $statusColors[$application->status]
        ?? ['#6b7280', 'bi-question-circle', ucfirst($application->status), '#f8fafc', '#6b7280'];

    $categoryBadges = ['CES' => 'badge-prog-ces', 'SG' => 'badge-prog-sg', 'CF' => 'badge-prog-cf'];
    $tag = $application->project->category->tag ?? null;
    $categoryBadgeClass = $tag ? ($categoryBadges[$tag] ?? '') : '';
    $categoryModalTag = ($tag && array_key_exists($tag, $categoryBadges)) ? $tag : 'CES';
@endphp

<div class="container mt-4 mb-5">

    {{-- ── HEADER ──────────────────────────────────────────────── --}}
    <div class="row align-items-center g-3 mb-4">
        <div class="col-lg">
            <h1 class="display-6 fw-bold text-dark mb-1">Dettaglio Candidatura</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-calendar3 me-1"></i>
                Ricevuta il {{ $application->created_at->format('d/m/Y \a\l\l\e H:i') }}
            </p>
        </div>
        <div class="col-lg-auto">
            <span class="rounded-pill px-4 py-2 text-white d-inline-flex align-items-center gap-2 fw-semibold"
                  style="background-color:{{ $stColor }};font-size:.9rem;">
                <i class="bi {{ $stIcon }}"></i>{{ $stLabel }}
            </span>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── COLONNA PRINCIPALE ────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Informazioni candidato --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-light border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person me-2 text-primary"></i>Informazioni Candidato
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="px-4 py-3 border-bottom">
                        <div class="admin-mobile-kicker mb-1">Nome Completo</div>
                        <div class="text-dark">{{ $application->user->name }}</div>
                    </div>
                    <div class="px-4 py-3 border-bottom">
                        <div class="admin-mobile-kicker mb-1">Email</div>
                        <a href="mailto:{{ $application->user->email }}" class="text-decoration-none text-dark">{{ $application->user->email }}</a>
                    </div>
                    <div class="px-4 py-3 border-bottom">
                        <div class="admin-mobile-kicker mb-1">Telefono</div>
                        @if($application->phone)
                            <a href="tel:{{ $application->phone }}" class="text-decoration-none text-dark">{{ $application->phone }}</a>
                        @else
                            <span class="text-muted">Non fornito</span>
                        @endif
                    </div>
                    {{-- Curriculum Vitae --}}
                    @if($application->document_path)
                        <a href="{{ asset('storage/' . $application->document_path) }}"
                           target="_blank" rel="noopener" download
                           class="d-flex align-items-center gap-3 px-4 py-3 text-decoration-none"
                           style="background:#f8fafc;transition:background .15s;"
                           onmouseover="this.style.background='#eaf0ff'" onmouseout="this.style.background='#f8fafc'">
                            <i class="bi bi-file-earmark-pdf text-danger fs-4 flex-shrink-0"></i>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-dark small">{{ $application->document_name ?? 'Curriculum Vitae' }}</div>
                                <small class="text-body-secondary">Scarica il documento PDF</small>
                            </div>
                            <i class="bi bi-file-earmark-arrow-down text-primary fs-5 flex-shrink-0"></i>
                        </a>
                    @else
                        <div class="px-4 py-3">
                            <div class="admin-mobile-kicker mb-1">Curriculum Vitae</div>
                            <span class="text-muted small">Nessun documento allegato</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Progetto --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-light border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-folder-open me-2 text-primary"></i>Progetto di Candidatura
                    </h5>
                </div>
                <div class="card-body">

                    {{-- Titolo --}}
                    <h6 class="fw-bold text-primary mb-3">{{ $application->project->title }}</h6>

                    {{-- Badge categoria + scadenza --}}
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                        @if($application->project->category)
                            <button type="button" class="{{ $categoryBadgeClass }} position-relative z-3"
                                    data-bs-toggle="modal" data-bs-target="#infoModal-{{ $categoryModalTag }}">
                                {{ $application->project->category->tag }}
                            </button>
                        @endif
                        @if($application->project->expire_date)
                            <span class="badge rounded-pill bg-light border px-3 py-2 shadow-sm text-muted" style="font-size:.82rem;">
                                <i class="bi bi-calendar-event me-1"></i>Scadenza: {{ $application->project->expire_date->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('project.show', ['project' => $application->project, 'adminContext' => 1]) }}"
                        class="btn btn-ae btn-ae-outline-secondary btn-sm btn-ae-square">
                        <i class="bi bi-eye me-1"></i>Visualizza Progetto
                    </a>
                </div>
            </div>

            {{-- Messaggio Admin --}}
            @if($application->admin_message)
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-light border-bottom py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-chat-square-text me-2 text-primary"></i>Messaggio dell'Amministratore
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="p-3 rounded-3" style="background:#f8fafc;border-left:4px solid var(--bs-primary)">
                            {!! nl2br(e($application->admin_message)) !!}
                        </div>
                        @if($application->status_updated_at)
                            <small class="text-muted mt-2 d-block">
                                <i class="bi bi-clock me-1"></i>
                                Aggiornato il {{ $application->status_updated_at->format('d/m/Y H:i') }}
                                @if($application->updatedByAdmin) da {{ $application->updatedByAdmin->name }} @endif
                            </small>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        {{-- ── SIDEBAR AZIONI ───────────────────────────────────── --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-light border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-sliders me-2 text-primary"></i>Azioni Amministratore
                    </h5>
                </div>
                <div class="card-body">
                    @if($application->status === 'pending')
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-ae btn-ae-success btn-ae-square"
                                    data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-lg me-2"></i>Approva Candidatura
                            </button>
                            <button type="button" class="btn btn-ae btn-ae-danger btn-ae-square"
                                    data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-lg me-2"></i>Rifiuta Candidatura
                            </button>
                        </div>
                    @else
                        <div class="p-3 rounded-3 mb-3" style="background:rgba(253,197,0,.1);border-left:4px solid #f59e0b">
                            <p class="fw-semibold mb-1 small text-primary">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                                Candidatura già valutata
                            </p>
                            <p class="text-muted mb-0 small">
                                Questa candidatura è già stata
                                <strong>{{ $application->status === 'approved' ? 'approvata' : 'rifiutata' }}</strong>.
                                Modificare lo stato potrebbe creare inconsistenze.
                            </p>
                        </div>
                        <div class="d-grid">
                            <button type="button" class="btn btn-ae btn-ae-outline-secondary btn-ae-square"
                                    data-bs-toggle="modal" data-bs-target="#updateModal">
                                <i class="bi bi-pencil me-2"></i>Modifica Stato
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ MODALI ════════════════════════════════════════════════════════ --}}

@if($application->status === 'pending')
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="{{ route('admin.applications.approve', $application) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title mb-0">Approva Candidatura</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <p>Stai per approvare la candidatura di <strong>{{ $application->user->name }}</strong>.</p>
                        <div class="mb-0">
                            <label for="admin_message" class="form-label fw-medium">Messaggio per il candidato <span class="text-muted fw-normal">(opzionale)</span></label>
                            <textarea class="form-control" id="admin_message" name="admin_message" rows="3"
                                placeholder="Messaggio personalizzato…">La tua candidatura è stata approvata! Ti contatteremo presto per i prossimi passi.</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-ae btn-ae-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-ae btn-ae-success">Approva</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="{{ route('admin.applications.reject', $application) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title mb-0">Rifiuta Candidatura</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <p>Stai per rifiutare la candidatura di <strong>{{ $application->user->name }}</strong>.</p>
                        <div class="mb-0">
                            <label for="reject_message" class="form-label fw-medium">Motivo del rifiuto <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reject_message" name="admin_message" rows="3" required placeholder="Spiega i motivi del rifiuto…"></textarea>
                            <div class="form-text">Spiegare i motivi aiuta il candidato a migliorare.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-ae btn-ae-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-ae btn-ae-danger">Rifiuta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@if($application->status !== 'pending')
    <div class="modal fade" id="updateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="{{ route('admin.applications.update-status', $application) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title mb-0">Modifica Stato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <p>Modifica lo stato della candidatura di <strong>{{ $application->user->name }}</strong>.</p>
                        <div class="mb-3">
                            <label for="status" class="form-label fw-medium">Stato <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending"  {{ $application->status === 'pending'  ? 'selected' : '' }}>In Attesa</option>
                                <option value="approved" {{ $application->status === 'approved' ? 'selected' : '' }}>Approvata</option>
                                <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rifiutata</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label for="update_message" class="form-label fw-medium">Messaggio per il candidato</label>
                            <textarea class="form-control" id="update_message" name="admin_message" rows="3"
                                placeholder="Aggiorna il messaggio…">{{ $application->admin_message }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-ae btn-ae-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-ae btn-ae-primary">Aggiorna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<x-category-info-modals />
@endsection