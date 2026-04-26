@extends('layouts.master')

@section('page_title', 'Gestione Candidature - ' . $project->title)

@section('active_candidature', 'active')

@section('body')
    @php
        $categoryMap = [
            'CES' => ['label' => 'CES', 'badge' => 'badge-prog-ces'],
            'SG' => ['label' => 'SG', 'badge' => 'badge-prog-sg'],
            'CF' => ['label' => 'CF', 'badge' => 'badge-prog-cf'],
        ];

        // Badge candidature allineati alle euristiche Nielsen
        $statusMap = [
            'pending' => ['label' => 'In Attesa', 'icon' => 'bi-hourglass-split', 'class' => 'admin-kpi-icon-pending'],
            'approved' => ['label' => 'Approvata', 'icon' => 'bi-check-circle', 'class' => 'text-success'],
            'rejected' => ['label' => 'Rifiutata', 'icon' => 'bi-x-circle', 'class' => 'text-danger'],
        ];

        // Badge stato progetto allineato allo stile corrente
        $pStatusMap = [
            'published' => ['label' => 'Pubblicato', 'icon' => 'bi-broadcast', 'class' => 'text-success'],
            'draft' => ['label' => 'Bozza', 'icon' => 'bi-pencil', 'class' => 'text-secondary'],
            'completed' => ['label' => 'Completato', 'icon' => 'bi-archive', 'class' => 'text-dark'],
        ];
        $pStatusStr = strtolower((string) $project->status);
        $pStatusConfig = $pStatusMap[$pStatusStr] ?? ['label' => ucfirst($pStatusStr), 'icon' => 'bi-question-circle', 'class' => 'text-secondary'];
    @endphp

    <div class="container pt-4 pb-5">

        {{-- ── NAVIGAZIONE (Pulsante Mobile + Breadcrumb Desktop) ──────── --}}
        <div class="d-md-none mb-3">
            <a href="{{ route('project.show', ['project' => $project, 'adminContext' => 1]) }}"
                class="btn btn-ae btn-ae-light border shadow-sm rounded-pill px-3 py-2 text-secondary fw-semibold transition-hover text-decoration-none">
                <i class="bi bi-arrow-left me-2"></i>Indietro
            </a>
        </div>

        <div class="d-none d-md-block mb-4">
            <x-breadcrumb>
                <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Progetti</a></li>
                <li class="breadcrumb-item">
                    <a
                        href="{{ route('project.show', ['project' => $project, 'adminContext' => 1]) }}">{{ Str::limit($project->title, 35) }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Candidature</li>
            </x-breadcrumb>
        </div>

        {{-- ── HEADER ──────────────────────────────────────────────────── --}}
        <div class="row align-items-center g-3 mb-4">
            <div class="col-12">
                <h1 class="display-6 fw-bold text-dark mb-1">Gestione Candidature</h1>
                <p class="text-muted mb-0">
                    <i class="bi bi-folder2-open me-1"></i>
                    <strong class="text-primary">{{ $project->title }}</strong>
                    &mdash;
                    Scadenza: <span
                        class="fw-medium">{{ $project->expire_date ? $project->expire_date->format('d/m/Y') : 'Non specificata' }}</span>
                    &mdash;
                    <span
                        class="badge rounded-pill bg-light border px-3 py-1 {{ $pStatusConfig['class'] }} d-inline-flex align-items-center gap-1 shadow-sm align-middle"
                        style="font-size:.8rem;">
                        <i class="bi {{ $pStatusConfig['icon'] }}"></i>
                        {{ $pStatusConfig['label'] }}
                    </span>
                </p>
            </div>
        </div>

        {{-- ── BARRA AVANZAMENTO POSTI ──────────────────────────────────── --}}
        @php
            $pct = $project->requested_people > 0 ? min(100, round(($stats['approved'] / $project->requested_people) * 100)) : 0;
            $barColor = $pct >= 100 ? 'danger' : ($pct >= 70 ? 'warning' : 'success');
        @endphp
        <div class="bg-white rounded-4 shadow-sm p-3 p-md-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold text-primary small"><i class="bi bi-people me-1"></i>Posti occupati</span>
                <span class="fw-bold {{ $pct >= 100 ? 'text-danger' : 'text-primary' }}">
                    {{ $stats['approved'] }} / {{ $project->requested_people }}
                </span>
            </div>
            <div class="progress" style="height:8px;border-radius:4px;">
                <div class="progress-bar bg-{{ $barColor }}" style="width:{{ $pct }}%"></div>
            </div>
            @if($pct >= 100)
                <p class="text-danger small mt-2 mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i>Limite raggiunto —
                    impossibile approvare ulteriori candidature.</p>
            @elseif($stats['approved'] > 0)
                <p class="text-muted small mt-2 mb-0">Rimangono
                    <strong>{{ $project->requested_people - $stats['approved'] }}</strong> posti disponibili.</p>
            @endif
        </div>

        {{-- ── FILTRI (Ottimizzati UX/UI per Mobile) ────────────────────── --}}
        <div class="bg-white rounded-4 shadow-sm p-3 p-md-4 mb-4">
            <form method="GET" action="{{ route('admin.applications.index', $project->id) }}" class="m-0">
                <div class="row g-2 align-items-end">

                    {{-- Barra di Ricerca --}}
                    <div class="col-12 col-lg-6">
                        <label for="app-search" class="form-label small text-body-secondary fw-semibold mb-1">Cerca
                            candidato</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-body-secondary"></i>
                            </span>
                            <input type="text" id="app-search" name="search" value="{{ request('search') }}"
                                class="form-control border-start-0 bg-light" placeholder="Nome o email..."
                                onchange="this.form.requestSubmit()">

                            {{-- Bottone Filtri (Visibile SOLO su Mobile) --}}
                            <button class="btn btn-light border d-lg-none" type="button" data-bs-toggle="collapse"
                                data-bs-target="#adminFilters" aria-expanded="false" aria-controls="adminFilters"
                                aria-label="Mostra filtri avanzati">
                                <i class="bi bi-sliders text-body-secondary"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Dropdown Filtri (Collapse su Mobile) --}}
                    <div class="col-12 col-lg-6">
                        <div class="collapse d-lg-block" id="adminFilters">
                            <div class="row g-2 align-items-end mt-2 mt-lg-0">

                                <div class="col-12 col-sm-6 col-lg-8">
                                    <label for="app-status"
                                        class="form-label small text-body-secondary fw-semibold mb-1 d-none d-lg-block">Stato</label>
                                    <select id="app-status" name="status" class="form-select bg-light border-0"
                                        onchange="this.form.requestSubmit()">
                                        <option value="">Tutti gli stati</option>
                                        <option value="pending" @selected(request('status') === 'pending')>In Attesa</option>
                                        <option value="approved" @selected(request('status') === 'approved')>Approvate
                                        </option>
                                        <option value="rejected" @selected(request('status') === 'rejected')>Rifiutate
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12 col-sm-6 col-lg-4 d-grid mt-3 mt-sm-0">
                                    <a href="{{ route('admin.applications.index', $project->id) }}"
                                        class="btn btn-ae btn-ae-square btn-ae-outline-secondary d-inline-flex align-items-center justify-content-center">
                                        Cancella Filtri
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Badge Filtri Attivi (Mobile) --}}
                @php
                    $hasHiddenFilters = request()->filled('status') || request()->filled('search');
                @endphp
                @if ($hasHiddenFilters)
                    <div class="mt-3 pt-3 border-top d-lg-none">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="small fw-semibold text-body-secondary me-1">Filtri attivi:</span>

                            @if (request()->filled('search'))
                                <span
                                    class="badge bg-light border text-dark rounded-pill py-2 px-3 d-inline-flex align-items-center gap-2 shadow-sm">
                                    <span class="text-body-secondary fw-normal">Testo:</span> {{ request('search') }}
                                    <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}"
                                        class="text-dark opacity-50 text-decoration-none" aria-label="Rimuovi filtro"><i
                                            class="bi bi-x-circle-fill"></i></a>
                                </span>
                            @endif

                            @if (request()->filled('status'))
                                @php
                                    $statusStr = request('status');
                                    $statusLabel = $statusMap[$statusStr]['label'] ?? ucfirst($statusStr);
                                @endphp
                                <span
                                    class="badge bg-light border text-dark rounded-pill py-2 px-3 d-inline-flex align-items-center gap-2 shadow-sm">
                                    <span class="text-body-secondary fw-normal">Stato:</span> {{ $statusLabel }}
                                    <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => null]) }}"
                                        class="text-dark opacity-50 text-decoration-none" aria-label="Rimuovi filtro"><i
                                            class="bi bi-x-circle-fill"></i></a>
                                </span>
                            @endif

                            <a href="{{ route('admin.applications.index', $project->id) }}"
                                class="btn btn-link text-danger text-decoration-none btn-sm ms-auto py-0">Svuota</a>
                        </div>
                    </div>
                @endif
            </form>
        </div>

        {{-- ── TABELLA DESKTOP ──────────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle admin-table-clean mb-0 d-none d-md-table">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-4">Candidato</th>
                            <th scope="col">Data di invio</th>
                            <th scope="col" class="text-center">Stato</th>
                            <th scope="col" class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            @php
                                $userName = $application->user->name ?? 'Utente Sconosciuto';
                                $userInitial = strtoupper(substr($userName, 0, 1));

                                $appStatusStr = strtolower((string) $application->status);
                                $appStatusConfig = $statusMap[$appStatusStr] ?? ['label' => ucfirst($appStatusStr), 'icon' => 'bi-question-circle', 'class' => 'text-secondary'];
                            @endphp
                            <tr class="admin-clickable-row position-relative"
                                data-href="{{ route('admin.applications.show', $application) }}"
                                onclick="window.location.href=this.dataset.href" style="cursor: pointer;">

                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                            style="width:36px;height:36px;font-size:.85rem">
                                            {{ $userInitial }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold lh-1">{{ $userName }}</div>
                                            <small class="text-muted">{{ $application->user->email ?? 'N/D' }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    {{ $application->created_at ? $application->created_at->format('d/m/Y') : 'N/D' }}
                                </td>

                                <td class="text-center">
                                    <span
                                        class="badge rounded-pill bg-light border px-3 py-2 {{ $appStatusConfig['class'] }} d-inline-flex align-items-center gap-1"
                                        style="font-size: 0.85rem;">
                                        <i class="bi {{ $appStatusConfig['icon'] }}"></i>
                                        {{ $appStatusConfig['label'] }}
                                    </span>
                                </td>

                                <td class="text-end pe-4">
                                    <i class="bi bi-chevron-right text-muted fs-5"></i>
                                </td>
                            </tr>
                        @empty
                            <tr class="admin-empty-row">
                                <td colspan="4" class="text-center text-muted py-4">
                                    @if(request()->hasAny(['search', 'status']))
                                        Nessun risultato per i filtri applicati.
                                    @else
                                        Nessuna candidatura disponibile.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── VISTA MOBILE ─────────────────────────────────────────── --}}
            <div class="d-md-none p-3 admin-mobile-list admin-mobile-projects-list">
                @forelse($applications as $application)
                    @php
                        $userName = $application->user->name ?? 'Utente Sconosciuto';
                        $userInitial = strtoupper(substr($userName, 0, 1));

                        $appStatusStr = strtolower((string) $application->status);
                        $appStatusConfig = $statusMap[$appStatusStr] ?? ['label' => ucfirst($appStatusStr), 'icon' => 'bi-question-circle', 'class' => 'text-secondary'];
                    @endphp
                    <a href="{{ route('admin.applications.show', $application) }}"
                        class="admin-mobile-item admin-mobile-project-card text-decoration-none text-dark d-block mb-3">

                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2 admin-mobile-project-head">
                            <div class="d-flex align-items-center gap-2 admin-mobile-project-title-wrap">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                    style="width:32px;height:32px;font-size:.8rem">
                                    {{ $userInitial }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="h6 fw-bold mb-0 admin-mobile-title lh-1">{{ $userName }}</h3>
                                    <small
                                        class="text-muted text-truncate d-block">{{ $application->user->email ?? 'N/D' }}</small>
                                </div>
                            </div>

                            <span
                                class="badge rounded-pill bg-light border px-3 py-2 {{ $appStatusConfig['class'] }} shadow-sm d-inline-flex align-items-center gap-1 small admin-mobile-status-badge flex-shrink-0"
                                style="font-size: 0.85rem;">
                                <i class="bi {{ $appStatusConfig['icon'] }}"></i>
                                <span class="d-none d-sm-inline">{{ $appStatusConfig['label'] }}</span>
                            </span>
                        </div>

                        <p class="mb-2 small admin-mobile-meta mt-3">
                            <span class="text-body-secondary">Inviata il:</span>
                            {{ $application->created_at ? $application->created_at->format('d/m/Y') : 'N/D' }}
                        </p>

                        <div class="d-flex justify-content-end align-items-center mt-2 border-top pt-2">
                            <span class="text-primary small fw-semibold">Vedi dettagli <i
                                    class="bi bi-chevron-right ms-1"></i></span>
                        </div>
                    </a>
                @empty
                    <div class="admin-mobile-empty-state text-center py-4 text-muted">
                        @if(request()->hasAny(['search', 'status']))
                            Nessun risultato per i filtri applicati.
                        @else
                            Nessuna candidatura disponibile.
                        @endif
                    </div>
                @endforelse
            </div>

            @if($applications->hasPages())
                <div class="card-footer bg-white border-0 border-top py-3">
                    <div class="d-flex justify-content-center">
                        {{ $applications->links() }}
                    </div>
                </div>
            @endif

        </div>{{-- /card --}}

    </div>

    {{-- ══ MODALI APPROVA / RIFIUTA / AGGIORNA ═══════════════════════════ --}}
    @foreach($applications as $application)
        @php
            $remainingSpots = $project->requested_people - $stats['approved'];
            $limitReached = $stats['approved'] >= $project->requested_people;
        @endphp

        @if($application->status === 'pending')
            <div class="modal fade" id="approveModal{{ $application->id }}" tabindex="-1">
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
                                @if($remainingSpots <= 3 && $remainingSpots > 0)
                                    <div class="alert alert-warning py-2 small"><i
                                            class="bi bi-exclamation-triangle me-1"></i>Rimarranno solo
                                        <strong>{{ $remainingSpots - 1 }}</strong> posti disponibili.</div>
                                @endif
                                <div class="mb-0">
                                    <label class="form-label fw-medium">Messaggio per il candidato <span
                                            class="text-muted fw-normal">(opzionale)</span></label>
                                    <textarea class="form-control" name="admin_message" rows="3"
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

            <div class="modal fade" id="rejectModal{{ $application->id }}" tabindex="-1">
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
                                    <label class="form-label fw-medium">Motivo del rifiuto <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control" name="admin_message" rows="3" required
                                        placeholder="Spiega i motivi del rifiuto…"></textarea>
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

        <div class="modal fade" id="updateModal{{ $application->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <form action="{{ route('admin.applications.update-status', $application) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title mb-0">Modifica Stato</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body pt-3">
                            @if($application->status !== 'pending')
                                <div class="d-flex gap-2 align-items-start p-3 rounded-3 mb-3"
                                    style="background:rgba(253,197,0,.1);border-left:4px solid #f59e0b">
                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5 flex-shrink-0 mt-1"></i>
                                    <p class="text-muted mb-0 small">Questa candidatura è già stata
                                        <strong>{{ $application->status === 'approved' ? 'approvata' : 'rifiutata' }}</strong>. Sei
                                        sicuro di voler modificare lo stato?</p>
                                </div>
                            @endif
                            <p>Candidatura di <strong>{{ $application->user->name }}</strong></p>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Nuovo stato <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    @php $limitReachedForApprove = $limitReached && $application->status !== 'approved'; @endphp
                                    <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>In Attesa
                                    </option>
                                    <option value="approved" @selected($application->status === 'approved')
                                        @disabled($limitReachedForApprove)>
                                        Approvata{{ $limitReachedForApprove ? ' (limite raggiunto)' : '' }}</option>
                                    <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rifiutata
                                    </option>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-medium">Messaggio per il candidato</label>
                                <textarea class="form-control" name="admin_message" rows="3"
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
    @endforeach

    <x-category-info-modals />
@endsection