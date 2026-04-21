@extends('layouts.master')

@section('title', 'AE - Dettaglio Candidatura')

@section('breadcrumb')
    <div class="bg-light py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('applications.index') }}" class="text-decoration-none">
                            Le Mie Candidature
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $application->project->title }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('body')
@php
    $statusMap = [
        'pending'  => ['text' => 'In Attesa',  'badge' => 'bg-warning text-dark', 'icon' => 'bi-clock-history',    'bg' => 'rgba(253,197,0,.08)',   'border' => 'var(--bs-warning)'],
        'approved' => ['text' => 'Approvata',  'badge' => 'bg-success',            'icon' => 'bi-check-circle-fill','bg' => 'rgba(16,185,129,.08)',  'border' => 'var(--bs-success)'],
        'rejected' => ['text' => 'Rifiutata',  'badge' => 'bg-danger',             'icon' => 'bi-x-circle-fill',   'bg' => 'rgba(239,68,68,.08)',   'border' => 'var(--bs-danger)'],
    ];
    $st = $statusMap[$application->status] ?? ['text' => ucfirst($application->status), 'badge' => 'bg-secondary', 'icon' => 'bi-question-circle', 'bg' => '#f8fafc', 'border' => '#cbd5e1'];

    $categoryBadges = [
        'CES' => 'badge-prog-ces',
        'SG'  => 'badge-prog-sg',
        'CF'  => 'badge-prog-cf',
    ];
    $tag = $application->project->category->tag ?? null;
    $categoryBadgeClass = $tag ? ($categoryBadges[$tag] ?? '') : '';
@endphp

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- ── HEADER ──────────────────────────────────────────── --}}
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="mb-1">Dettaglio Candidatura</h1>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar3 me-1"></i>
                        Inviata il {{ $application->created_at->format('d/m/Y \a\l\l\e H:i') }}
                    </p>
                </div>
                <span class="badge fs-6 px-3 py-2 {{ $st['badge'] }}" style="pointer-events:none; font-weight:600;">
                    <i class="bi {{ $st['icon'] }} me-2"></i>{{ $st['text'] }}
                </span>
            </div>

            <div class="row g-4">

                {{-- ── COLONNA PRINCIPALE ───────────────────────────── --}}
                <div class="col-lg-8">

                    {{-- Progetto --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0">
                                <i class="bi bi-folder2-open me-2 text-primary"></i>
                                Progetto
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-1">
                                {{ $application->project->title }}
                            </h6>
                            <p class="text-muted mb-3">
                                {{ $application->project->description }}
                            </p>
                            @if($application->project->category)
                                <span class="badge mb-3 px-3 py-2 {{ $categoryBadgeClass }}" style="font-size:.85rem;">
                                    {{ $application->project->category->name }}
                                </span>
                            @endif
                            <div>
                                <a href="{{ route('project.show', $application->project->id) }}"
                                   class="btn btn-ae btn-ae-outline-secondary btn-sm">
                                    <i class="bi bi-folder2-open me-1"></i>Vedi progetto
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Stato candidatura --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0">
                                <i class="bi bi-info-circle me-2 text-primary"></i>
                                Stato della Candidatura
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($application->status === 'pending')
                                <div class="d-flex gap-3 align-items-start p-3 rounded-3"
                                     style="background:{{ $st['bg'] }};border-left:4px solid {{ $st['border'] }}">
                                    <i class="bi bi-hourglass-split text-warning fs-4 flex-shrink-0 mt-1"></i>
                                    <div>
                                        <p class="fw-semibold mb-1">Candidatura in fase di valutazione</p>
                                        <p class="text-muted small mb-0">
                                            La tua candidatura è stata ricevuta e verrà valutata dal nostro team.
                                            Ti contatteremo presto per comunicarti l'esito.
                                        </p>
                                    </div>
                                </div>
                            @elseif($application->status === 'approved')
                                <div class="d-flex gap-3 align-items-start p-3 rounded-3"
                                     style="background:{{ $st['bg'] }};border-left:4px solid {{ $st['border'] }}">
                                    <i class="bi bi-check-circle-fill text-success fs-4 flex-shrink-0 mt-1"></i>
                                    <div>
                                        <p class="fw-semibold mb-1">Congratulazioni! Candidatura approvata</p>
                                        <p class="text-muted small mb-0">
                                            La tua candidatura è stata approvata. Sarai contattato presto
                                            dal nostro team per i prossimi passi.
                                        </p>
                                    </div>
                                </div>
                            @elseif($application->status === 'rejected')
                                <div class="d-flex gap-3 align-items-start p-3 rounded-3"
                                     style="background:{{ $st['bg'] }};border-left:4px solid {{ $st['border'] }}">
                                    <i class="bi bi-x-circle-fill text-danger fs-4 flex-shrink-0 mt-1"></i>
                                    <div>
                                        <p class="fw-semibold mb-1">Candidatura non selezionata</p>
                                        <p class="text-muted small mb-0">
                                            Purtroppo la tua candidatura non è stata selezionata per questo progetto.
                                            Ti incoraggiamo a candidarti per altri progetti disponibili.
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- Messaggio dell'organizzazione --}}
                            @if($application->admin_message)
                                <hr class="my-3">
                                <p class="small fw-semibold mb-2 text-primary">
                                    <i class="bi bi-chat-left-text me-1"></i>Messaggio dall'organizzazione:
                                </p>
                                <div class="p-3 rounded-3"
                                     style="background:#f8fafc;border-left:4px solid var(--bs-primary)">
                                    <p class="small text-muted mb-0">
                                        {{ $application->admin_message }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- ── SIDEBAR ──────────────────────────────────────── --}}
                <div class="col-lg-4">

                    {{-- Le tue informazioni --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0">
                                <i class="bi bi-person me-2 text-primary"></i>
                                Le tue informazioni
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted small text-uppercase"
                                       style="letter-spacing:.04em">Nome</label>
                                <p class="mb-0 fw-semibold">{{ $application->user->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted small text-uppercase"
                                       style="letter-spacing:.04em">Email</label>
                                <p class="mb-0">{{ $application->user->email }}</p>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-medium text-muted small text-uppercase"
                                       style="letter-spacing:.04em">Telefono</label>
                                <p class="mb-0">{{ $application->phone }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Documento allegato --}}
                    @if($application->document_path)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0">
                                    <i class="bi bi-file-earmark-pdf me-2 text-primary"></i>
                                    Documento allegato
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3"
                                     style="background:#f8fafc;border:1px dashed #cbd5e1">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-2 flex-shrink-0"></i>
                                    <div class="flex-grow-1 min-w-0">
                                        <p class="fw-semibold mb-0 text-truncate">
                                            {{ $application->document_name }}
                                        </p>
                                        <small class="text-muted">Documento PDF</small>
                                    </div>
                                    <a href="{{ asset('storage/' . $application->document_path) }}"
                                       target="_blank" rel="noopener"
                                       class="btn btn-ae btn-ae-outline-primary btn-sm flex-shrink-0">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Azioni --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-grid gap-2">
                            <a href="{{ route('applications.index') }}"
                               class="btn btn-ae btn-ae-outline-primary">
                                <i class="bi bi-arrow-left me-2"></i>Le Mie Candidature
                            </a>

                            {{-- Ritira candidatura (solo se in attesa) — P. 61 --}}
                            @if($application->status === 'pending')
                                <button type="button"
                                        class="btn btn-ae btn-ae-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#withdrawModal">
                                    <i class="bi bi-x-lg me-2"></i>Ritira candidatura
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modale ritiro --}}
@if($application->status === 'pending')
    <div class="modal fade" id="withdrawModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('applications.withdraw', $application) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:40px;height:40px">
                                <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                            </div>
                            <h5 class="modal-title mb-0">Ritira Candidatura</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <p class="mb-1">
                            Stai per ritirare la candidatura per
                            <strong>{{ $application->project->title }}</strong>.
                        </p>
                        <p class="text-muted small mb-0">
                            Questa azione è irreversibile. Potrai candidarti nuovamente
                            se il progetto è ancora aperto.
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-ae btn-ae-secondary"
                                data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-ae btn-ae-danger">
                            <i class="bi bi-x-lg me-1"></i>Ritira candidatura
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection