@extends('layouts.master')

@section('title', 'AE - Tutti i Progetti Admin')

@section('active_progetti', 'active')

@section('body')
    @php
        $projectsCollection = $projects ?? collect();
        $availableCountriesCollection = $availableCountries ?? collect();
        $categoryMap = [
            'CES' => ['label' => 'CES', 'badge' => 'badge-prog-ces'],
            'SG' => ['label' => 'SG', 'badge' => 'badge-prog-sg'],
            'CF' => ['label' => 'CF', 'badge' => 'badge-prog-cf'],
        ];

        // Icone aggiornate in versione "outline" per coerenza visiva e leggerezza
        $statusMap = [
            'completed' => ['label' => 'Completato', 'icon' => 'bi-archive', 'class' => 'text-dark'],
            'draft' => ['label' => 'Bozza', 'icon' => 'bi-pencil', 'class' => 'text-secondary'],
            'published' => ['label' => 'Pubblicato', 'icon' => 'bi-broadcast', 'class' => 'text-success'],
        ];
    @endphp

    <div class="container py-5" x-data="{
            selectedCount: 0,
            selectAll: false,
            selectedProjectIds: [],
            selectedStatuses: [],
            bulkStatus: '',
            bulkWarningMessage: '',
            statusLabels: {
                draft: 'Bozza',
                published: 'Pubblicato',
                completed: 'Completato',
            },
            getActiveRowCheckboxes() {
                const rowCheckboxes = Array.from(this.$root.querySelectorAll('.project-row-checkbox:not([disabled])'));
                return rowCheckboxes.filter((checkbox) => checkbox.offsetParent !== null);
            },
            getSelectedStatuses(checkedRows) {
                return Array.from(new Set(
                    checkedRows
                        .map((checkbox) => String(checkbox.dataset.status || '').toLowerCase())
                        .filter((status) => status !== '')
                ));
            },
            getStatusLabel(status) {
                return this.statusLabels[status] || status;
            },
            getNextStatusForSelection() {
                if (this.selectedStatuses.length !== 1) {
                    return null;
                }

                const currentStatus = this.selectedStatuses[0];
                if (currentStatus === 'draft') {
                    return 'published';
                }

                if (currentStatus === 'published') {
                    return 'completed';
                }

                return null;
            },
            syncBulkStatusContext(checkedRows) {
                this.selectedStatuses = this.getSelectedStatuses(checkedRows);

                if (this.selectedStatuses.length > 1) {
                    const labels = this.selectedStatuses.map((status) => this.getStatusLabel(status)).join(', ');
                    this.bulkWarningMessage = `Hai selezionato progetti con stati diversi (${labels}). Seleziona solo progetti con lo stesso stato per l'aggiornamento in blocco.`;
                    this.bulkStatus = '';
                    return;
                }

                const nextStatus = this.getNextStatusForSelection();

                if (this.selectedStatuses.length === 1 && nextStatus === null) {
                    this.bulkWarningMessage = 'I progetti completati non possono cambiare stato.';
                    this.bulkStatus = '';
                    return;
                }

                this.bulkWarningMessage = '';
                this.bulkStatus = nextStatus || '';
            },
            toggleAll(event) {
                this.selectAll = event.target.checked;
                const rowCheckboxes = this.getActiveRowCheckboxes();
                rowCheckboxes.forEach((checkbox) => {
                    checkbox.checked = this.selectAll;
                });
                this.updateCount();
            },
            updateCount() {
                const rowCheckboxes = this.getActiveRowCheckboxes();
                const checkedRows = Array.from(rowCheckboxes).filter((checkbox) => checkbox.checked);
                this.selectedProjectIds = checkedRows.map((checkbox) => checkbox.value);
                this.selectedCount = this.selectedProjectIds.length;
                this.selectAll = rowCheckboxes.length > 0 && this.selectedCount === rowCheckboxes.length;
                this.syncBulkStatusContext(checkedRows);

                const masterCheckbox = this.$root.querySelector('#projects-select-all');
                if (masterCheckbox) {
                    masterCheckbox.indeterminate = this.selectedCount > 0 && this.selectedCount < rowCheckboxes.length;
                }
            },
            clearSelection() {
                const rowCheckboxes = this.getActiveRowCheckboxes();
                rowCheckboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });
                this.selectedCount = 0;
                this.selectAll = false;
                this.selectedProjectIds = [];
                this.selectedStatuses = [];
                this.bulkStatus = '';
                this.bulkWarningMessage = '';

                const masterCheckbox = this.$root.querySelector('#projects-select-all');
                if (masterCheckbox) {
                    masterCheckbox.indeterminate = false;
                }
            }
        }" x-init="updateCount()">

        <div class="row align-items-center g-3 mb-4">
            <div class="col-lg">
                <h1 class="fw-bold text-dark mb-1">Progetti</h1>
                <p class="text-muted mb-0">Crea e gestisci nuove opportunità. Monitoria i progetti in corso.</p>
            </div>
            <div class="col-lg-auto">
                <a href="{{ route('project.create', ['adminContext' => 1]) }}"
                    class="btn btn-ae btn-ae-success btn-ae-square px-4 py-2">
                    <i class="bi bi-plus-lg me-2"></i>Crea Nuovo Progetto
                </a>
            </div>
        </div>

        {{-- ── FILTRI (Ottimizzati UX/UI per Mobile) ──────────── --}}
        <div class="row mb-4">
            <div class="col-12">
                <form method="GET" action="{{ route('admin.projects.index') }}" class="bg-white rounded-4 shadow-sm p-3">
                    <div class="row g-2 align-items-end">

                        {{-- Barra di Ricerca (Sempre visibile) --}}
                        <div class="col-12 col-lg-3">
                            <label for="project-search" class="form-label small text-body-secondary fw-semibold mb-1">Cerca
                                progetto</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-body-secondary"></i>
                                </span>
                                <input type="text" id="project-search" name="q" value="{{ request()->query('q', '') }}"
                                    class="form-control border-start-0" placeholder="Titolo, paese..."
                                    onchange="this.form.requestSubmit()">

                                {{-- Bottone Filtri (Visibile SOLO su Mobile) --}}
                                <button class="btn btn-light border d-lg-none" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#adminFilters" aria-expanded="false" aria-controls="adminFilters"
                                    aria-label="Mostra filtri avanzati">
                                    <i class="bi bi-sliders text-body-secondary"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Dropdown Filtri (Collapse su Mobile, Inline su Desktop) --}}
                        <div class="col-12 col-lg-9">
                            <div class="collapse d-lg-block" id="adminFilters">
                                <div class="row g-2 align-items-end mt-2 mt-lg-0">

                                    <div class="col-12 col-sm-4 col-lg-3">
                                        <label for="project-country"
                                            class="form-label small text-body-secondary fw-semibold mb-1">Paese</label>
                                        <select id="project-country" name="country" class="form-select"
                                            onchange="this.form.requestSubmit()">
                                            <option value="">Tutti</option>
                                            @foreach ($availableCountriesCollection as $country)
                                                <option value="{{ $country }}" @selected(request('country') === $country)>
                                                    {{ $country }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-sm-4 col-lg-3">
                                        <label for="project-deadline"
                                            class="form-label small text-body-secondary fw-semibold mb-1">Scadenza</label>
                                        <select id="project-deadline" name="deadline" class="form-select"
                                            onchange="this.form.requestSubmit()">
                                            <option value="">Tutte</option>
                                            <option value="7" @selected(request('deadline') === '7')>Entro 7 giorni</option>
                                            <option value="30" @selected(request('deadline') === '30')>Entro 30 giorni
                                            </option>
                                            <option value="expired" @selected(request('deadline') === 'expired')>Già scaduti
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-sm-4 col-lg-3">
                                        <label for="project-status"
                                            class="form-label small text-body-secondary fw-semibold mb-1">Stato</label>
                                        <select id="project-status" name="status" class="form-select"
                                            onchange="this.form.requestSubmit()">
                                            <option value="">Tutti</option>
                                            <option value="published" @selected(request('status') === 'published')>Pubblicato
                                            </option>
                                            <option value="draft" @selected(request('status') === 'draft')>Bozza</option>
                                            <option value="completed" @selected(request('status') === 'completed')>Completato
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-lg-3 d-grid mt-3 mt-lg-0">
                                        <a href="{{ route('admin.projects.index') }}"
                                            class="btn btn-ae btn-ae-square btn-ae-outline-secondary d-inline-flex align-items-center justify-content-center">
                                            Cancella Filtri
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Badge Filtri Attivi (Visibili SOLO su Mobile quando i filtri sono nascosti) --}}
                    @php
                        $hasHiddenFilters = request()->filled('country') || request()->filled('deadline') || request()->filled('status') || request()->filled('q');
                    @endphp
                    @if ($hasHiddenFilters)
                        <div class="mt-3 pt-3 border-top d-lg-none">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="small fw-semibold text-body-secondary me-1">Filtri attivi:</span>

                                @if (request()->filled('q'))
                                    @php $removeQParams = request()->except('q', 'page'); @endphp
                                    <span
                                        class="badge bg-light border text-dark rounded-pill py-2 px-3 d-inline-flex align-items-center gap-2 shadow-sm">
                                        <span class="text-body-secondary fw-normal">Testo:</span> {{ request('q') }}
                                        <a href="{{ route('admin.projects.index', $removeQParams) }}"
                                            class="text-dark opacity-50 text-decoration-none" aria-label="Rimuovi filtro"><i
                                                class="bi bi-x-circle-fill"></i></a>
                                    </span>
                                @endif

                                @if (request()->filled('country'))
                                    @php $removeCountryParams = request()->except('country', 'page'); @endphp
                                    <span
                                        class="badge bg-light border text-dark rounded-pill py-2 px-3 d-inline-flex align-items-center gap-2 shadow-sm">
                                        <span class="text-body-secondary fw-normal">Paese:</span> {{ request('country') }}
                                        <a href="{{ route('admin.projects.index', $removeCountryParams) }}"
                                            class="text-dark opacity-50 text-decoration-none" aria-label="Rimuovi filtro"><i
                                                class="bi bi-x-circle-fill"></i></a>
                                    </span>
                                @endif

                                @if (request()->filled('deadline'))
                                                    @php
                                                        $removeDeadlineParams = request()->except('deadline', 'page');
                                                        $dl = request('deadline');
                                                        $dlLabel = $dl == '7' ? 'Entro 7 gg' : ($dl == '30' ? 'Entro 30 gg' : 'Scaduti');
                                                    @endphp
                                     <span
                                                        class="badge bg-light border text-dark rounded-pill py-2 px-3 d-inline-flex align-items-center gap-2 shadow-sm">
                                                        <span class="text-body-secondary fw-normal">Scadenza:</span> {{ $dlLabel }}
                                                        <a href="{{ route('admin.projects.index', $removeDeadlineParams) }}"
                                                            class="text-dark opacity-50 text-decoration-none" aria-label="Rimuovi filtro"><i
                                                                class="bi bi-x-circle-fill"></i></a>
                                                    </span>
                                @endif

                                @if (request()->filled('status'))
                                                @php
                                                    $removeStatusParams = request()->except('status', 'page');
                                                    $statusStr = request('status');
                                                    $statusLabel = $statusMap[$statusStr]['label'] ?? ucfirst($statusStr);
                                                @endphp
                                    <span
                                                    class="badge bg-light border text-dark rounded-pill py-2 px-3 d-inline-flex align-items-center gap-2 shadow-sm">
                                                    <span class="text-body-secondary fw-normal">Stato:</span> {{ $statusLabel }}
                                                    <a href="{{ route('admin.projects.index', $removeStatusParams) }}"
                                                        class="text-dark opacity-50 text-decoration-none" aria-label="Rimuovi filtro"><i
                                                            class="bi bi-x-circle-fill"></i></a>
                                                </span>
                                @endif

                                <a href="{{ route('admin.projects.index') }}"
                                    class="btn btn-link text-danger text-decoration-none btn-sm ms-auto py-0">Svuota</a>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle admin-table-clean mb-0 d-none d-md-table">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-3">
                                <input id="projects-select-all" type="checkbox" class="form-check-input" x-model="selectAll"
                                    @change="toggleAll($event)" aria-label="Seleziona tutti i progetti">
                            </th>
                            <th scope="col">Nome Progetto</th>
                            <th scope="col" class="text-center">Categoria</th>
                            <th scope="col">Paese</th>
                            <th scope="col" class="text-center">Candidature</th>
                            <th scope="col">Scadenza</th>
                            <th scope="col" class="text-center">Stato</th>
                            <th scope="col" class="text-end pe-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projectsCollection as $project)
                            @php
                                $projectId = data_get($project, 'id');
                                $projectTitle = data_get($project, 'title', 'Ecosistema Urbano');
                                $projectLocation = data_get($project, 'location', data_get($project, 'country', 'Milano, Italia'));

                                $expireDateRaw = data_get($project, 'expire_date', data_get($project, 'deadline'));
                                $deadlineText = $expireDateRaw
                                    ? \Carbon\Carbon::parse($expireDateRaw)->format('d/m/Y')
                                    : '08/04/2026';

                                $categoryTag = strtoupper((string) data_get($project, 'category.tag', data_get($project, 'category_tag', 'CES')));
                                $categoryConfig = $categoryMap[$categoryTag] ?? [
                                    'label' => $categoryTag ?: 'N/D',
                                    'badge' => 'badge-prog-ces',
                                ];

                                $applicationsCount = data_get($project, 'approved_applications_count', data_get($project, 'applications_count', 0));
                                $requestedPeople = data_get($project, 'requested_people', 6);

                                $status = strtolower((string) data_get($project, 'status', 'published'));
                                $isCompleted = $status === 'completed';
                                $statusConfig = $statusMap[$status] ?? $statusMap['draft'];

                                $showUrl = ($projectId && \Illuminate\Support\Facades\Route::has('project.show'))
                                    ? route('project.show', ['project' => $projectId, 'adminContext' => 1])
                                    : '#';
                                $editUrl = ($projectId && \Illuminate\Support\Facades\Route::has('project.edit'))
                                    ? route('project.edit', ['id' => $projectId, 'adminContext' => 1])
                                    : '#';
                                $deleteUrl = ($projectId && \Illuminate\Support\Facades\Route::has('project.show'))
                                    ? route('project.show', ['project' => $projectId, 'openDeleteModal' => 1, 'adminContext' => 1])
                                    : '#';
                            @endphp

                            <tr>
                                <td class="ps-3">
                                    <input type="checkbox" class="form-check-input project-row-checkbox"
                                        value="{{ $projectId }}" data-status="{{ $status }}" @change="updateCount"
                                        @disabled(!$projectId)aria-label="Seleziona progetto">
                                </td>
                                <td class="fw-semibold">{{ $projectTitle }}</td>
                                <td class="text-center">
                                    <span
                                        class="{{ $categoryConfig['badge'] }} shadow-sm d-inline-flex align-items-center justify-content-center">
                                        {{ $categoryConfig['label'] }}
                                    </span>
                                </td>
                                <td>{{ $projectLocation }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <x-participants-progress :current="$applicationsCount" :max="$requestedPeople" />
                                    </div>
                                </td>
                                <td>{{ $deadlineText }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge rounded-pill bg-light border px-3 py-2 {{ $statusConfig['class'] }} d-inline-flex align-items-center gap-1"
                                        style="font-size: 0.85rem;">
                                        <i class="bi {{ $statusConfig['icon'] }}"></i>
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="{{ $showUrl }}"
                                            class="btn btn-sm btn-ae btn-ae-square admin-project-action-view"
                                            title="Visualizza progetto" aria-label="Visualizza progetto">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if ($isCompleted)
                                            <button type="button"
                                                class="btn btn-sm btn-ae btn-ae-square btn-ae-outline-secondary opacity-50" disabled
                                                title="Progetto completato non modificabile" aria-label="Modifica non disponibile">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @else
                                            <a href="{{ $editUrl }}"
                                                class="btn btn-sm btn-ae btn-ae-square admin-project-action-edit"
                                                title="Modifica progetto" aria-label="Modifica progetto">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        <span class="vr admin-project-action-divider mx-1" aria-hidden="true"></span>
                                        <a href="{{ $deleteUrl }}" class="btn btn-sm btn-ae btn-ae-square btn-ae-outline-danger"
                                            title="Elimina progetto" aria-label="Elimina progetto">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Nessun progetto disponibile.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-md-none p-3 admin-mobile-list admin-mobile-projects-list">
                @forelse ($projectsCollection as $project)
                    @php
                        $projectId = data_get($project, 'id');
                        $projectTitle = data_get($project, 'title', 'Ecosistema Urbano');
                        $projectLocation = data_get($project, 'location', data_get($project, 'country', 'Milano, Italia'));

                        $expireDateRaw = data_get($project, 'expire_date', data_get($project, 'deadline'));
                        $deadlineText = $expireDateRaw
                            ? \Carbon\Carbon::parse($expireDateRaw)->format('d/m/Y')
                            : '08/04/2026';

                        $categoryTag = strtoupper((string) data_get($project, 'category.tag', data_get($project, 'category_tag', 'CES')));
                        $categoryConfig = $categoryMap[$categoryTag] ?? [
                            'label' => $categoryTag ?: 'N/D',
                            'badge' => 'badge-prog-ces',
                        ];

                        $applicationsCount = data_get($project, 'approved_applications_count', data_get($project, 'applications_count', 0));
                        $requestedPeople = data_get($project, 'requested_people', 6);

                        $status = strtolower((string) data_get($project, 'status', 'published'));
                        $isCompleted = $status === 'completed';
                        $statusConfig = $statusMap[$status] ?? $statusMap['draft'];

                        $showUrl = ($projectId && \Illuminate\Support\Facades\Route::has('project.show'))
                            ? route('project.show', ['project' => $projectId, 'adminContext' => 1])
                            : '#';
                        $editUrl = ($projectId && \Illuminate\Support\Facades\Route::has('project.edit'))
                            ? route('project.edit', ['id' => $projectId, 'adminContext' => 1])
                            : '#';
                        $deleteUrl = ($projectId && \Illuminate\Support\Facades\Route::has('project.show'))
                            ? route('project.show', ['project' => $projectId, 'openDeleteModal' => 1, 'adminContext' => 1])
                            : '#';
                    @endphp

                    <div class="admin-mobile-item admin-mobile-project-card">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2 admin-mobile-project-head">
                            <div class="d-flex align-items-center gap-2 admin-mobile-project-title-wrap">
                                <input type="checkbox" class="form-check-input project-row-checkbox" value="{{ $projectId }}"
                                    data-status="{{ $status }}" @change="updateCount" @disabled(!$projectId)
                                    aria-label="Seleziona progetto">
                                <h3 class="h6 fw-bold mb-0 admin-mobile-title">{{ $projectTitle }}</h3>
                            </div>
                            <span
                                class="badge rounded-pill bg-light border px-3 py-2 {{ $statusConfig['class'] }} shadow-sm d-inline-flex align-items-center gap-1 small admin-mobile-status-badge"
                                style="font-size: 0.85rem;">
                                <i class="bi {{ $statusConfig['icon'] }}"></i>
                                {{ $statusConfig['label'] }}
                            </span>
                        </div>

                        <div class="mb-2 d-flex align-items-center gap-2 admin-mobile-meta-row">
                            <span
                                class="{{ $categoryConfig['badge'] }} shadow-sm d-inline-flex align-items-center justify-content-center">
                                {{ $categoryConfig['label'] }}
                            </span>
                        </div>

                        <p class="mb-1 small admin-mobile-meta"><span class="text-body-secondary">Paese:</span>
                            {{ $projectLocation }}</p>
                        <p class="mb-2 small admin-mobile-meta"><span class="text-body-secondary">Scadenza:</span>
                            {{ $deadlineText }}</p>

                        <div class="mb-3 admin-mobile-project-progress">
                            <x-participants-progress :current="$applicationsCount" :max="$requestedPeople" />
                        </div>

                        <div class="d-flex align-items-center gap-2 admin-mobile-project-actions">
                            <a href="{{ $showUrl }}" class="btn btn-sm btn-ae btn-ae-square admin-project-action-view"
                                title="Visualizza progetto" aria-label="Visualizza progetto">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if ($isCompleted)
                                <button type="button" class="btn btn-sm btn-ae btn-ae-square btn-ae-outline-secondary opacity-50"
                                    disabled title="Progetto completato non modificabile" aria-label="Modifica non disponibile">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            @else
                                <a href="{{ $editUrl }}" class="btn btn-sm btn-ae btn-ae-square admin-project-action-edit"
                                    title="Modifica progetto" aria-label="Modifica progetto">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif
                            <a href="{{ $deleteUrl }}" class="btn btn-sm btn-ae btn-ae-square btn-ae-outline-danger"
                                title="Elimina progetto" aria-label="Elimina progetto">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-4 text-center text-muted">Nessun progetto disponibile.</div>
                @endforelse
            </div>

            <div class="card-footer bg-white border-0 border-top py-3">
                <div class="d-flex justify-content-center">
                    @if (isset($projects) && method_exists($projects, 'links'))
                        @php
                            $currentPage = $projects->currentPage();
                            $lastPage = $projects->lastPage();

                            // 1. Definiamo quali numeri VOGLIAMO mostrare
                            $pagesToShow = [1, $lastPage]; // Sempre la prima e l'ultima

                            // Aggiungiamo la corrente e i suoi vicini immediati (se esistono)
                            if ($currentPage > 1) {
                                $pagesToShow[] = $currentPage - 1;
                            }
                            $pagesToShow[] = $currentPage;
                            if ($currentPage < $lastPage) {
                                $pagesToShow[] = $currentPage + 1;
                            }

                            // 2. Rimuoviamo i doppioni e ordiniamo l'array dal più piccolo al più grande
                            $pagesToShow = array_unique($pagesToShow);
                            sort($pagesToShow);
                        @endphp

                        @if ($lastPage > 1)
                            <nav aria-label="Paginazione progetti">
                                <ul class="pagination pagination-sm flex-wrap justify-content-center mb-0 gap-2">

                                    {{-- Bottone Precedente --}}
                                    @if ($projects->onFirstPage())
                                        <li aria-disabled="true" aria-label="Precedente">
                                            <button class="btn btn-ae btn-ae-outline-primary" type="button" disabled aria-hidden="true">
                                                <i class="bi bi-chevron-left"></i>
                                            </button>
                                        </li>
                                    @else
                                        <li>
                                            <a class="btn btn-ae btn-ae-outline-primary" href="{{ $projects->previousPageUrl() }}"
                                                rel="prev" aria-label="Precedente">
                                                <i class="bi bi-chevron-left"></i>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Generazione Numeri e Puntini --}}
                                    @php $previousPage = null; @endphp
                                    @foreach ($pagesToShow as $page)

                                        {{-- Se c'è un "salto" maggiore di 1 tra il numero precedente e quello attuale, stampiamo i
                                        puntini --}}
                                        @if ($previousPage !== null && $page - $previousPage > 1)
                                            <li class="disabled" aria-disabled="true">
                                                <span
                                                    class="btn btn-ae btn-ae-outline-primary disabled border-0 text-muted fw-bold bg-transparent">...</span>
                                            </li>
                                        @endif

                                        {{-- Stampa del numero --}}
                                        @if ($page == $currentPage)
                                            <li class="active" aria-current="page">
                                                <span class="btn btn-ae btn-ae-primary">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li>
                                                <a class="btn btn-ae btn-ae-outline-primary"
                                                    href="{{ $projects->url($page) }}">{{ $page }}</a>
                                            </li>
                                        @endif

                                        @php $previousPage = $page; @endphp
                                    @endforeach

                                    {{-- Bottone Successivo --}}
                                    @if ($projects->hasMorePages())
                                        <li>
                                            <a class="btn btn-ae btn-ae-outline-primary" href="{{ $projects->nextPageUrl() }}"
                                                rel="next" aria-label="Successiva">
                                                <i class="bi bi-chevron-right"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li aria-disabled="true" aria-label="Successiva">
                                            <button class="btn btn-ae btn-ae-outline-primary" type="button" disabled aria-hidden="true">
                                                <i class="bi bi-chevron-right"></i>
                                            </button>
                                        </li>
                                    @endif

                                </ul>
                            </nav>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="position-fixed bottom-0 start-50 translate-middle-x mb-4 z-3" x-show="selectedCount > 0" x-transition
            style="display: none;">
            <div class="shadow-lg bg-white p-2 px-3 d-flex align-items-center gap-3 rounded-4 border">
                <button type="button" class="btn btn-sm btn-ae btn-ae-square btn-ae-outline-secondary"
                    @click="clearSelection" aria-label="Annulla selezione" title="Annulla selezione">
                    <i class="bi bi-x-lg"></i>
                </button>
                <span class="small fw-semibold"><span x-text="selectedCount"></span> progetti selezionati</span>
                <div class="vr"></div>

                <button type="button" class="btn btn-sm btn-ae btn-ae-square btn-ae-primary" data-bs-toggle="modal"
                    data-bs-target="#bulkStatusModal">
                    Cambia Stato
                </button>

                <button type="button" class="btn btn-sm btn-ae btn-ae-square btn-ae-danger" data-bs-toggle="modal"
                    data-bs-target="#bulkDeleteModal">
                    Elimina
                </button>
            </div>
        </div>

        <div class="modal fade" id="bulkStatusModal" tabindex="-1" aria-labelledby="bulkStatusModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0 pb-0 admin-bulk-modal-header">
                        <h5 class="modal-title fw-bold" id="bulkStatusModalLabel">Cambia stato progetti</h5>
                        <button type="button"
                            class="btn btn-sm btn-ae btn-ae-square btn-ae-outline-secondary admin-bulk-modal-close"
                            data-bs-dismiss="modal" aria-label="Chiudi modale" title="Chiudi modale">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.projects.bulk-status') }}"
                        @submit="if (selectedProjectIds.length === 0 || selectedStatuses.length !== 1 || !bulkStatus) { $event.preventDefault(); }">
                        @csrf
                        <div class="modal-body pt-2">
                            <p class="text-body-secondary mb-3">
                                Selezionati: <strong x-text="selectedCount"></strong> progetti
                            </p>

                            <p class="small text-body-secondary mb-3" x-show="selectedStatuses.length === 1">
                                Stato attuale selezione: <strong x-text="getStatusLabel(selectedStatuses[0])"></strong>
                            </p>

                            <div class="alert alert-warning py-2 px-3 small mb-3" x-show="bulkWarningMessage"
                                x-text="bulkWarningMessage"></div>

                            <label for="bulk-status-select" class="form-label fw-semibold">Nuovo stato consentito</label>
                            <select id="bulk-status-select" name="status" x-model="bulkStatus"
                                class="form-select admin-bulk-status-select" aria-label="Nuovo stato progetti"
                                :disabled="selectedStatuses.length !== 1 || !bulkStatus" required>
                                <option value="" disabled>Seleziona stato</option>
                                <option value="published"
                                    :disabled="selectedStatuses.length !== 1 || selectedStatuses[0] !== 'draft'">Pubblicato
                                </option>
                                <option value="completed"
                                    :disabled="selectedStatuses.length !== 1 || selectedStatuses[0] !== 'published'">
                                    Completato</option>
                            </select>

                            <p class="small text-body-secondary mt-2 mb-0"
                                x-show="selectedStatuses.length === 1 && bulkStatus">
                                Transizione consentita: <strong x-text="getStatusLabel(selectedStatuses[0])"></strong> -&gt;
                                <strong x-text="getStatusLabel(bulkStatus)"></strong>
                            </p>

                            <template x-for="id in selectedProjectIds" :key="'modal-bulk-status-' + id">
                                <input type="hidden" name="project_ids[]" :value="id">
                            </template>
                        </div>

                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-ae btn-ae-square btn-ae-outline-secondary"
                                data-bs-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-ae btn-ae-square btn-ae-primary"
                                :disabled="selectedProjectIds.length === 0 || selectedStatuses.length !== 1 || !bulkStatus">Conferma</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0 pb-0 admin-bulk-modal-header">
                        <h5 class="modal-title fw-bold" id="bulkDeleteModalLabel">Elimina progetti</h5>
                        <button type="button"
                            class="btn btn-sm btn-ae btn-ae-square btn-ae-outline-secondary admin-bulk-modal-close"
                            data-bs-dismiss="modal" aria-label="Chiudi modale" title="Chiudi modale">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.projects.bulk-delete') }}"
                        @submit="if (selectedProjectIds.length === 0) { $event.preventDefault(); }">
                        @csrf
                        @method('DELETE')

                        <div class="modal-body pt-2">
                            <p class="mb-2">Confermi l'eliminazione dei progetti selezionati?</p>
                            <p class="text-body-secondary mb-0">
                                Questa azione rimuoverà <strong x-text="selectedCount"></strong> progetti.
                            </p>

                            <template x-for="id in selectedProjectIds" :key="'modal-bulk-delete-' + id">
                                <input type="hidden" name="project_ids[]" :value="id">
                            </template>
                        </div>

                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-ae btn-ae-square btn-ae-outline-secondary"
                                data-bs-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-ae btn-ae-square btn-ae-danger">Conferma elimina</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection