@extends('layouts.master')

@section('title', 'AE - Dettagli Candidatura')

@section('body')
    @php
        $categoryBadges = [
            'CES' => 'badge-prog-ces',
            'SG' => 'badge-prog-sg',
            'CF' => 'badge-prog-cf',
        ];

        $statusMap = [
            'pending' => [
                'label' => 'In Attesa',
                'icon' => 'bi-hourglass-split',
                'class' => 'admin-kpi-icon-pending',
                'title' => 'Candidatura in fase di valutazione',
                'desc' => 'La tua candidatura è stata ricevuta correttamente.'
            ],
            'approved' => [
                'label' => 'Approvata',
                'icon' => 'bi-check-circle',
                'class' => 'text-success',
                'title' => 'Congratulazioni! Candidatura approvata',
                'desc' => 'Sarai contattato presto per i prossimi passi.'
            ],
            'rejected' => [
                'label' => 'Rifiutata',
                'icon' => 'bi-x-circle',
                'class' => 'text-danger',
                'title' => 'Candidatura non selezionata',
                'desc' => 'Ti invitiamo a provare con altri progetti.'
            ],
        ];

        $statusStr = strtolower((string) $application->status);
        $statusConfig = $statusMap[$statusStr] ?? [
            'label' => ucfirst($statusStr),
            'icon' => 'bi-question-circle',
            'class' => 'bg-secondary-subtle text-secondary-emphasis',
            'title' => 'Stato sconosciuto',
            'desc' => 'Impossibile determinare lo stato.'
        ];

        $tag = $application->project->category->tag ?? null;
        $categoryBadgeClass = $tag ? ($categoryBadges[$tag] ?? 'badge-prog-ces') : 'badge-prog-ces';
        $categoryModalTag = ($tag && array_key_exists($tag, $categoryBadges)) ? $tag : 'CES';
        $categoryName = $application->project->category->name ?? 'programma selezionato';
        $programTooltip = 'Info sul programma ' . $categoryName;
    @endphp

    <div class="container px-3 px-md-4 py-4">

        {{-- ── NAVIGAZIONE ──────── --}}
        <div class="d-md-none mb-3">
            <a href="{{ route('applications.index') }}"
                class="btn btn-ae btn-ae-light border shadow-sm rounded-pill px-3 py-2 text-secondary fw-semibold transition-hover text-decoration-none">
                <i class="bi bi-arrow-left me-2"></i>Indietro
            </a>
        </div>

        <div class="d-none d-md-block mb-4">
            <x-breadcrumb>
                <li class="breadcrumb-item">
                    <a href="{{ route('applications.index') }}">Le Mie Candidature</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ Str::limit($application->project->title, 40) }}
                </li>
            </x-breadcrumb>
        </div>

        {{-- ── HEADER ──────────── --}}
        <div class="row align-items-center g-3 mb-4">
            <div class="col-lg">
                <h1 class="display-6 fw-bold text-dark mb-1">Dettagli Candidatura</h1>
                <p class="text-muted mb-0">
                    <i class="bi bi-calendar3 me-1"></i>
                    Inviata il {{ $application->created_at->format('d/m/Y \a\l\l\e H:i') }}
                </p>
            </div>
            <div class="col-lg-auto">
                <span
                    class="badge rounded-pill px-4 py-2 {{ $statusConfig['class'] }} d-inline-flex align-items-center gap-2 shadow-sm"
                    style="font-size: .95rem; font-weight: 600;">
                    <i class="bi {{ $statusConfig['icon'] }}"></i>
                    {{ $statusConfig['label'] }}
                </span>
            </div>
        </div>

        <div class="row g-4">

            {{-- ── COLONNA SINISTRA: PROGETTO E STATO ──────────── --}}
            <div class="col-lg-8">

                {{-- PROGETTO --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-light border-bottom py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-folder2-open me-2 text-primary"></i>
                            Progetto Richiesto
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-primary mb-3">
                            {{ $application->project->title }}
                        </h5>

                        <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                            @if($tag)
                                <span class="d-inline-block position-relative z-3" tabindex="0" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="{{ $programTooltip }}">
                                    <button type="button" class="{{ $categoryBadgeClass }} border-0 shadow-sm px-3 py-1"
                                        data-bs-toggle="modal" data-bs-target="#infoModal-{{ $categoryModalTag }}"
                                        style="font-size: 0.9rem;">
                                        {{ $tag }} <i class="bi bi-info-circle ms-1"></i>
                                    </button>
                                </span>
                            @endif
                        </div>

                        <p class="text-muted mb-4" style="line-height: 1.6;">
                            {{ $application->project->sum_description }}
                        </p>

                        <a href="{{ route('project.show', $application->project->id) }}"
                            class="btn btn-ae btn-ae-outline-secondary btn-sm btn-ae-square">
                            <i class="bi bi-folder me-1"></i>Progetto
                        </a>
                    </div>
                </div>

                {{-- STATO --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-light border-bottom py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-info-circle me-2 text-primary"></i>
                            Esito e Valutazione
                        </h5>
                    </div>
                    <div class="card-body p-4">

                        {{-- Box di Stato (Soft UI) --}}
                        <div class="d-flex gap-3 align-items-start p-4 rounded-4 mb-4 {{ $statusConfig['class'] }}"
                            style="background-opacity: 0.5;">
                            <i class="bi {{ $statusConfig['icon'] }} fs-2 flex-shrink-0"></i>
                            <div>
                                <h5 class="fw-bold mb-1">{{ $statusConfig['title'] }}</h5>
                                <p class="mb-0 opacity-75">
                                    {{ $statusConfig['desc'] }}
                                </p>
                            </div>
                        </div>

                        {{-- Messaggio organizzazione (Design coerente con la lista) --}}
                        @if($application->admin_message)
                            <div class="p-4 rounded-4 bg-light border">
                                <p class="small fw-bold mb-2 text-dark">
                                    <i class="bi bi-chat-left-text text-primary me-1"></i>
                                    Messaggio dall'organizzazione:
                                </p>
                                <p class="small text-secondary mb-0" style="line-height: 1.6;">
                                    {!! nl2br(e($application->admin_message)) !!}
                                </p>
                            </div>
                        @endif

                    </div>
                </div>

            </div>

            {{-- ── COLONNA DESTRA: LE MIE INFORMAZIONI ──────────── --}}
            <div class="col-lg-4">
                <div class="position-sticky" style="top: 2rem;">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-light border-bottom py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-person-lines-fill me-2 text-primary"></i>
                                Informazioni Personali
                            </h5>
                        </div>
                        <div class="card-body p-4">

                            <div class="mb-4">
                                <div class="text-muted small fw-semibold mb-1 tracking-wide">Nome Completo
                                </div>
                                <div class="text-dark fw-medium fs-6">{{ $application->user->name }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="text-muted small fw-semibold mb-1 tracking-wide">Email</div>
                                <div class="text-dark">{{ $application->user->email }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="text-muted small fw-semibold mb-1 tracking-wide">Telefono</div>
                                <div class="text-dark">{{ $application->phone ?? 'Non fornito' }}</div>
                            </div>

                            <div>
                                <div class="text-muted small fw-semibold mb-2 tracking-wide">Curriculum Vitae
                                </div>
                                @if($application->document_path)
                                    <a href="{{ asset('storage/' . $application->document_path) }}" target="_blank"
                                        rel="noopener" download
                                        class="d-flex align-items-center gap-3 px-3 py-3 text-decoration-none rounded-3 border"
                                        style="background:#f8fafc; transition:background .15s;"
                                        onmouseover="this.style.background='#eaf0ff'"
                                        onmouseout="this.style.background='#f8fafc'">
                                        <i class="bi bi-file-earmark-pdf text-danger fs-3 flex-shrink-0"></i>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="fw-bold text-dark small text-truncate">
                                                {{ $application->document_name ?? 'Curriculum Vitae' }}
                                            </div>
                                            <small class="text-body-secondary" style="font-size: 0.75rem;">Scarica PDF</small>
                                        </div>
                                        <i class="bi bi-download text-primary fs-5 flex-shrink-0"></i>
                                    </a>
                                @else
                                    <span class="text-muted small"><i class="bi bi-x-circle me-1"></i>Nessun documento
                                        allegato</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection