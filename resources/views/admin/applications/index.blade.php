@extends('layouts.master')

@section('page_title', 'Gestione Candidature - ' . $project->title)

@section('breadcrumb')
<div class="bg-light py-2">
    <div class="container">
        <x-breadcrumb>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.projects.index') }}" class="text-decoration-none">Progetti</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('project.show', ['project' => $project, 'adminContext' => 1]) }}"
                    class="text-decoration-none">{{ Str::limit($project->title, 35) }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Candidature</li>
        </x-breadcrumb>
    </div>
</div>
@endsection

@section('body')
@php
    $statusColors = [
        'pending'  => ['#f59e0b', 'bi-clock-history',     'In Attesa'],
        'approved' => ['#10b981', 'bi-check-circle-fill', 'Approvata'],
        'rejected' => ['#ef4444', 'bi-x-circle-fill',     'Rifiutata'],
    ];
    $categoryBadges = ['CES' => 'badge-prog-ces', 'SG' => 'badge-prog-sg', 'CF' => 'badge-prog-cf'];

    $pStatusMap = ['published' => ['Pubblicato','#10b981'], 'draft' => ['Bozza','#f59e0b'], 'completed' => ['Completato','#6b7280']];
    [$pLabel, $pColor] = $pStatusMap[$project->status] ?? [ucfirst($project->status), '#6b7280'];
@endphp

<div class="container mt-4 mb-5">

    {{-- ── HEADER ──────────────────────────────────────────────────── --}}
    <div class="row align-items-center g-3 mb-4">
        <div class="col-12">
            <h1 class="display-6 fw-bold text-dark mb-1">Gestione Candidature</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-folder2-open me-1"></i>
                <strong class="text-primary">{{ $project->title }}</strong>
                &mdash;
                Scadenza: <span class="fw-medium">{{ $project->expire_date ? $project->expire_date->format('d/m/Y') : 'Non specificata' }}</span>
                &mdash;
                <span class="badge rounded-pill px-3 py-1 text-white" style="background-color:{{ $pColor }};font-size:.8rem;">{{ $pLabel }}</span>
            </p>
        </div>
    </div>

    {{-- ── BARRA AVANZAMENTO POSTI ──────────────────────────────────── --}}
    @php
        $pct      = $project->requested_people > 0 ? min(100, round(($stats['approved'] / $project->requested_people) * 100)) : 0;
        $barColor = $pct >= 100 ? 'danger' : ($pct >= 70 ? 'warning' : 'success');
    @endphp
    <div class="bg-white rounded-4 shadow-sm p-3 mb-4">
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
            <p class="text-danger small mt-2 mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i>Limite raggiunto — impossibile approvare ulteriori candidature.</p>
        @elseif($stats['approved'] > 0)
            <p class="text-muted small mt-2 mb-0">Rimangono <strong>{{ $project->requested_people - $stats['approved'] }}</strong> posti disponibili.</p>
        @endif
    </div>

    {{-- ── FILTRI (identici ad all.blade.php e admin/projects) ────── --}}
    <div class="row mb-4">
        <div class="col-12">
            <form method="GET" action="{{ route('admin.applications.index', $project->id) }}"
                  class="bg-white rounded-4 shadow-sm p-3">
                <div class="row g-2 align-items-end">

                    <div class="col-12 col-lg-6">
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

                    <div class="col-6 col-lg-4">
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

                    <div class="col-6 col-lg-2 d-grid">
                        <a href="{{ route('admin.applications.index', $project->id) }}"
                           class="btn btn-ae btn-ae-square btn-ae-outline-secondary d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-x-lg me-1"></i>Cancella Filtri
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ── TABELLA (identica ad all.blade.php) ────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle admin-table-clean mb-0 d-none d-md-table">
                <thead class="bg-light">
                    <tr>
                        <th>Candidato</th>
                        <th class="d-none d-md-table-cell">Progetto</th>
                        <th class="d-none d-lg-table-cell">Tipo</th>
                        <th class="d-none d-lg-table-cell">Data di invio</th>
                        <th>Stato</th>
                        <th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        @php
                            [$stColor, $stIcon, $stLabel] = $statusColors[$application->status]
                                ?? ['#6b7280', 'bi-question-circle', ucfirst($application->status)];
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

                            <td class="d-none d-md-table-cell">
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
                                Nessuna candidatura disponibile.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── VISTA MOBILE (identica ad all.blade.php) ──────────── --}}
        <div class="d-md-none p-3 admin-mobile-list">
            @forelse($applications as $application)
                @php
                    [$stColor, $stIcon, $stLabel] = $statusColors[$application->status]
                        ?? ['#6b7280', 'bi-question-circle', ucfirst($application->status)];
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
                    Nessuna candidatura disponibile.
                </div>
            @endforelse
        </div>

        @if($applications->hasPages())
            <div class="d-flex justify-content-center py-3 border-top">
                {{ $applications->links() }}
            </div>
        @endif

    </div>{{-- /card --}}

</div>

{{-- ══ MODALI APPROVA / RIFIUTA / AGGIORNA ═══════════════════════════ --}}
@foreach($applications as $application)
    @php
        $remainingSpots = $project->requested_people - $stats['approved'];
        $limitReached   = $stats['approved'] >= $project->requested_people;
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
                                <div class="alert alert-warning py-2 small"><i class="bi bi-exclamation-triangle me-1"></i>Rimarranno solo <strong>{{ $remainingSpots - 1 }}</strong> posti disponibili.</div>
                            @endif
                            <div class="mb-0">
                                <label class="form-label fw-medium">Messaggio per il candidato <span class="text-muted fw-normal">(opzionale)</span></label>
                                <textarea class="form-control" name="admin_message" rows="3" placeholder="Messaggio personalizzato…">La tua candidatura è stata approvata! Ti contatteremo presto per i prossimi passi.</textarea>
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
                                <label class="form-label fw-medium">Motivo del rifiuto <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="admin_message" rows="3" required placeholder="Spiega i motivi del rifiuto…"></textarea>
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
                            <div class="d-flex gap-2 align-items-start p-3 rounded-3 mb-3" style="background:rgba(253,197,0,.1);border-left:4px solid #f59e0b">
                                <i class="bi bi-exclamation-triangle-fill text-warning fs-5 flex-shrink-0 mt-1"></i>
                                <p class="text-muted mb-0 small">Questa candidatura è già stata <strong>{{ $application->status === 'approved' ? 'approvata' : 'rifiutata' }}</strong>. Sei sicuro di voler modificare lo stato?</p>
                            </div>
                        @endif
                        <p>Candidatura di <strong>{{ $application->user->name }}</strong></p>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Nuovo stato <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" required>
                                @php $limitReachedForApprove = $limitReached && $application->status !== 'approved'; @endphp
                                <option value="pending"  {{ $application->status === 'pending'  ? 'selected' : '' }}>In Attesa</option>
                                <option value="approved" @selected($application->status === 'approved') @disabled($limitReachedForApprove)>Approvata{{ $limitReachedForApprove ? ' (limite raggiunto)' : '' }}</option>
                                <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rifiutata</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium">Messaggio per il candidato</label>
                            <textarea class="form-control" name="admin_message" rows="3" placeholder="Aggiorna il messaggio…">{{ $application->admin_message }}</textarea>
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