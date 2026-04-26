@extends('layouts.master')

@section('page_title', 'Tutte le Candidature')

@section('active_candidature', 'active')

@section('body')
    @php
        $categoryMap = [
            'CES' => ['label' => 'CES', 'badge' => 'badge-prog-ces'],
            'SG' => ['label' => 'SG', 'badge' => 'badge-prog-sg'],
            'CF' => ['label' => 'CF', 'badge' => 'badge-prog-cf'],
        ];

        $statusMap = [
            'pending' => ['label' => 'In Attesa', 'icon' => 'bi-hourglass-split', 'class' => 'text-warning'],
            'approved' => ['label' => 'Approvata', 'icon' => 'bi-check-circle', 'class' => 'text-success'],
            'rejected' => ['label' => 'Rifiutata', 'icon' => 'bi-x-circle', 'class' => 'text-danger'],
        ];
    @endphp

    <div class="container py-5">

        {{-- ── HEADER ──────────── --}}
        <div class="row align-items-center g-3 mb-4">
            <div class="col-lg">
                <h1 class="fw-bold text-dark mb-1">Candidature</h1>
                <p class="text-muted mb-0">Panoramica globale di tutte le candidature ricevute.</p>
            </div>
        </div>

        {{-- ── FILTRI ──────────── --}}
        <div class="row mb-4">
            <div class="col-12">
                <form method="GET" action="{{ route('admin.applications.all') }}" class="bg-white rounded-4 shadow-sm p-3">
                    <div class="row g-2 align-items-end">

                        {{-- Barra di Ricerca (Sempre visibile) --}}
                        <div class="col-12 col-lg-4">
                            <label for="app-search" class="form-label small text-body-secondary fw-semibold mb-1">Cerca
                                candidato</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-body-secondary"></i>
                                </span>
                                <input type="text" id="app-search" name="search" value="{{ request('search') }}"
                                    class="form-control border-start-0" placeholder="Nome o email..."
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
                        <div class="col-12 col-lg-8">
                            <div class="collapse d-lg-block" id="adminFilters">
                                <div class="row g-2 align-items-end mt-2 mt-lg-0">

                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label for="app-project"
                                            class="form-label small text-body-secondary fw-semibold mb-1">Progetto</label>
                                        <select id="app-project" name="project_id" class="form-select"
                                            onchange="this.form.requestSubmit()">
                                            <option value="">Tutti</option>
                                            @foreach($projects as $proj)
                                                <option value="{{ $proj->id }}" @selected(request('project_id') == $proj->id)>
                                                    {{ Str::limit($proj->title, 35) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label for="app-status"
                                            class="form-label small text-body-secondary fw-semibold mb-1">Stato</label>
                                        <select id="app-status" name="status" class="form-select"
                                            onchange="this.form.requestSubmit()">
                                            <option value="">Tutti</option>
                                            <option value="pending" @selected(request('status') === 'pending')>In Attesa
                                            </option>
                                            <option value="approved" @selected(request('status') === 'approved')>Approvate
                                            </option>
                                            <option value="rejected" @selected(request('status') === 'rejected')>Rifiutate
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-lg-4 d-grid mt-3 mt-lg-0">
                                        <a href="{{ route('admin.applications.all') }}"
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
                        $hasHiddenFilters = request()->filled('project_id') || request()->filled('status') || request()->filled('search');
                    @endphp
                    @if ($hasHiddenFilters)
                        <div class="mt-3 pt-3 border-top d-lg-none">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="small fw-semibold text-body-secondary me-1">Filtri attivi:</span>

                                @if (request()->filled('search'))
                                    @php $removeSearchParams = request()->except('search', 'page'); @endphp
                                    <span
                                        class="badge bg-light border text-dark rounded-pill py-2 px-3 d-inline-flex align-items-center gap-2 shadow-sm">
                                        <span class="text-body-secondary fw-normal">Testo:</span> {{ request('search') }}
                                        <a href="{{ route('admin.applications.all', $removeSearchParams) }}"
                                            class="text-dark opacity-50 text-decoration-none" aria-label="Rimuovi filtro"><i
                                                class="bi bi-x-circle-fill"></i></a>
                                    </span>
                                @endif

                                @if (request()->filled('project_id'))
                                    @php
                                        $removeProjectParams = request()->except('project_id', 'page');
                                        $activeProject = collect($projects)->firstWhere('id', (int) request('project_id'));
                                    @endphp
                                    @if ($activeProject)
                                        <span
                                            class="badge bg-light border text-dark rounded-pill py-2 px-3 d-inline-flex align-items-center gap-2 shadow-sm">
                                            <span class="text-body-secondary fw-normal">Prog:</span>
                                            {{ Str::limit($activeProject->title, 15) }}
                                            <a href="{{ route('admin.applications.all', $removeProjectParams) }}"
                                                class="text-dark opacity-50 text-decoration-none" aria-label="Rimuovi filtro"><i
                                                    class="bi bi-x-circle-fill"></i></a>
                                        </span>
                                    @endif
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
                                        <a href="{{ route('admin.applications.all', $removeStatusParams) }}"
                                            class="text-dark opacity-50 text-decoration-none" aria-label="Rimuovi filtro"><i
                                                class="bi bi-x-circle-fill"></i></a>
                                    </span>
                                @endif

                                <a href="{{ route('admin.applications.all') }}"
                                    class="btn btn-link text-danger text-decoration-none btn-sm ms-auto py-0">Svuota</a>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- ── TABELLA DESKTOP ──────────── --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle admin-table-clean mb-0 d-none d-md-table">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-4">Candidato</th>
                            <th scope="col">Progetto</th>
                            <th scope="col" class="text-center">Categoria</th>
                            <th scope="col">Data Inoltro</th>
                            <th scope="col" class="text-center">Stato</th>
                            <th scope="col" class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            @php
                                $userName = $application->user->name ?? 'Utente Sconosciuto';
                                $userInitial = strtoupper(substr($userName, 0, 1));

                                $catTag = strtoupper((string) data_get($application, 'project.category.tag', 'CES'));
                                $categoryConfig = $categoryMap[$catTag] ?? ['label' => $catTag ?: 'N/D', 'badge' => 'badge-prog-ces'];

                                $statusStr = strtolower((string) $application->status);
                                $statusConfig = $statusMap[$statusStr] ?? ['label' => ucfirst($statusStr), 'icon' => 'bi-question-circle', 'class' => 'text-secondary'];
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
                                    <span
                                        class="fw-medium">{{ Str::limit(data_get($application, 'project.title', 'N/D'), 38) }}</span>
                                </td>

                                <td class="text-center">
                                    <span
                                        class="{{ $categoryConfig['badge'] }} shadow-sm d-inline-flex align-items-center justify-content-center">
                                        {{ $categoryConfig['label'] }}
                                    </span>
                                </td>

                                <td>
                                    {{ $application->created_at ? $application->created_at->format('d/m/Y') : 'N/D' }}
                                </td>

                                <td class="text-center">
                                    <span
                                        class="badge rounded-pill bg-light border px-3 py-2 {{ $statusConfig['class'] }} d-inline-flex align-items-center gap-1"
                                        style="font-size: 0.85rem;">
                                        <i class="bi {{ $statusConfig['icon'] }}"></i>
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>

                                <td class="text-end pe-4">
                                    <i class="bi bi-chevron-right text-muted fs-5"></i>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    @if(request()->hasAny(['search', 'project_id', 'status']))
                                        Nessun risultato per i filtri applicati.
                                    @else
                                        Non è ancora stata inviata alcuna candidatura.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── VISTA MOBILE ──────────── --}}
            <div class="d-md-none p-3 admin-mobile-list admin-mobile-projects-list">
                @forelse($applications as $application)
                    @php
                        $userName = $application->user->name ?? 'Utente Sconosciuto';
                        $userInitial = strtoupper(substr($userName, 0, 1));

                        $catTag = strtoupper((string) data_get($application, 'project.category.tag', 'CES'));
                        $categoryConfig = $categoryMap[$catTag] ?? ['label' => $catTag ?: 'N/D', 'badge' => 'badge-prog-ces'];

                        $statusStr = strtolower((string) $application->status);
                        $statusConfig = $statusMap[$statusStr] ?? ['label' => ucfirst($statusStr), 'icon' => 'bi-question-circle', 'class' => 'text-secondary'];
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
                                class="badge rounded-pill bg-light border px-3 py-2 {{ $statusConfig['class'] }} shadow-sm d-inline-flex align-items-center gap-1 small admin-mobile-status-badge flex-shrink-0"
                                style="font-size: 0.85rem;">
                                <i class="bi {{ $statusConfig['icon'] }}"></i>
                                <span class="d-none d-sm-inline">{{ $statusConfig['label'] }}</span>
                            </span>
                        </div>

                        <div class="mb-2 d-flex align-items-center gap-2 admin-mobile-meta-row">
                            <span
                                class="{{ $categoryConfig['badge'] }} shadow-sm d-inline-flex align-items-center justify-content-center">
                                {{ $categoryConfig['label'] }}
                            </span>
                        </div>

                        <p class="mb-1 small admin-mobile-meta">
                            <span class="text-body-secondary">Progetto:</span>
                            {{ Str::limit(data_get($application, 'project.title', 'N/D'), 45) }}
                        </p>
                        <p class="mb-2 small admin-mobile-meta">
                            <span class="text-body-secondary">Inviata il:</span>
                            {{ $application->created_at ? $application->created_at->format('d/m/Y') : 'N/D' }}
                        </p>

                        <div class="d-flex justify-content-end align-items-center mt-2 border-top pt-2">
                            <span class="text-primary small fw-semibold">Vedi dettagli <i
                                    class="bi bi-chevron-right ms-1"></i></span>
                        </div>
                    </a>
                @empty
                    <div class="py-4 text-center text-muted">
                        @if(request()->hasAny(['search', 'project_id', 'status']))
                            Nessun risultato per i filtri applicati.
                        @else
                            Non è ancora stata inviata alcuna candidatura.
                        @endif
                    </div>
                @endforelse
            </div>

            {{-- ── FOOTER TABELLA / PAGINAZIONE ──────────── --}}
            <div class="card-footer bg-white border-0 border-top py-3">
                <div class="d-flex justify-content-center">
                    @if (isset($applications) && method_exists($applications, 'links'))
                        {{ $applications->links() }}
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection