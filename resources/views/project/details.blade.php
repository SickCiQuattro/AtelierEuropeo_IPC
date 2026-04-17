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

        // Gestione Navigazione Sicura
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

        // Gestione Badge Categoria
        $categoryBadges = [
            'CES' => 'badge-prog-ces',
            'SG'  => 'badge-prog-sg',
            'CF'  => 'badge-prog-cf',
        ];
        $tag = $project->category->tag ?? 'CES';
        $categoryBadgeClass = $categoryBadges[$tag] ?? 'badge-prog-ces';
        $categoryModalTag = array_key_exists($tag, $categoryBadges) ? $tag : 'CES';
        $categoryName = $project->category->name ?? 'programma selezionato';

        // Configurazione Stato Progetto (Colori semantici)
        $statusConfig = match ($project->status) {
            'published' => ['label' => 'Pubblicato', 'icon' => 'bi-broadcast', 'color' => 'text-success'],
            'completed' => ['label' => 'Completato', 'icon' => 'bi-archive-fill', 'color' => 'text-dark'],
            default     => ['label' => 'Bozza', 'icon' => 'bi-pencil-square', 'color' => 'text-secondary'],
        };
    @endphp

    <div class="container-fluid px-3 px-md-4 py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">

                <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-between gap-3 mb-4">
                    <div class="flex-grow-1" style="margin-bottom: -1.5rem;">
                        <x-breadcrumb>
                            @if($isAdmin)
                                <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Gestione Progetti</a></li>
                            @else
                                <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Progetti Disponibili</a></li>
                            @endif
                            <li class="breadcrumb-item active" aria-current="page">{{ $project->title }}</li>
                        </x-breadcrumb>
                    </div>
                    
                    <a href="{{ $backUrl }}" class="btn btn-ae btn-ae-light border shadow-sm rounded-pill px-3 py-2 text-secondary fw-semibold transition-hover flex-shrink-0">
                        <i class="bi bi-arrow-left me-2"></i>Indietro
                    </a>
                </div>

                <article class="card border-0 shadow-sm overflow-hidden mb-4" style="border-radius: 1.25rem;">
                    <div class="row g-0 align-items-stretch">
                        
                        <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-center bg-white">
                            
                            <div class="d-flex flex-column align-items-start gap-2 mb-3">
                                
                                @if($isAdmin)
                                    <span class="badge rounded-pill bg-light border px-3 py-2 {{ $statusConfig['color'] }} shadow-sm" style="font-size: 0.85rem;">
                                        <i class="bi {{ $statusConfig['icon'] }} me-1"></i> Stato: {{ $statusConfig['label'] }}
                                    </span>
                                @endif
                                
                                <span class="d-inline-block position-relative z-3" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Info sul programma {{ $categoryName }}">
                                    <button type="button" class="{{ $categoryBadgeClass }} border-0 shadow-sm px-3 py-1 mt-1" data-bs-toggle="modal" data-bs-target="#infoModal-{{ $categoryModalTag }}" style="font-size: 0.9rem;">
                                        {{ $tag }} <i class="bi bi-info-circle ms-1"></i>
                                    </button>
                                </span>

                            </div>
                            
                            <h1 class="display-5 fw-bold mb-3 text-primary" style="line-height: 1.1;">{{ $project->title }}</h1>
                            
                            <p class="lead text-secondary mb-0" style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $project->sum_description }}
                            </p>
                        </div>

                        <div class="col-lg-6 position-relative">
                            <img src="{{ $project->image_url }}" alt="{{ $project->title }}" class="w-100 h-100 object-fit-cover" style="min-height: 350px;">
                            
                            @if (!$isAdmin)
                                @guest
                                    <button type="button" class="btn-favorite shadow-sm m-4" data-bs-toggle="modal" data-bs-target="#loginRequiredModal">
                                        <i class="bi bi-heart"></i>
                                    </button>
                                @endguest
                                @auth
                                    @php $isFavorite = auth()->user()->favorites->contains($project->id); @endphp
                                    <button type="button" class="btn-favorite js-favorite-toggle shadow-sm m-4"
                                        data-project-id="{{ $project->id }}"
                                        data-url="{{ route('project.favorite.toggle', $project->id) }}"
                                        aria-label="Salva nei preferiti"
                                        aria-pressed="{{ $isFavorite ? 'true' : 'false' }}">
                                        <i class="bi bi-heart{{ $isFavorite ? '-fill' : '' }}"></i>
                                    </button>
                                @endauth
                            @endif
                        </div>
                    </div>
                </article>

                @if ($isAdmin)
                    <section class="bg-white border shadow-sm p-3 mb-5 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3" style="border-radius: 1.25rem;">
                        <div class="text-secondary fw-semibold ps-2 d-none d-md-block">
                            <i class="bi bi-gear-fill me-2"></i>Pannello Operativo
                        </div>
                        
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="button" class="btn btn-ae btn-ae-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#deleteProjectModal">
                                <i class="bi bi-trash-fill me-2"></i>Elimina
                            </button>

                            <a href="{{ route('admin.applications.index', $project->id) }}" class="btn btn-ae btn-ae-outline-secondary rounded-pill px-4">
                                <i class="bi bi-people-fill me-2"></i>Candidature
                            </a>

                            @if (!$isCompleted)
                                <a href="{{ route('project.edit', ['id' => $project->id]) }}" class="btn btn-ae btn-ae-primary rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-pencil-fill me-2"></i>Modifica Progetto
                                </a>
                            @endif
                        </div>
                    </section>
                @endif

                <section class="mb-5">
                    <div class="row g-3 g-md-4">
                        <div class="col-6 col-md-3">
                            <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover" style="border-radius: 1.25rem;">
                                <i class="bi bi-person-badge-fill fs-2 mb-2 d-block text-primary"></i>
                                <span class="d-block fw-bold fs-5">{{ $project->requested_people }}</span>
                                <span class="small text-secondary fw-semibold">Richiesti</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover" style="border-radius: 1.25rem;">
                                <i class="bi bi-geo-alt-fill fs-2 mb-2 d-block text-primary"></i>
                                <span class="d-block fw-bold fs-6">{{ $project->location }}</span>
                                <span class="small text-secondary fw-semibold">Luogo</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover" style="border-radius: 1.25rem;">
                                <i class="bi bi-calendar2-week-fill fs-2 mb-2 d-block text-primary"></i>
                                <span class="d-block fw-bold fs-6">{{ $formatHumanDate($project->start_date) }}</span>
                                <span class="small text-secondary fw-semibold">Inizio Previsto</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover" style="border-radius: 1.25rem;">
                                <i class="bi bi-calendar-check-fill fs-2 mb-2 d-block text-primary"></i>
                                <span class="d-block fw-bold fs-6">{{ $formatHumanDate($project->expire_date) }}</span>
                                <span class="small text-secondary fw-semibold">Scadenza Iscrizioni</span>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="bg-white border p-4 p-md-5 mb-4 shadow-sm" style="border-radius: 1.25rem;">
                            <h3 class="h4 fw-bold mb-4 text-primary"><i class="bi bi-journal-text me-2"></i>Il viaggio in pillole</h3>
                            <p class="text-secondary" style="white-space: pre-line; line-height: 1.8;">{{ $project->full_description }}</p>
                        </div>

                        <div class="bg-white border p-4 p-md-5 shadow-sm" style="border-radius: 1.25rem;">
                            <h3 class="h4 fw-bold mb-4 text-primary"><i class="bi bi-list-check me-2"></i>Requisiti di partecipazione</h3>
                            <p class="text-secondary mb-0" style="white-space: pre-line; line-height: 1.8;">{{ $project->requirements }}</p>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="bg-white border p-4 shadow-sm mb-4" style="border-radius: 1.25rem;">
                            <h3 class="h6 fw-bold mb-3 text-muted text-uppercase"><i class="bi bi-wallet2 me-2"></i>Condizioni Economiche</h3>
                            <p class="text-secondary small mb-0" style="white-space: pre-line; line-height: 1.6;">{{ $project->travel_conditions }}</p>
                        </div>

                        <div class="bg-light border p-4 shadow-sm sticky-top" style="border-radius: 1.25rem; top: 110px;">
                            <h3 class="h6 fw-bold mb-3 text-muted text-uppercase"><i class="bi bi-building me-2"></i>L'Associazione</h3>
                            <h4 class="h5 fw-bold mb-2 text-primary">{{ $project->association->name }}</h4>
                            <p class="text-secondary small mb-0" style="line-height: 1.6;">{{ $project->association->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($isAdmin)
        <div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                    <div class="modal-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-exclamation-triangle-fill text-danger display-5"></i>
                            </div>
                            <h4 class="fw-bold mb-2 text-dark">Elimina Progetto</h4>
                            <p class="text-secondary mb-0">Stai per eliminare definitivamente il progetto <strong>"{{ $project->title }}"</strong>. Questa azione non può essere annullata e rimuoverà anche tutte le candidature associate.</p>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center mt-4">
                            <button type="button" class="btn btn-ae btn-ae-outline-secondary px-4 fw-semibold rounded-pill" data-bs-dismiss="modal">
                                Annulla
                            </button>
                            <form method="post" action="{{ route('project.destroy', ['id' => $project->id]) }}" class="m-0">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-ae btn-ae-danger px-4 fw-semibold rounded-pill w-100">
                                    <i class="bi bi-trash-fill me-2"></i>Sì, Elimina
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @guest
        <div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginRequiredModalLabel">Accesso Richiesto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Devi accedere al tuo account per poter salvare i progetti nei preferiti e ritrovarli in seguito.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ae btn-ae-square btn-ae-outline-secondary"
                            data-bs-dismiss="modal">Annulla</button>
                        <a href="{{ route('login') }}" class="btn btn-ae btn-ae-square btn-ae-primary">Accedi</a>
                    </div>
                </div>
            </div>
        </div>
    @endguest
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