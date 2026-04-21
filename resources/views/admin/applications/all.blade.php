@extends('layouts.master')

@section('page_title', 'Tutte le Candidature')

@section('breadcrumb')
    <div class="bg-light py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Candidature</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('body')
<div class="container mt-4 mb-5">

    {{-- ── HEADER ──────────────────────────────────────────────── --}}
    <div class="mb-4">
        <h1 class="mb-1">Tutte le Candidature</h1>
        <p class="text-muted mb-0">Panoramica globale di tutte le candidature ricevute.</p>
    </div>

    {{-- ── CARD STATISTICHE ────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @php
            $statCards = [
                ['icon' => 'bi-people-fill',       'value' => $stats['total'],    'label' => 'Totali',    'color' => 'primary',  'filter' => ''],
                ['icon' => 'bi-clock-history',     'value' => $stats['pending'],  'label' => 'In Attesa', 'color' => 'warning',  'filter' => 'pending'],
                ['icon' => 'bi-check-circle-fill', 'value' => $stats['approved'], 'label' => 'Approvate', 'color' => 'success',  'filter' => 'approved'],
                ['icon' => 'bi-x-circle-fill',     'value' => $stats['rejected'], 'label' => 'Rifiutate', 'color' => 'danger',   'filter' => 'rejected'],
            ];
        @endphp
        @foreach($statCards as $sc)
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.applications.all', array_merge(request()->query(), ['status' => $sc['filter']])) }}"
                   class="card border-0 shadow-sm text-decoration-none stat-card {{ request('status') === $sc['filter'] ? 'stat-card--active' : '' }}">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:44px;height:44px;background:rgba(var(--bs-{{ $sc['color'] }}-rgb),.12)">
                            <i class="bi {{ $sc['icon'] }} fs-5 text-{{ $sc['color'] }}"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 lh-1" style="color:var(--bs-primary)">{{ $sc['value'] }}</div>
                            <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.04em">
                                {{ $sc['label'] }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- ── FILTRI ───────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.applications.all') }}"
                  class="d-flex flex-wrap gap-2 align-items-end">

                {{-- Ricerca --}}
                <div class="flex-grow-1" style="min-width:200px;">
                    <label for="search" class="form-label small fw-medium mb-1">
                        <i class="bi bi-search me-1"></i>Candidato
                    </label>
                    <input type="text" id="search" name="search"
                           class="form-control form-control-sm"
                           placeholder="Nome o email…"
                           value="{{ request('search') }}">
                </div>

                {{-- Filtro progetto --}}
                <div style="min-width:200px;">
                    <label for="project_id" class="form-label small fw-medium mb-1">
                        <i class="bi bi-folder me-1"></i>Progetto
                    </label>
                    <select id="project_id" name="project_id" class="form-select form-select-sm">
                        <option value="">Tutti i progetti</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}"
                                {{ request('project_id') == $proj->id ? 'selected' : '' }}>
                                {{ $proj->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro stato --}}
                <div>
                    <label for="status" class="form-label small fw-medium mb-1">
                        <i class="bi bi-flag me-1"></i>Stato
                    </label>
                    <select id="status" name="status" class="form-select form-select-sm">
                        <option value="">Tutti gli stati</option>
                        <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>In Attesa</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approvate</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rifiutate</option>
                    </select>
                </div>

                {{-- Bottoni — Reimposta compare se almeno un filtro ha un valore --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-ae btn-ae-primary btn-sm">
                        <i class="bi bi-funnel me-1"></i>Filtra
                    </button>
                    @if(request('search') || request('project_id') || request('status'))
                        <a href="{{ route('admin.applications.all') }}"
                           class="btn btn-ae btn-ae-outline-secondary btn-sm">
                            <i class="bi bi-x-lg me-1"></i>Reimposta
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── TABELLA ───────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2 text-primary"></i>
                Candidature
                <span class="badge bg-primary ms-1">{{ $applications->total() }}</span>
            </h5>
            @if($applications->total() > 0)
                <span class="text-muted small">
                    Pagina {{ $applications->currentPage() }} di {{ $applications->lastPage() }}
                </span>
            @endif
        </div>

        <div class="card-body p-0">
            @if($applications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Candidato</th>
                                <th class="d-none d-md-table-cell">Progetto</th>
                                <th class="d-none d-lg-table-cell">Data</th>
                                <th>Stato</th>
                                <th style="width:100px">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                @php
                                    $statusMap = [
                                        'pending'  => ['bg-warning text-dark', 'bi-clock-history',     'In Attesa'],
                                        'approved' => ['bg-success',           'bi-check-circle-fill', 'Approvata'],
                                        'rejected' => ['bg-danger',            'bi-x-circle-fill',     'Rifiutata'],
                                    ];
                                    [$badgeClass, $icon, $label] = $statusMap[$application->status]
                                        ?? ['bg-secondary', 'bi-question-circle', ucfirst($application->status)];
                                @endphp
                                <tr>
                                    {{-- Candidato --}}
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

                                    {{-- Progetto --}}
                                    <td class="d-none d-md-table-cell">
                                        <a href="{{ route('admin.applications.index', $application->project_id) }}"
                                           class="text-decoration-none fw-medium text-primary">
                                            {{ Str::limit($application->project->title, 40) }}
                                        </a>
                                        @if($application->project->category)
                                            <div>
                                                <small class="text-muted">
                                                    {{ $application->project->category->name }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Data --}}
                                    <td class="d-none d-lg-table-cell">
                                        <span class="text-muted small">
                                            {{ $application->created_at->format('d/m/Y') }}
                                        </span>
                                    </td>

                                    {{-- Stato --}}
                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                                        </span>
                                    </td>

                                    {{-- Azioni --}}
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.applications.show', $application) }}"
                                               class="btn btn-ae btn-ae-outline-primary btn-sm"
                                               title="Visualizza dettagli"
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.applications.index', $application->project_id) }}"
                                               class="btn btn-ae btn-ae-outline-secondary btn-sm"
                                               title="Vai alle candidature del progetto"
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-folder2-open"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                    <h5 class="text-muted">Nessuna candidatura trovata</h5>
                    @if(request()->hasAny(['search', 'project_id', 'status']))
                        <p class="text-muted small mb-3">
                            Nessun risultato per i filtri applicati.
                        </p>
                        <a href="{{ route('admin.applications.all') }}"
                           class="btn btn-ae btn-ae-outline-secondary btn-sm">
                            <i class="bi bi-x-lg me-1"></i>Reimposta filtri
                        </a>
                    @else
                        <p class="text-muted small">
                            Non è ancora stata inviata alcuna candidatura.
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>

</div>

<style>
    .stat-card {
        border: 2px solid transparent !important;
        transition: transform .15s, box-shadow .15s, border-color .15s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,29,61,.1) !important;
    }
    .stat-card--active {
        border-color: var(--bs-primary) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el, { placement: 'top', trigger: 'hover' });
        });
    });
</script>
@endsection