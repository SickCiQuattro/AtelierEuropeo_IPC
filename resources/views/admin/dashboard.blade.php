@extends('layouts.master')

@section('title', 'AE - Dashboard Admin')

@section('body')
    <div class="container py-5">
        <div class="row align-items-center g-3 mb-5">
            <div class="col-lg">
                <h1 class="display-6 fw-bold text-dark mb-0">Bentornato, {{ auth()->user()->name ?? 'Daniele' }}</h1>
            </div>
            <div class="col-lg-auto">
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-lg-end">
                    <a href="#" class="btn btn-ae btn-ae-square btn-ae-outline-secondary px-4 py-2">
                        <i class="bi bi-download me-2"></i>Scarica Report Mensile
                    </a>
                    <a href="{{ route('project.create') }}" class="btn btn-success btn-ae btn-ae-square px-4 py-2">
                        <i class="bi bi-plus-lg me-2"></i>Crea Nuovo Progetto
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-5">
            <div class="col-md-6 col-lg-3">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 p-3 d-flex flex-row align-items-center gap-3 admin-kpi-card">
                    <i class="bi bi-folder-fill admin-kpi-icon-active fs-1"></i>
                    <div>
                        <div class="fs-2 fw-bold lh-1 text-dark mb-1">{{ $activeProjectsCount }}</div>
                        <div class="small text-body-secondary">Progetti Attivi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 p-3 d-flex flex-row align-items-center gap-3 admin-kpi-card">
                    <i class="bi bi-person-arms-up admin-kpi-icon-pending fs-1"></i>
                    <div>
                        <div class="fs-2 fw-bold lh-1 text-dark mb-1">{{ $pendingApplicationsCount }}</div>
                        <div class="small text-body-secondary">Candidature in Sospeso</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 p-3 d-flex flex-row align-items-center gap-3 admin-kpi-card">
                    <i class="bi bi-calendar-event-fill admin-kpi-icon-expiring fs-1"></i>
                    <div>
                        <div class="fs-2 fw-bold lh-1 text-dark mb-1">{{ $expiringProjectsCount }}</div>
                        <div class="small text-body-secondary">Progetti in Scadenza</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 p-3 d-flex flex-row align-items-center gap-3 admin-kpi-card">
                    <i class="bi bi-pencil-square text-secondary fs-1"></i>
                    <div>
                        <div class="fs-2 fw-bold lh-1 text-dark mb-1">{{ $draftProjectsCount }}</div>
                        <div class="small text-body-secondary">Progetti in Bozza</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 d-flex flex-column">
                    <h2 class="h4 fw-bold text-center mb-4">Ultime Candidature da Valutare</h2>
                    <div class="table-responsive flex-grow-1">
                        <table class="table table-hover align-middle mb-0 admin-table-clean">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-body-secondary small text-uppercase fw-semibold">Nome
                                        Candidato</th>
                                    <th scope="col" class="text-body-secondary small text-uppercase fw-semibold">Progetto
                                    </th>
                                    <th scope="col" class="text-body-secondary small text-uppercase fw-semibold">Data di
                                        invio</th>
                                    <th scope="col" class="text-end text-body-secondary small text-uppercase fw-semibold">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestPendingApplications as $application)
                                    <tr class="admin-clickable-row" role="link" tabindex="0"
                                        onclick="window.location.href='{{ route('admin.applications.show', $application->id) }}'"
                                        onkeydown="if(event.key === 'Enter' || event.key === ' '){ event.preventDefault(); window.location.href='{{ route('admin.applications.show', $application->id) }}'; }">
                                        <td class="fw-semibold">{{ $application->user->name ?? 'Utente Sconosciuto' }}</td>
                                        <td>{{ $application->project->title ?? 'Progetto Rimosso' }}</td>
                                        <td>{{ $application->created_at->format('d/m/Y') }}</td>
                                        <td class="text-end">
                                            <i class="bi bi-chevron-right text-body-secondary"></i>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="admin-empty-row">
                                        <td colspan="4" class="text-center text-muted">Nessuna candidatura in sospeso.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-auto pt-3 border-top">
                        @php
                            // TODO: abilita la route dedicata quando sara creata (es. admin.applications.list)
                            $allApplicationsRouteName = 'admin.applications.list';
                            $hasAllApplicationsPage = \Illuminate\Support\Facades\Route::has($allApplicationsRouteName);
                        @endphp
                        <a href="{{ $hasAllApplicationsPage ? route($allApplicationsRouteName) : '#' }}"
                            class="admin-list-link fw-semibold text-decoration-none d-inline-flex align-items-center gap-1 {{ $hasAllApplicationsPage ? '' : 'disabled opacity-50 pe-none' }}"
                            @if (!$hasAllApplicationsPage) aria-disabled="true" tabindex="-1" title="Pagina in arrivo"
                            @endif>
                            Vedi tutte le candidature <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 d-flex flex-column">
                    <h2 class="h4 fw-bold text-center mb-4">Progetti In Scadenza</h2>
                    <div class="table-responsive flex-grow-1">
                        <table class="table table-hover align-middle mb-0 admin-table-clean">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-body-secondary small text-uppercase fw-semibold">Nome
                                        Progetto</th>
                                    <th scope="col" class="text-body-secondary small text-uppercase fw-semibold">Paese
                                    </th>
                                    <th scope="col" class="text-body-secondary small text-uppercase fw-semibold">Scadenza
                                    </th>
                                    <th scope="col" class="text-end text-body-secondary small text-uppercase fw-semibold">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expiringProjects as $project)
                                    <tr class="admin-clickable-row" role="link" tabindex="0"
                                        onclick="window.location.href='{{ route('project.edit', $project->id) }}'"
                                        onkeydown="if(event.key === 'Enter' || event.key === ' '){ event.preventDefault(); window.location.href='{{ route('project.edit', $project->id) }}'; }">
                                        <td class="fw-semibold">{{ $project->title }}</td>
                                        <td>{{ $project->location ?? $project->country ?? 'N/D' }}</td>
                                        <td>
                                            @if ($project->expire_date)
                                                Scade {{ $project->expire_date->locale(app()->getLocale())->diffForHumans() }}
                                            @else
                                                Data non disponibile
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <i class="bi bi-chevron-right text-body-secondary"></i>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="admin-empty-row">
                                        <td colspan="4" class="text-center text-muted">Nessun progetto in scadenza a breve.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-auto pt-3 border-top">
                        <a href="{{ route('admin.projects.index') }}"
                            class="admin-list-link fw-semibold text-decoration-none d-inline-flex align-items-center gap-1">Vedi
                            tutti i progetti <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection