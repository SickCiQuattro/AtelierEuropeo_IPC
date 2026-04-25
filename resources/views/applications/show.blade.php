@extends('layouts.master')

@section('title', 'AE - Dettaglio Candidatura')

@section('breadcrumb')
<div class="bg-light py-2">
    <div class="container">
        <x-breadcrumb>
            <li class="breadcrumb-item">
                <a href="{{ route('applications.index') }}" class="text-decoration-none">
                    Le Mie Candidature
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $application->project->title }}
            </li>
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

    /* ── BADGE PROGETTO ───────────────────── */
    $categoryBadges = [
        'CES' => 'badge-prog-ces',
        'SG'  => 'badge-prog-sg',
        'CF'  => 'badge-prog-cf',
    ];

    $tag = $application->project->category->tag ?? null;
    $categoryBadgeClass = $tag ? ($categoryBadges[$tag] ?? '') : '';
    $categoryName = $application->project->category->name ?? 'programma selezionato';

    $categoryModalTag = $tag && array_key_exists($tag, $categoryBadges) ? $tag : 'CES';
    $programTooltip = 'Info sul programma ' . $categoryName;
@endphp

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- HEADER --}}
            <div class="mb-4">
                <h1 class="mb-1">Dettaglio Candidatura</h1>
                <p class="text-muted mb-0">
                    <i class="bi bi-calendar3 me-1"></i>
                    Inviata il {{ $application->created_at->format('d/m/Y \a\l\l\e H:i') }}
                </p>
            </div>

            <div class="row g-4">

                <div class="col-lg-8">

                    {{-- PROGETTO --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-light border-bottom py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-folder2-open me-2 text-primary"></i>
                                Progetto
                            </h5>
                        </div>

                        <div class="card-body">

                            {{-- TITOLO + BADGE --}}
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">

                                <h6 class="fw-bold text-primary mb-0">
                                    {{ $application->project->title }}
                                </h6>

                                @if($tag && $categoryBadgeClass)
                                    <span class="d-inline-block position-relative z-3" tabindex="0"
                                          data-bs-toggle="tooltip" data-bs-placement="top"
                                          title="{{ $programTooltip }}">
                                        <button type="button"
                                                class="{{ $categoryBadgeClass }} border-0 shadow-sm px-3 py-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#infoModal-{{ $categoryModalTag }}"
                                                style="font-size: 0.9rem;">
                                            {{ $tag }} <i class="bi bi-info-circle ms-1"></i>
                                        </button>
                                    </span>
                                @endif

                            </div>

                            <p class="text-muted mb-0">
                                {{ $application->project->sum_description }}
                            </p>

                        </div>
                    </div>

                    {{-- STATO --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-light border-bottom py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-info-circle me-2 text-primary"></i>
                                Stato della Candidatura
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-3 align-items-start p-3 rounded-3"
                                 style="background:{{ $stBg }};border-left:4px solid {{ $stBorder }}">
                                <i class="bi {{ $stIcon }} fs-4 flex-shrink-0 mt-1"
                                   style="color:{{ $stColor }}"></i>
                                <div>
                                    @if($application->status === 'pending')
                                        <p class="fw-semibold mb-1">Candidatura in fase di valutazione</p>
                                        <p class="text-muted small mb-0">
                                            La tua candidatura è stata ricevuta.
                                        </p>
                                    @elseif($application->status === 'approved')
                                        <p class="fw-semibold mb-1">Congratulazioni! Candidatura approvata</p>
                                        <p class="text-muted small mb-0">Sarai contattato presto.</p>
                                    @elseif($application->status === 'rejected')
                                        <p class="fw-semibold mb-1">Candidatura non selezionata</p>
                                        <p class="text-muted small mb-0">Prova altri progetti.</p>
                                    @endif
                                </div>
                            </div>

                            @if($application->admin_message)
                                <hr class="my-3">
                                <p class="small fw-semibold mb-2 text-primary">
                                    <i class="bi bi-chat-left-text me-1"></i>
                                    Messaggio dall'organizzazione:
                                </p>
                                <div class="p-3 rounded-3 bg-light border-start border-4 border-primary">
                                    <p class="small text-muted mb-0">
                                        {{ $application->admin_message }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- SIDEBAR --}}
                <div class="col-lg-4">

                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-light border-bottom py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-person me-2 text-primary"></i>
                                Le tue informazioni
                            </h5>
                        </div>
                        <div class="card-body p-0">

                            <div class="px-4 py-3 border-bottom">
                                <div class="admin-mobile-kicker mb-1">Nome Completo</div>
                                <div>{{ $application->user->name }}</div>
                            </div>

                            <div class="px-4 py-3 border-bottom">
                                <div class="admin-mobile-kicker mb-1">Email</div>
                                <div>{{ $application->user->email }}</div>
                            </div>

                            <div class="px-4 py-3 border-bottom">
                                <div class="admin-mobile-kicker mb-1">Telefono</div>
                                <div>{{ $application->phone }}</div>
                            </div>

                            @if($application->document_path)
                                <a href="{{ asset('storage/' . $application->document_path) }}"
                                   target="_blank"
                                   class="d-flex align-items-center gap-3 px-4 py-3 text-decoration-none bg-light">
                                    <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-dark small">
                                            {{ $application->document_name ?? 'Curriculum Vitae' }}
                                        </div>
                                        <small class="text-muted">Scarica il documento PDF</small>
                                    </div>
                                    <i class="bi bi-file-earmark-arrow-down text-primary fs-5"></i>
                                </a>
                            @else
                                <div class="px-4 py-3">
                                    <div class="admin-mobile-kicker mb-1">Curriculum Vitae</div>
                                    <span class="text-muted small">
                                        Nessun documento allegato
                                    </span>
                                </div>
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection