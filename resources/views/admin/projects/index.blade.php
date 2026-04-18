@extends('layouts.master')

@section('title', 'AE - Tutti i Progetti Admin')

@section('active_progetti', 'active')

@section('body')
    @php
        $projectsCollection = $projects ?? collect();
        $availableCountriesCollection = $availableCountries ?? collect();
        $categoryMap = [
            'CES' => ['icon' => 'heart-fill', 'label' => 'CES', 'class' => 'text-prog-ces'],
            'SG' => ['icon' => 'people-fill', 'label' => 'SG', 'class' => 'text-prog-sg'],
            'CF' => ['icon' => 'mortarboard-fill', 'label' => 'CF', 'class' => 'text-prog-cf'],
        ];
        $statusMap = [
            'completed' => ['label' => 'Completato', 'icon' => 'check-circle', 'color' => '#5b5bd6'],
            'draft' => ['label' => 'Bozza', 'icon' => 'pencil-square', 'color' => '#6c757d'],
            'published' => ['label' => 'Pubblicato', 'icon' => 'broadcast', 'color' => '#198754'],
        ];
    @endphp

    <div class="container py-5" x-data="{
        selectedCount: 0,
        selectAll: false,
        selectedProjectIds: [],
        bulkStatus: 'published',
        getActiveRowCheckboxes() {
            const rowCheckboxes = Array.from(this.$root.querySelectorAll('.project-row-checkbox:not([disabled])'));
            return rowCheckboxes.filter((checkbox) => checkbox.offsetParent !== null);
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

            const masterCheckbox = this.$root.querySelector('#projects-select-all');
            if (masterCheckbox) {
                masterCheckbox.indeterminate = false;
            }
        }
    }" x-init="updateCount()">

        <div class="row align-items-center g-3 mb-4">
            <div class="col-lg">
                <h1 class="display-6 fw-bold text-dark mb-0">Tutti i Progetti</h1>
            </div>
            <div class="col-lg-auto">
                <a href="{{ route('project.create', ['adminContext' => 1]) }}" class="btn btn-ae btn-ae-success btn-ae-square px-4 py-2">
                    <i class="bi bi-plus-lg me-2"></i>Crea Nuovo Progetto
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <form method="GET" action="{{ route('admin.projects.index') }}" class="bg-white rounded-4 shadow-sm p-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-lg-5">
                            <label for="project-search" class="form-label small text-body-secondary fw-semibold mb-1">Cerca
                                progetto</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-body-secondary"></i>
                                </span>
                                <input type="text" id="project-search" name="q"
                                    value="{{ request('q') }}" class="form-control border-start-0"
                                    placeholder="Titolo, paese o keyword...">
                            </div>
                        </div>

                        <div class="col-sm-4 col-lg-2">
                            <label for="project-status" class="form-label small text-body-secondary fw-semibold mb-1">Stato</label>
                            <select id="project-status" name="status" class="form-select" onchange="this.form.requestSubmit()">
                                <option value="">Tutti</option>
                                <option value="published" @selected(request('status') === 'published')>Pubblicato</option>
                                <option value="draft" @selected(request('status') === 'draft')>Bozza</option>
                                <option value="completed" @selected(request('status') === 'completed')>Completato</option>
                            </select>
                        </div>

                        <div class="col-sm-4 col-lg-2">
                            <label for="project-country" class="form-label small text-body-secondary fw-semibold mb-1">Paese</label>
                            <select id="project-country" name="country" class="form-select" onchange="this.form.requestSubmit()">
                                <option value="">Tutti</option>
                                @foreach ($availableCountriesCollection as $country)
                                    <option value="{{ $country }}" @selected(request('country') === $country)>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-4 col-lg-2">
                            <label for="project-deadline"
                                class="form-label small text-body-secondary fw-semibold mb-1">Scadenza</label>
                            <select id="project-deadline" name="deadline" class="form-select" onchange="this.form.requestSubmit()">
                                <option value="">Tutte</option>
                                <option value="7" @selected(request('deadline') === '7')>Entro 7 giorni</option>
                                <option value="30" @selected(request('deadline') === '30')>Entro 30 giorni</option>
                                <option value="expired" @selected(request('deadline') === 'expired')>Gia scaduti</option>
                            </select>
                        </div>

                        <div class="col-12 col-lg-1 d-grid">
                            <a href="{{ route('admin.projects.index') }}"
                                class="btn btn-ae btn-ae-square btn-ae-outline-secondary d-inline-flex align-items-center">
                                <i class="bi bi-x-lg me-1"></i>Resetta
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle admin-table-clean mb-0 d-none d-md-table">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-3">
                                <input id="projects-select-all" type="checkbox" class="form-check-input"
                                    x-model="selectAll" @change="toggleAll($event)" aria-label="Seleziona tutti i progetti">
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
                                    'icon' => 'tag-fill',
                                    'label' => $categoryTag ?: 'N/D',
                                    'class' => 'text-secondary',
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
                                        value="{{ $projectId }}" @change="updateCount" @disabled(!$projectId)
                                        aria-label="Seleziona progetto">
                                </td>
                                <td class="fw-semibold">{{ $projectTitle }}</td>
                                <td class="text-center">
                                    <span class="d-inline-flex align-items-center gap-1 {{ $categoryConfig['class'] }} fw-semibold">
                                        <i class="bi bi-{{ $categoryConfig['icon'] }}"></i>
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
                                    <span class="rounded-pill px-3 py-1 text-white d-inline-flex align-items-center gap-1"
                                        style="background-color: {{ $statusConfig['color'] }};">
                                        <i class="bi bi-{{ $statusConfig['icon'] }}"></i>
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="{{ $showUrl }}"
                                            class="btn btn-sm btn-ae btn-ae-square admin-project-action-view"
                                            title="Visualizza progetto"
                                            aria-label="Visualizza progetto">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if ($isCompleted)
                                            <button type="button"
                                                class="btn btn-sm btn-ae btn-ae-square btn-ae-outline-secondary opacity-50"
                                                disabled title="Progetto completato non modificabile"
                                                aria-label="Modifica non disponibile">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @else
                                            <a href="{{ $editUrl }}"
                                                class="btn btn-sm btn-ae btn-ae-square admin-project-action-edit"
                                                title="Modifica progetto"
                                                aria-label="Modifica progetto">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        <span class="vr admin-project-action-divider mx-1" aria-hidden="true"></span>
                                        <a href="{{ $deleteUrl }}"
                                            class="btn btn-sm btn-ae btn-ae-square btn-ae-outline-danger"
                                            title="Elimina progetto"
                                            aria-label="Elimina progetto">
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
                            'icon' => 'tag-fill',
                            'label' => $categoryTag ?: 'N/D',
                            'class' => 'text-secondary',
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
                                <input type="checkbox" class="form-check-input project-row-checkbox"
                                    value="{{ $projectId }}" @change="updateCount" @disabled(!$projectId)
                                    aria-label="Seleziona progetto">
                                <h3 class="h6 fw-bold mb-0 admin-mobile-title">{{ $projectTitle }}</h3>
                            </div>
                            <span class="rounded-pill px-3 py-1 text-white d-inline-flex align-items-center gap-1 small admin-mobile-status-badge"
                                style="background-color: {{ $statusConfig['color'] }};">
                                <i class="bi bi-{{ $statusConfig['icon'] }}"></i>
                                {{ $statusConfig['label'] }}
                            </span>
                        </div>

                        <div class="mb-2 d-flex align-items-center gap-2 admin-mobile-meta-row">
                            <span class="d-inline-flex align-items-center gap-1 {{ $categoryConfig['class'] }} fw-semibold small">
                                <i class="bi bi-{{ $categoryConfig['icon'] }}"></i>
                                {{ $categoryConfig['label'] }}
                            </span>
                        </div>

                        <p class="mb-1 small admin-mobile-meta"><span class="text-body-secondary">Paese:</span> {{ $projectLocation }}</p>
                        <p class="mb-2 small admin-mobile-meta"><span class="text-body-secondary">Scadenza:</span> {{ $deadlineText }}</p>

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
                        {{ $projects->links() }}
                    @endif
                </div>
            </div>
        </div>

        <div class="position-fixed bottom-0 start-50 translate-middle-x mb-4 z-3" x-show="selectedCount > 0"
            x-transition style="display: none;">
            <div class="shadow-lg bg-white p-2 px-3 d-flex align-items-center gap-3 rounded-4 border">
                <button type="button" class="btn btn-sm btn-ae btn-ae-square btn-ae-outline-secondary"
                    @click="clearSelection" aria-label="Annulla selezione" title="Annulla selezione">
                    <i class="bi bi-x-lg"></i>
                </button>
                <span class="small fw-semibold"><span x-text="selectedCount"></span> progetti selezionati</span>
                <div class="vr"></div>

                <button type="button" class="btn btn-sm btn-ae btn-ae-square btn-ae-primary"
                    data-bs-toggle="modal" data-bs-target="#bulkStatusModal">
                    Cambia Stato
                </button>

                <button type="button" class="btn btn-sm btn-ae btn-ae-square btn-ae-danger"
                    data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                    Elimina
                </button>
            </div>
        </div>

        <div class="modal fade" id="bulkStatusModal" tabindex="-1" aria-labelledby="bulkStatusModalLabel" aria-hidden="true">
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
                        @submit="if (selectedProjectIds.length === 0) { $event.preventDefault(); }">
                        @csrf
                        <div class="modal-body pt-2">
                            <p class="text-body-secondary mb-3">
                                Selezionati: <strong x-text="selectedCount"></strong> progetti
                            </p>

                            <label for="bulk-status-select" class="form-label fw-semibold">Nuovo stato</label>
                            <select id="bulk-status-select" name="status" x-model="bulkStatus"
                                class="form-select admin-bulk-status-select" aria-label="Nuovo stato progetti">
                                <option value="published">Pubblicato</option>
                                <option value="draft">Bozza</option>
                                <option value="completed">Completato</option>
                            </select>

                            <template x-for="id in selectedProjectIds" :key="'modal-bulk-status-' + id">
                                <input type="hidden" name="project_ids[]" :value="id">
                            </template>
                        </div>

                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-ae btn-ae-square btn-ae-outline-secondary"
                                data-bs-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-ae btn-ae-square btn-ae-primary">Conferma</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
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
                                Questa azione rimuovera <strong x-text="selectedCount"></strong> progetti.
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
