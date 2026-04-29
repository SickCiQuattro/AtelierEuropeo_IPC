@extends('layouts.master')

@section('page_title', 'Dettaglio Candidatura - ' . $application->user->name)

@section('active_candidature', 'active')

@section('body')
    @php
        $categoryMap = [
            'CES' => ['label' => 'CES', 'badge' => 'badge-prog-ces'],
            'SG' => ['label' => 'SG', 'badge' => 'badge-prog-sg'],
            'CF' => ['label' => 'CF', 'badge' => 'badge-prog-cf'],
        ];

        // Configurazione Stati Candidatura
        $statusMap = [
            'pending' => ['label' => 'In Attesa', 'icon' => 'bi-hourglass-split', 'class' => 'admin-kpi-icon-pending'],
            'approved' => ['label' => 'Approvata', 'icon' => 'bi-check-circle', 'class' => 'text-success'],
            'rejected' => ['label' => 'Rifiutata', 'icon' => 'bi-x-circle', 'class' => 'text-danger'],
        ];

        $statusStr = strtolower((string) $application->status);
        $statusConfig = $statusMap[$statusStr] ?? ['label' => ucfirst($statusStr), 'icon' => 'bi-question-circle', 'class' => 'text-secondary'];

        // Configurazione Stati Progetto
        $pStatusMap = [
            'published' => ['label' => 'Pubblicato', 'icon' => 'bi-broadcast', 'class' => 'text-success'],
            'draft' => ['label' => 'Bozza', 'icon' => 'bi-pencil', 'class' => 'text-secondary'],
            'completed' => ['label' => 'Completato', 'icon' => 'bi-archive', 'class' => 'text-dark'],
        ];
        $pStatusStr = strtolower((string) $application->project->status);
        $pStatusConfig = $pStatusMap[$pStatusStr] ?? ['label' => ucfirst($pStatusStr), 'icon' => 'bi-question-circle', 'class' => 'text-secondary'];

        $tag = $application->project->category->tag ?? null;
        $categoryConfig = $categoryMap[$tag] ?? ['label' => $tag ?: 'N/D', 'badge' => 'badge-prog-ces'];

        // Flag cruciale per il read-only
        $isProjectCompleted = $pStatusStr === 'completed';
    @endphp

    <div class="container py-5">

        {{-- ── NAVIGAZIONE ──────── --}}
        <div class="d-md-none mb-3">
            <a href="{{ route('admin.applications.index', $application->project) }}"
                class="btn btn-ae btn-ae-light border shadow-sm rounded-pill px-3 py-2 text-secondary fw-semibold transition-hover text-decoration-none">
                <i class="bi bi-arrow-left me-2"></i>Indietro
            </a>
        </div>

        <div class="d-none d-md-block mb-4">
            <x-breadcrumb>
                <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Progetti</a></li>
                <li class="breadcrumb-item">
                    <a
                        href="{{ route('project.show', ['project' => $application->project, 'adminContext' => 1]) }}">{{ Str::limit($application->project->title, 35) }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.applications.index', $application->project) }}">Candidature</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $application->user->name }}</li>
            </x-breadcrumb>
        </div>

        {{-- ── BANNER READ-ONLY (Se progetto completato) ──────── --}}
        @if($isProjectCompleted)
            <div class="alert alert-secondary d-flex align-items-center gap-3 border-0 shadow-sm rounded-4 mb-4 p-4"
                role="alert">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                    style="width: 48px; height: 48px;">
                    <i class="bi bi-archive-fill text-secondary fs-4"></i>
                </div>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Candidatura in sola lettura</h5>
                    <p class="mb-0 small">Il progetto associato è stato contrassegnato come <strong>Completato</strong>. L'esito
                        di questa candidatura è archiviato e non può più essere modificato.</p>
                </div>
            </div>
        @endif

        {{-- ── HEADER (Ripulito dal badge ridondante) ──────────── --}}
        <div class="row align-items-center g-3 mb-4">
            <div class="col-12">
                <h1 class="display-6 fw-bold text-dark mb-1">Candidatura di {{ $application->user->name }}</h1>
                <p class="text-muted mb-0">
                    <i class="bi bi-calendar3 me-1"></i>
                    Inviata il {{ $application->created_at->format('d/m/Y \a\l\l\e H:i') }}
                </p>
            </div>
        </div>

        <div class="row g-4">

            {{-- ── COLONNA SINISTRA: COSA VALUTO (Dati, CV, Progetto) ───── --}}
            <div class="col-lg-7 col-xl-8">

                {{-- Informazioni candidato --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-light border-bottom py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-person me-2 text-primary"></i>Informazioni Candidato
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <div class="text-muted small fw-semibold mb-1 tracking-wide">Nome Completo</div>
                            <div class="text-dark fw-medium fs-5">{{ $application->user->name }}</div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="text-muted small fw-semibold mb-1 tracking-wide">Email</div>
                                <a href="mailto:{{ $application->user->email }}"
                                    class="text-decoration-none text-primary fw-medium">{{ $application->user->email }}</a>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small fw-semibold mb-1 tracking-wide">Telefono</div>
                                @if($application->phone)
                                    <a href="tel:{{ $application->phone }}"
                                        class="text-decoration-none text-dark fw-medium">{{ $application->phone }}</a>
                                @else
                                    <span class="text-muted small">Non fornito</span>
                                @endif
                            </div>
                        </div>

                        {{-- Curriculum Vitae --}}
                        @if($application->document_path)
                            <a href="{{ asset('storage/' . $application->document_path) }}" target="_blank" rel="noopener"
                                download class="d-flex align-items-center gap-3 px-4 py-3 text-decoration-none rounded-3"
                                style="background:#f8fafc; border: 1px solid #e2e8f0; transition:background .15s;"
                                onmouseover="this.style.background='#eaf0ff'" onmouseout="this.style.background='#f8fafc'">
                                <i class="bi bi-file-earmark-pdf text-danger fs-3 flex-shrink-0"></i>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold text-dark">{{ $application->document_name ?? 'Curriculum Vitae' }}</div>
                                    <small class="text-body-secondary">Fai clic per visualizzare o scaricare il
                                        documento</small>
                                </div>
                                <i class="bi bi-download text-primary fs-5 flex-shrink-0"></i>
                            </a>
                        @else
                            <div>
                                <div class="text-muted small fw-semibold mb-1 tracking-wide">Curriculum Vitae
                                </div>
                                <span class="text-muted small"><i class="bi bi-x-circle me-1"></i>Nessun documento allegato dal
                                    candidato.</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Progetto associato --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-light border-bottom py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-folder2-open me-2 text-primary"></i>Progetto Richiesto
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-primary mb-3">
                            {{ $application->project->title }}
                        </h5>

                        <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                            @if($application->project->category)
                                <span class="d-inline-block position-relative z-3" tabindex="0" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Info sul programma {{ $application->project->category->name }}">
                                    <button type="button"
                                        class="{{ $categoryConfig['badge'] }} border-0 shadow-sm px-3 py-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#infoModal-{{ $categoryConfig['label'] }}"
                                        style="font-size: 0.9rem; cursor: pointer;">
                                        {{ $categoryConfig['label'] }} <i class="bi bi-info-circle ms-1"></i>
                                    </button>
                                </span>
                            @endif

                            <span class="badge rounded-pill bg-light border px-3 py-2 shadow-sm {{ $pStatusConfig['class'] }}"
                                style="font-size:.82rem;">
                                <i class="bi {{ $pStatusConfig['icon'] }} me-1"></i>
                                {{ $pStatusConfig['label'] }}
                            </span>

                            <span class="badge rounded-pill bg-light border px-3 py-2 shadow-sm text-muted"
                                style="font-size:.82rem;">
                                <i class="bi bi-calendar-event me-1"></i>Scadenza:
                                {{ $application->project->expire_date ? $application->project->expire_date->format('d/m/Y') : 'N/D' }}
                            </span>
                        </div>

                        <p class="text-muted mb-4" style="line-height: 1.6;">
                            {{ $application->project->sum_description }}
                        </p>

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            
                            <a href="{{ route('project.show', ['project' => $application->project, 'adminContext' => 1]) }}"
                                class="btn btn-ae btn-ae-outline-secondary btn-sm btn-ae-square">
                                <i class="bi bi-folder me-1"></i>Progetto
                            </a>
                        </div>

                    </div>
                </div>

            </div>

            {{-- ── COLONNA DESTRA: COME LO VALUTO (Esito, Messaggi, Azioni) --}}
            <div class="col-lg-5 col-xl-4">
                <div class="position-sticky" style="top: 2rem;">

                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header border-bottom py-3 bg-light text-dark">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-clipboard-check me-2 text-primary"></i>Esito e Valutazione
                            </h5>
                        </div>
                        <div class="card-body p-4">

                            {{-- Sezione Stato Corrente (Standardizzata) --}}
                            <div class="mb-4">
                                <div class="text-muted small fw-semibold mb-2 tracking-wide">Stato Attuale
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi {{ $statusConfig['icon'] }} fs-4 {{ $statusConfig['class'] }}"></i>
                                    {{-- Se è In Attesa usiamo text-dark per la leggibilità, altrimenti i colori di
                                    Bootstrap --}}
                                    <span
                                        class="fs-5 fw-bold {{ $application->status === 'pending' ? 'text-dark' : $statusConfig['class'] }}">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </div>
                            </div>

                            {{-- Messaggio Admin (Visibile SOLO se non è in attesa) --}}
                            @if($application->status !== 'pending')
                                <div class="mb-4">
                                    <div class="text-muted small fw-semibold mb-2 tracking-wide">Messaggio
                                        Inviato</div>
                                    @if($application->admin_message)
                                        <div class="p-3 rounded-3 bg-light border">
                                            <p class="mb-0 small text-dark">{!! nl2br(e($application->admin_message)) !!}</p>
                                        </div>
                                        @if($application->status_updated_at)
                                            <div class="text-muted mt-2" style="font-size: 0.75rem;">
                                                Aggiornato il {{ $application->status_updated_at->format('d/m/Y \a\l\l\e H:i') }}
                                                @if($application->updatedByAdmin)<br>da {{ $application->updatedByAdmin->name }}@endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted small fst-italic">Nessun messaggio inserito per il candidato.</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Azioni (Nascoste se il progetto è completato) --}}
                            @if(!$isProjectCompleted)
                                <hr class="mb-4">

                                @if($application->status === 'pending')
                                    <div class="text-muted small fw-semibold mb-3 tracking-wide">Prendi una decisione
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <button type="button" class="btn btn-ae btn-ae-success btn-ae-square py-2 fw-bold shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#approveModal">
                                            <i class="bi bi-check-lg me-2"></i>Approva
                                        </button>
                                        <button type="button"
                                            class="btn btn-ae btn-ae-outline-danger btn-ae-square py-2 fw-semibold"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal">
                                            <i class="bi bi-x-lg me-2"></i>Rifiuta
                                        </button>
                                    </div>
                                @else
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-ae btn-ae-outline-primary btn-ae-square"
                                            data-bs-toggle="modal" data-bs-target="#updateModal">
                                            <i class="bi bi-pencil me-2"></i>Modifica Decisione
                                        </button>
                                    </div>
                                @endif
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODALI VALUTAZIONE (Non renderizzati se il progetto è completato) ══ --}}
    @if(!$isProjectCompleted)

        @if($application->status === 'pending')
            {{-- Modale Approva --}}
            <div class="modal fade" id="approveModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4">
                        <form action="{{ route('admin.applications.approve', $application) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold mb-0 text-success"><i class="bi bi-check-circle me-2"></i>Approva
                                    Candidatura</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body pt-3">
                                <p>Stai per <strong>approvare</strong> la candidatura di {{ $application->user->name }}.</p>

                                <div class="mb-0">
                                    <label for="approve_message" class="form-label fw-medium">Messaggio per il candidato <span
                                            class="text-muted fw-normal">(opzionale ma consigliato)</span></label>
                                    <textarea class="form-control bg-light" id="approve_message" name="admin_message" rows="4"
                                        placeholder="Es: Ciao! La tua candidatura è stata approvata. Ti contatteremo a breve per fissare una call conoscitiva.">La tua candidatura è stata approvata! Ti contatteremo presto per i prossimi passi.</textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none fw-medium"
                                    data-bs-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-ae btn-ae-success btn-ae-square px-4">Conferma
                                    Approvazione</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modale Rifiuta --}}
            <div class="modal fade" id="rejectModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4">
                        <form action="{{ route('admin.applications.reject', $application) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold mb-0 text-danger"><i class="bi bi-x-circle me-2"></i>Rifiuta
                                    Candidatura</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body pt-3">
                                <p>Stai per <strong>rifiutare</strong> la candidatura di {{ $application->user->name }}.</p>
                                <div class="mb-0">
                                    <label for="reject_message" class="form-label fw-medium">Motivo del rifiuto <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control bg-light" id="reject_message" name="admin_message" rows="4"
                                        required
                                        placeholder="Es: Ti ringraziamo per l'interesse, ma al momento cerchiamo profili con un livello di inglese più alto..."></textarea>
                                    <div class="form-text">Fornire un feedback costruttivo aiuta il candidato a migliorare per il
                                        futuro.</div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none fw-medium"
                                    data-bs-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-ae btn-ae-danger btn-ae-square px-4">Conferma Rifiuto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if($application->status !== 'pending')
            {{-- Modale Modifica Decisione --}}
            <div class="modal fade" id="updateModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4">
                        <form action="{{ route('admin.applications.update-status', $application) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold mb-0 text-primary"><i class="bi bi-pencil me-2"></i>Modifica
                                    Decisione</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body pt-3">
                                <div class="alert alert-light border shadow-sm mb-4 small">
                                    <i class="bi bi-info-circle-fill text-primary me-1"></i> Stai modificando un esito già
                                    comunicato. Assicurati che il nuovo messaggio sia chiaro.
                                </div>

                                <div class="mb-4">
                                    <label for="status" class="form-label fw-medium">Nuovo Esito <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select bg-light" id="status" name="status" required>
                                        <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>Riporta In
                                            Attesa</option>
                                        <option value="approved" {{ $application->status === 'approved' ? 'selected' : '' }}>Approvata
                                        </option>
                                        <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rifiutata
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-0">
                                    <label for="update_message" class="form-label fw-medium">Aggiorna Messaggio</label>
                                    <textarea class="form-control bg-light" id="update_message" name="admin_message" rows="4"
                                        placeholder="Scrivi qui il nuovo messaggio...">{{ $application->admin_message }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none fw-medium"
                                    data-bs-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-ae btn-ae-primary btn-ae-square px-4">Salva Modifiche</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    @endif

@endsection