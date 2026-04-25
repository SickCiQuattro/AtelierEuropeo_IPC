@extends('layouts.master')

@section('page_title', 'Tutte le Candidature')

@section('body')
<div class="container mt-4 mb-5">

    {{-- ── HEADER (centrato, identico ad admin progetti) ──────────── --}}
    <div class="row align-items-center g-3 mb-4">
        <div class="col-12 text-center">
            <h1 class="display-6 fw-bold text-dark mb-1">Tutte le Candidature</h1>
            <p class="text-muted mb-0">Panoramica globale di tutte le candidature ricevute.</p>
        </div>
    </div>

    {{-- ── FILTRI (struttura e stile identici ad admin/projects/index) ── --}}
    <div class="row mb-4">
        <div class="col-12">
            <form method="GET" action="{{ route('admin.applications.all') }}"
                  class="bg-white rounded-4 shadow-sm p-3">
                <div class="row g-2 align-items-end">

                    <div class="col-12 col-lg-4">
                        <label for="app-search"
                               class="form-label small text-body-secondary fw-semibold mb-1">
                            Cerca candidato
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-body-secondary"></i>
                            </span>
                            <input type="text" id="app-search" name="search"
                                   class="form-control border-start-0"
                                   placeholder="Nome o email…"
                                   value="{{ request('search') }}"
                                   onchange="this.form.requestSubmit()">
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <label for="app-project"
                               class="form-label small text-body-secondary fw-semibold mb-1">
                            Progetto
                        </label>
                        <select id="app-project" name="project_id" class="form-select"
                                onchange="this.form.requestSubmit()">
                            <option value="">Tutti i progetti</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}"
                                    {{ request('project_id') == $proj->id ? 'selected' : '' }}>
                                    {{ $proj->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-lg-3">
                        <label for="app-status"
                               class="form-label small text-body-secondary fw-semibold mb-1">
                            Stato
                        </label>
                        <select id="app-status" name="status" class="form-select"
                                onchange="this.form.requestSubmit()">
                            <option value="">Tutti gli stati</option>
                            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>In Attesa</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approvate</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rifiutate</option>
                        </select>
                    </div>

                    <div class="col-12 col-lg-2 d-grid">
                        <a href="{{ route('admin.applications.all') }}"
                           class="btn btn-ae btn-ae-square btn-ae-outline-secondary d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-x-lg me-1"></i>Cancella Filtri
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ── TABELLA desktop (identica struttura admin progetti) ────── --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle admin-table-clean mb-0 d-none d-md-table">
                <thead class="bg-light">
                    <tr>
                        <th>Candidato</th>
                        <th>Progetto</th>
                        <th class="d-none d-lg-table-cell">Tipo</th>
                        <th class="d-none d-lg-table-cell">Data di invio</th>
                        <th>Stato</th>
                        <th class="text-end pe-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        @php
                            $statusMap = [
                                'pending'  => ['bi-clock-history',     'In Attesa', '#f59e0b'],
                                'approved' => ['bi-check-circle-fill', 'Approvata', '#10b981'],
                                'rejected' => ['bi-x-circle-fill',     'Rifiutata', '#ef4444'],
                            ];
                            [$stIcon, $stLabel, $stColor] = $statusMap[$application->status]
                                ?? ['bi-question-circle', ucfirst($application->status), '#6b7280'];

                            $categoryBadges = ['CES' => 'badge-prog-ces', 'SG' => 'badge-prog-sg', 'CF' => 'badge-prog-cf'];
                            $catTag   = $application->project->category->tag ?? null;
                            $catClass = $catTag ? ($categoryBadges[$catTag] ?? '') : '';
                        @endphp
                        <tr class="admin-clickable-row"
                            data-href="{{ route('admin.applications.show', $application) }}"
                            onclick="window.location.href=this.dataset.href">

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                         style="width:36px;height:36px;font-size:.85rem">
                                        {{ strtoupper(substr($application->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold lh-1">{{ $application->user->name }}</div>
                                        <small class="text-muted">{{ $application->user->email }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="fw-medium">{{ Str::limit($application->project->title, 38) }}</span>
                            </td>

                            <td class="d-none d-lg-table-cell">
                                @if($catTag && $catClass)
                                    <button type="button" class="{{ $catClass }} position-relative z-3"
                                            style="pointer-events:none;">{{ $catTag }}</button>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            <td class="d-none d-lg-table-cell">
                                <span class="text-muted small">{{ $application->created_at->format('d/m/Y') }}</span>
                            </td>

                            <td>
                                <span class="rounded-pill px-3 py-1 text-white d-inline-flex align-items-center gap-1"
                                      style="background-color:{{ $stColor }};font-size:.82rem;">
                                    <i class="bi {{ $stIcon }}"></i>{{ $stLabel }}
                                </span>
                            </td>

                            <td class="text-end pe-3">
                                <i class="bi bi-chevron-right text-muted"></i>
                            </td>
                        </tr>
                    @empty
                        <tr class="admin-empty-row">
                            <td colspan="6" class="text-center text-muted py-4">
                                @if(request()->hasAny(['search','project_id','status']))
                                    Nessun risultato per i filtri applicati.
                                    <a href="{{ route('admin.applications.all') }}" class="btn btn-link btn-sm p-0 ms-1">Cancella Filtri</a>
                                @else
                                    Non è ancora stata inviata alcuna candidatura.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── VISTA MOBILE (identica struttura admin progetti) ──── --}}
        <div class="d-md-none p-3 admin-mobile-list">
            @forelse($applications as $application)
                @php
                    [$stIcon, $stLabel, $stColor] = $statusMap[$application->status]
                        ?? ['bi-question-circle', ucfirst($application->status), '#6b7280'];
                    $catTag   = $application->project->category->tag ?? null;
                    $catClass = $catTag ? ($categoryBadges[$catTag] ?? '') : '';
                @endphp
                <a href="{{ route('admin.applications.show', $application) }}"
                   class="admin-mobile-item admin-mobile-item-link text-decoration-none text-dark d-flex align-items-center gap-3">
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2 min-w-0">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                     style="width:32px;height:32px;font-size:.8rem">
                                    {{ strtoupper(substr($application->user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="fw-semibold admin-mobile-title lh-1">{{ $application->user->name }}</div>
                                    <small class="text-muted text-truncate d-block">{{ $application->user->email }}</small>
                                </div>
                            </div>
                            <span class="rounded-pill px-2 py-1 text-white d-inline-flex align-items-center gap-1 admin-mobile-status-badge flex-shrink-0"
                                  style="background-color:{{ $stColor }}">
                                <i class="bi {{ $stIcon }}"></i>{{ $stLabel }}
                            </span>
                        </div>

                        <p class="mb-1 small admin-mobile-meta">
                            <span class="text-body-secondary">Progetto:</span>
                            {{ Str::limit($application->project->title, 45) }}
                        </p>
                        @if($catTag && $catClass)
                            <div class="mb-1">
                                <button type="button" class="{{ $catClass }} position-relative z-3"
                                        style="pointer-events:none;">{{ $catTag }}</button>
                            </div>
                        @endif
                        <p class="mb-0 small admin-mobile-meta">
                            <span class="text-body-secondary">Inviata il:</span>
                            {{ $application->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <i class="bi bi-chevron-right admin-mobile-chevron flex-shrink-0"></i>
                </a>
            @empty
                <div class="admin-mobile-empty-state text-center py-4 text-muted">
                    @if(request()->hasAny(['search','project_id','status']))
                        Nessun risultato per i filtri applicati.
                    @else
                        Non è ancora stata inviata alcuna candidatura.
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Paginazione --}}
        @if($applications->hasPages())
            <div class="d-flex justify-content-center py-3 border-top">
                {{ $applications->links() }}
            </div>
        @endif

    </div>{{-- /card --}}

</div>
@endsection