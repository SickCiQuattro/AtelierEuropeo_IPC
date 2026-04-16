@extends('layouts.master')

@section('title', 'AE - Dettaglio Progetto')

@section('active_progetti', 'active')

@section('body')
    @php
        $isAuthenticated = auth()->check();
        $isAdmin = $isAuthenticated && auth()->user()->role === 'admin';
        $isCompleted = $project->status === 'completed';
        $openDeleteModal = request()->boolean('openDeleteModal');

        $formatHumanDate = function ($value) {
            if (empty($value))
                return 'N/D';
            return \Carbon\Carbon::parse($value)->format('d/m/Y');
        };

        $previousUrl = url()->previous();
        $currentUrl = url()->current();
        $previousPath = parse_url($previousUrl, PHP_URL_PATH) ?? '';
        $defaultBackUrl = ($isAdmin && \Illuminate\Support\Facades\Route::has('admin.projects.index'))
            ? route('admin.projects.index')
            : route('project.index');
        $isUnsafeBackTarget = $previousUrl === $currentUrl
            || str_contains($previousPath, '/project/create')
            || (str_contains($previousPath, '/project/') && str_contains($previousPath, '/edit'));
        $backUrl = $isUnsafeBackTarget ? $defaultBackUrl : $previousUrl;

        $categoryBadges = [
            'CES' => 'badge-prog-ces',
            'SG' => 'badge-prog-sg',
            'CF' => 'badge-prog-cf',
        ];
        $tag = $project->category->tag ?? 'CES';
        $categoryBadgeClass = $categoryBadges[$tag] ?? 'badge-prog-ces';

        // Configurazione stato progetto
        $statusConfig = match ($project->status) {
            'published' => ['label' => 'Pubblicato', 'icon' => 'bi-broadcast', 'color' => 'text-success'],
            'completed' => ['label' => 'Completato', 'icon' => 'bi-check-circle-fill', 'color' => 'text-dark'],
            default => ['label' => 'Bozza', 'icon' => 'bi-pencil-square', 'color' => 'text-secondary'],
        };
    @endphp

    <main class="container px-2 px-md-4 pb-5 pt-4 project-detail-page">

        <div class="mb-4">
            <a href="{{ $backUrl }}"
                class="btn btn-ae btn-ae-light border shadow-sm rounded-pill px-3 py-2 text-secondary fw-semibold transition-hover">
                <i class="bi bi-arrow-left me-2"></i>Torna indietro
            </a>
        </div>

        <article class="card border-0 shadow-sm overflow-hidden mb-5" style="border-radius: 1.25rem;">
            <div class="row g-0 align-items-stretch">

                <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-center bg-white">
                    <div class="mb-3">
                        <span class="{{ $categoryBadgeClass }} shadow-sm" style="font-size: 0.9rem; padding: 0.4rem 1rem;">
                            {{ $tag }}
                        </span>
                    </div>
                    <h1 class="display-6 fw-bold mb-3 text-primary">{{ $project->title }}</h1>
                    <p class="lead text-secondary mb-0"
                        style="display: -webkit-box; -webkit-line-clamp: 4; line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $project->sum_description }}
                    </p>
                </div>

                <div class="col-lg-6 position-relative">
                    <img src="{{ $project->image_url }}" alt="{{ $project->title }}" class="w-100 h-100 object-fit-cover"
                        style="min-height: 320px;">

                    @if($isAdmin)
                        <div class="position-absolute shadow-sm"
                            style="top: 1.25rem; left: 1.25rem; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); border-radius: 1rem; padding: 0.5rem 1rem; z-index: 2;">
                            <span class="fw-bold {{ $statusConfig['color'] }} d-flex align-items-center gap-2">
                                <i class="bi {{ $statusConfig['icon'] }}"></i> {{ $statusConfig['label'] }}
                            </span>
                        </div>
                    @endif

                    @if (!$isAdmin)
                        @guest
                            <button type="button" class="btn-favorite shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#loginRequiredModal" style="top: 1.25rem; right: 1.25rem;">
                                <i class="bi bi-heart"></i>
                            </button>
                        @endguest
                        @auth
                            @php $isFavorite = auth()->user()->favorites->contains($project->id); @endphp
                            <button type="button" class="btn-favorite js-favorite-toggle shadow-sm"
                                data-project-id="{{ $project->id }}" aria-pressed="{{ $isFavorite ? 'true' : 'false' }}"
                                style="top: 1.25rem; right: 1.25rem;">
                                <i class="bi bi-heart{{ $isFavorite ? '-fill' : '' }}"></i>
                            </button>
                        @endauth
                    @endif
                </div>
            </div>
        </article>

        @if ($isAdmin)
            <section class="bg-white border shadow-sm p-3 mb-5 d-flex flex-wrap justify-content-end gap-2"
                style="border-radius: 1.25rem;">
                @if (!$isCompleted)
                    <a href="{{ route('project.edit', ['id' => $project->id]) }}" class="btn btn-ae btn-ae-outline-primary">
                        <i class="bi bi-pen-fill me-2"></i>Modifica
                    </a>
                @endif
                <a href="{{ route('admin.applications.index', $project->id) }}" class="btn btn-ae btn-ae-primary">
                    <i class="bi bi-people-fill me-2"></i>Gestisci Candidature
                </a>
                <button type="button" class="btn btn-ae btn-ae-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#deleteProjectModal">
                    <i class="bi bi-trash-fill me-2"></i>Elimina
                </button>
            </section>
        @endif

        <section class="mb-5">
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover"
                        style="border-radius: 1.25rem;">
                        <i class="bi bi-people-fill fs-2 mb-2 d-block text-primary"></i>
                        <span class="d-block fw-bold fs-5">{{ $project->requested_people }}</span>
                        <span class="small text-secondary fw-semibold">Richiesti</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover"
                        style="border-radius: 1.25rem;">
                        <i class="bi bi-geo-alt-fill fs-2 mb-2 d-block text-warning"></i>
                        <span class="d-block fw-bold fs-6">{{ $project->location }}</span>
                        <span class="small text-secondary fw-semibold">Luogo</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover"
                        style="border-radius: 1.25rem;">
                        <i class="bi bi-calendar2-week-fill fs-2 mb-2 d-block text-info"></i>
                        <span class="d-block fw-bold fs-6">{{ $formatHumanDate($project->start_date) }}</span>
                        <span class="small text-secondary fw-semibold">Inizio</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover"
                        style="border-radius: 1.25rem;">
                        <i class="bi bi-calendar-x-fill fs-2 mb-2 d-block text-danger"></i>
                        <span class="d-block fw-bold fs-6 text-danger">{{ $formatHumanDate($project->expire_date) }}</span>
                        <span class="small text-secondary fw-semibold">Scadenza</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="bg-white border p-4 p-md-5 mb-4 shadow-sm" style="border-radius: 1.25rem;">
                    <h3 class="h4 fw-bold mb-4 text-primary"><i class="bi bi-journal-text me-2"></i>Il viaggio in pillole
                    </h3>
                    <p class="text-secondary" style="white-space: pre-line;">{{ $project->full_description }}</p>
                </div>

                <div class="bg-white border p-4 p-md-5 shadow-sm" style="border-radius: 1.25rem;">
                    <h3 class="h4 fw-bold mb-4 text-primary"><i class="bi bi-list-check me-2"></i>Requisiti di
                        partecipazione</h3>
                    <p class="text-secondary mb-0" style="white-space: pre-line;">{{ $project->requirements }}</p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="bg-white border p-4 shadow-sm mb-4" style="border-radius: 1.25rem;">
                    <h3 class="h6 fw-bold mb-3 text-muted text-uppercase">Condizioni Economiche</h3>
                    <p class="text-secondary small mb-0" style="white-space: pre-line;">{{ $project->travel_conditions }}
                    </p>
                </div>

                <div class="bg-light border p-4 shadow-sm sticky-top" style="border-radius: 1.25rem; top: 110px;">
                    <h3 class="h6 fw-bold mb-2 text-muted text-uppercase">L'Associazione</h3>
                    <h4 class="h5 fw-bold mb-3 text-primary">{{ $project->association->name }}</h4>
                    <p class="text-secondary small mb-0">{{ $project->association->description }}</p>
                </div>
            </div>
        </div>
    </main>

    @if ($isAdmin)
        <div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                    <div class="modal-body p-5 text-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger display-3 mb-3 d-block"></i>
                        <h4 class="fw-bold mb-3">Elimina Definitivamente "{{ $project->title }}"?</h4>
                        <p class="text-secondary mb-4">L'azione è irreversibile e rimuoverà anche tutte le candidature
                            associate.</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-ae btn-ae-outline-secondary px-4"
                                data-bs-dismiss="modal">Annulla</button>
                            <form method="post" action="{{ route('project.destroy', ['id' => $project->id]) }}" class="m-0">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-ae btn-ae-danger px-4">Elimina</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const shouldOpenDeleteModal = @json($isAdmin && $openDeleteModal);
            if (!shouldOpenDeleteModal) {
                return;
            }

            const modalEl = document.getElementById('deleteProjectModal');
            if (!modalEl || !window.bootstrap || !bootstrap.Modal) {
                return;
            }

            bootstrap.Modal.getOrCreateInstance(modalEl).show();

            const current = new URL(window.location.href);
            current.searchParams.delete('openDeleteModal');
            const cleanQuery = current.searchParams.toString();
            const cleanUrl = current.pathname + (cleanQuery ? `?${cleanQuery}` : '') + current.hash;
            window.history.replaceState({}, document.title, cleanUrl);
        });
    </script>
@endsection