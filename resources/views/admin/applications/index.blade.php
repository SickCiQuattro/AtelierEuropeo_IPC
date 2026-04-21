@extends('layouts.master')

@section('page_title', 'Gestione Candidature - ' . $project->title)

@section('breadcrumb')
    <div class="bg-light py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.projects.index') }}" class="text-decoration-none">Progetti</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('project.show', ['project' => $project, 'adminContext' => 1]) }}"
                            class="text-decoration-none">{{ $project->title }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Candidature</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('body')
    <div x-data="candidatureManager()" class="container mt-4 mb-5">

        {{-- ── HEADER ──────────────────────────────────────────────────── --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="mb-1">Gestione Candidature</h1>
                <p class="text-muted mb-0">
                    <i class="bi bi-folder2-open me-1"></i>
                    <strong class="text-primary">{{ $project->title }}</strong>
                    &mdash;
                    Scadenza:
                    <span class="fw-medium">
                        {{ $project->expire_date ? $project->expire_date->format('d/m/Y') : 'Non specificata' }}
                    </span>
                    &mdash;
                    @php
                        $statusMap = ['published' => ['Pubblicato','success'], 'draft' => ['Bozza','warning'], 'completed' => ['Completato','secondary']];
                        [$statusLabel, $statusColor] = $statusMap[$project->status] ?? [ucfirst($project->status), 'secondary'];
                    @endphp
                    <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                </p>
            </div>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-ae btn-ae-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Tutti i Progetti
            </a>
        </div>

        {{-- ── BARRA DI AVANZAMENTO POSTI ──────────────────────────────── --}}
        @php
            $pct = $project->requested_people > 0
                ? min(100, round(($stats['approved'] / $project->requested_people) * 100))
                : 0;
            $barColor = $pct >= 100 ? 'danger' : ($pct >= 70 ? 'warning' : 'success');
        @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold text-primary">
                        <i class="bi bi-people me-1"></i> Posti occupati
                    </span>
                    <span class="fw-bold {{ $pct >= 100 ? 'text-danger' : 'text-primary' }}">
                        {{ $stats['approved'] }} / {{ $project->requested_people }}
                    </span>
                </div>
                <div class="progress" style="height: 8px; border-radius: 4px;">
                    <div class="progress-bar bg-{{ $barColor }}" role="progressbar"
                        style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0"
                        aria-valuemax="100"></div>
                </div>
                @if($pct >= 100)
                    <p class="text-danger small mt-2 mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Limite partecipanti raggiunto — non è possibile approvare ulteriori candidature.
                    </p>
                @elseif($stats['approved'] > 0)
                    <p class="text-muted small mt-2 mb-0">
                        Rimangono <strong>{{ $project->requested_people - $stats['approved'] }}</strong>
                        posti disponibili.
                    </p>
                @endif
            </div>
        </div>

        {{-- ── CARD STATISTICHE (solo informative) ────────────────────────── --}}
        <div class="row g-3 mb-4">
            @php
                $statCards = [
                    ['icon' => 'bi-people-fill',       'value' => $stats['total'],    'label' => 'Totali',    'color' => 'primary'],
                    ['icon' => 'bi-clock-history',     'value' => $stats['pending'],  'label' => 'In Attesa', 'color' => 'warning'],
                    ['icon' => 'bi-check-circle-fill', 'value' => $stats['approved'], 'label' => 'Approvate', 'color' => 'success'],
                    ['icon' => 'bi-x-circle-fill',     'value' => $stats['rejected'], 'label' => 'Rifiutate', 'color' => 'danger'],
                ];
            @endphp
            @foreach($statCards as $sc)
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3 py-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:44px;height:44px;background:rgba(var(--bs-{{ $sc['color'] }}-rgb),.12)">
                                <i class="bi {{ $sc['icon'] }} fs-5 text-{{ $sc['color'] }}"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-4 lh-1" style="color:var(--bs-primary)">{{ $sc['value'] }}</div>
                                <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.04em">{{ $sc['label'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── TABELLA CANDIDATURE ─────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    {{-- Titolo + contatore --}}
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2 text-primary"></i>
                        Candidature
                        <span class="badge bg-primary ms-1">{{ $stats['total'] }}</span>
                    </h5>

                    {{-- Ricerca + filtro --}}
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        {{-- Barra di ricerca --}}
                        <div class="input-group input-group-sm" style="min-width:200px;">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 bg-light"
                                   placeholder="Cerca candidato o email…"
                                   x-model="search"
                                   @keydown.enter.prevent="applyFilters()"
                                   aria-label="Cerca candidato">
                        </div>

                        {{-- Filtro per stato --}}
                        <select class="form-select form-select-sm" style="width:auto;"
                                x-model="filterStatus"
                                aria-label="Filtra per stato">
                            <option value="all">Tutti gli stati</option>
                            <option value="pending">In Attesa</option>
                            <option value="approved">Approvate</option>
                            <option value="rejected">Rifiutate</option>
                        </select>

                        {{-- Filtra --}}
                        <button type="button"
                                class="btn btn-ae btn-ae-primary btn-sm"
                                @click="applyFilters()">
                            <i class="bi bi-funnel me-1"></i>Filtra
                        </button>

                        {{-- Reimposta — usa visibility per non spostare il layout --}}
                        <button type="button"
                                class="btn btn-ae btn-ae-outline-secondary btn-sm"
                                :style="hasActiveFilters ? 'visibility:visible' : 'visibility:hidden'"
                                @click="resetFilters()">
                            <i class="bi bi-x-lg me-1"></i>Reimposta
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                @if($stats['total'] > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Candidato</th>
                                    <th class="d-none d-md-table-cell">Email</th>
                                    <th class="d-none d-lg-table-cell">Telefono</th>
                                    <th class="d-none d-md-table-cell">Data</th>
                                    <th>Stato</th>
                                    <th style="width:160px">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $application)
                                    @php
                                        $appStatus = $application->status;
                                        $statusBadge = [
                                            'pending'  => ['bg-warning text-dark', 'bi-clock-history', 'In Attesa'],
                                            'approved' => ['bg-success', 'bi-check-circle', 'Approvata'],
                                            'rejected' => ['bg-danger', 'bi-x-circle', 'Rifiutata'],
                                        ][$appStatus] ?? ['bg-secondary', 'bi-question-circle', ucfirst($appStatus)];
                                        $limitReached = $stats['approved'] >= $project->requested_people;
                                    @endphp
                                    <tr x-show="rowVisible('{{ $appStatus }}', '{{ addslashes($application->user->name) }}', '{{ addslashes($application->user->email) }}')"
                                        data-app-id="{{ $application->id }}">

                                        {{-- Avatar + Nome --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                                     style="width:36px;height:36px;font-size:.85rem">
                                                    {{ strtoupper(substr($application->user->name, 0, 1)) }}
                                                </div>
                                                <span class="fw-semibold">{{ $application->user->name }}</span>
                                            </div>
                                        </td>

                                        {{-- Email --}}
                                        <td class="d-none d-md-table-cell">
                                            <a href="mailto:{{ $application->user->email }}"
                                               class="text-decoration-none text-muted small">
                                                {{ $application->user->email }}
                                            </a>
                                        </td>

                                        {{-- Telefono --}}
                                        <td class="d-none d-lg-table-cell">
                                            @if($application->phone)
                                                <a href="tel:{{ $application->phone }}"
                                                   class="text-decoration-none text-muted small">
                                                    {{ $application->phone }}
                                                </a>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        {{-- Data --}}
                                        <td class="d-none d-md-table-cell">
                                            <span class="text-muted small">
                                                {{ $application->created_at->format('d/m/Y') }}
                                            </span>
                                        </td>

                                        {{-- Stato (badge con testo italiano — P. 76) --}}
                                        <td>
                                            <span class="badge {{ $statusBadge[0] }}">
                                                <i class="bi {{ $statusBadge[1] }} me-1"></i>
                                                {{ $statusBadge[2] }}
                                            </span>
                                        </td>

                                        {{-- Azioni (P. 66 – etichette + tooltip; P. 77 – consistenza) --}}
                                        <td>
                                            <div class="d-flex gap-1">
                                                {{-- Visualizza (tutti gli stati) --}}
                                                <a href="{{ route('admin.applications.show', $application) }}"
                                                   class="btn btn-ae btn-ae-outline-primary btn-sm"
                                                   data-bs-toggle="tooltip" title="Visualizza dettagli">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                @if($appStatus === 'pending')
                                                    {{-- Approva --}}
                                                    <button type="button"
                                                        class="btn btn-ae btn-ae-outline-success btn-sm{{ $limitReached ? ' disabled' : '' }}"
                                                        @if(!$limitReached)
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveModal{{ $application->id }}"
                                                        @endif
                                                        title="{{ $limitReached ? 'Limite partecipanti raggiunto' : 'Approva candidatura' }}"
                                                        {{ $limitReached ? 'disabled' : '' }}>
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                    {{-- Rifiuta --}}
                                                    <button type="button"
                                                        class="btn btn-ae btn-ae-outline-danger btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal{{ $application->id }}"
                                                        title="Rifiuta candidatura">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                @endif

                                                {{-- Modifica stato (P. 77 – ora disponibile per TUTTI gli stati,
                                                     non solo per chi è già stato valutato) --}}
                                                <button type="button"
                                                    class="btn btn-ae btn-ae-outline-secondary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateModal{{ $application->id }}"
                                                    title="Modifica stato candidatura">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Messaggio nessun risultato (filtri attivi) --}}
                    <div x-show="noResults" x-cloak class="text-center py-4 text-muted">
                        <i class="bi bi-search fs-2 d-block mb-2"></i>
                        Nessuna candidatura corrisponde ai filtri selezionati.
                        <button class="btn btn-link btn-sm p-0 ms-1" @click="resetFilters()">Reimposta filtri</button>
                    </div>

                    {{-- Paginazione --}}
                    @if($applications->hasPages())
                        <div class="d-flex justify-content-center py-3 border-top">
                            {{ $applications->links() }}
                        </div>
                    @endif

                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                        <h5 class="text-muted">Nessuna candidatura presente</h5>
                        <p class="text-muted small">Non sono ancora state inviate candidature per questo progetto.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>{{-- /x-data --}}

    {{-- ══════════════════════════════════════════════════════════
         MODALI
    ══════════════════════════════════════════════════════════ --}}
    @foreach($applications as $application)
        @php
            $remainingSpots = $project->requested_people - $stats['approved'];
            $limitReached   = $stats['approved'] >= $project->requested_people;
        @endphp

        {{-- Modale Approva --}}
        @if($application->status === 'pending')
            <div class="modal fade" id="approveModal{{ $application->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form action="{{ route('admin.applications.approve', $application) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-header border-0 pb-0">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center"
                                         style="width:40px;height:40px;">
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

                                @if($remainingSpots <= 3 && $remainingSpots > 0)
                                    <div class="alert alert-warning py-2 small">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Dopo questa approvazione rimarranno solo
                                        <strong>{{ $remainingSpots - 1 }}</strong> posti disponibili.
                                    </div>
                                @endif

                                <div class="mb-0">
                                    <label for="admin_message_app{{ $application->id }}" class="form-label fw-medium">
                                        Messaggio per il candidato
                                        <span class="text-muted fw-normal">(opzionale)</span>
                                    </label>
                                    <textarea class="form-control"
                                              id="admin_message_app{{ $application->id }}"
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
            <div class="modal fade" id="rejectModal{{ $application->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form action="{{ route('admin.applications.reject', $application) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-header border-0 pb-0">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center"
                                         style="width:40px;height:40px;">
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
                                    <label for="reject_message{{ $application->id }}" class="form-label fw-medium">
                                        Motivo del rifiuto
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control"
                                              id="reject_message{{ $application->id }}"
                                              name="admin_message" rows="3" required
                                              placeholder="Spiega i motivi del rifiuto…"></textarea>
                                    <div class="form-text">
                                        È importante spiegare i motivi per aiutare il candidato a migliorare.
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

        {{-- Modale Modifica Stato (P. 77 — disponibile per TUTTI gli stati) --}}
        <div class="modal fade" id="updateModal{{ $application->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('admin.applications.update-status', $application) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header border-0 pb-0">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                     style="width:40px;height:40px;">
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
                                <label for="status{{ $application->id }}" class="form-label fw-medium">
                                    Nuovo stato <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="status{{ $application->id }}"
                                        name="status" required>
                                    @php
                                        $limitReachedForApprove = $stats['approved'] >= $project->requested_people
                                            && $application->status !== 'approved';
                                    @endphp
                                    <option value="pending"
                                        {{ $application->status === 'pending' ? 'selected' : '' }}>
                                        In Attesa
                                    </option>
                                    <option value="approved"
                                        @selected($application->status === 'approved')
                                        @disabled($limitReachedForApprove)>
                                        Approvata{{ $limitReachedForApprove ? ' (limite raggiunto)' : '' }}
                                    </option>
                                    <option value="rejected"
                                        {{ $application->status === 'rejected' ? 'selected' : '' }}>
                                        Rifiutata
                                    </option>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label for="update_message{{ $application->id }}" class="form-label fw-medium">
                                    Messaggio per il candidato
                                </label>
                                <textarea class="form-control"
                                          id="update_message{{ $application->id }}"
                                          name="admin_message" rows="3"
                                          placeholder="Aggiorna il messaggio per il candidato…">{{ $application->admin_message }}</textarea>
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
    @endforeach

    {{-- ── STILI LOCALI ─────────────────────────────────────────── --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>

    {{-- ── ALPINE.JS CONTROLLER ────────────────────────────────── --}}
    <script>
        function candidatureManager() {
            return {
                // Stato dei campi (in digitazione)
                search: '',
                filterStatus: 'all',

                // Stato applicato (solo dopo click su Filtra)
                appliedSearch: '',
                appliedStatus: 'all',

                get hasActiveFilters() {
                    return this.appliedSearch !== '' || this.appliedStatus !== 'all';
                },

                applyFilters() {
                    this.appliedSearch = this.search;
                    this.appliedStatus = this.filterStatus;
                },

                resetFilters() {
                    this.search = '';
                    this.filterStatus = 'all';
                    this.appliedSearch = '';
                    this.appliedStatus = 'all';
                },

                rowVisible(status, name, email) {
                    const statusOk = this.appliedStatus === 'all' || this.appliedStatus === status;
                    const q = this.appliedSearch.toLowerCase();
                    const textOk = !q || name.toLowerCase().includes(q) || email.toLowerCase().includes(q);
                    return statusOk && textOk;
                },

                get noResults() {
                    const rows = document.querySelectorAll('tbody tr[data-app-id]');
                    if (rows.length === 0) return false;
                    return [...rows].every(r => r.style.display === 'none');
                },
            };
        }

        // Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el, { placement: 'top', trigger: 'hover' });
            });
        });
    </script>
@endsection