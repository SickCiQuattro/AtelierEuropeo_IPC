@extends('layouts.master')

@section('title', 'AE - Dettaglio Progetto')

@section('active_progetti', 'active')

@section('body')
    @php
        $isAuthenticated = auth()->check();
        $isAdmin = $isAuthenticated && auth()->user()->role === 'admin';
        $detailSource = (string) request()->query('source');
        $isUserSource = in_array($detailSource, ['projects', 'portfolio'], true);
        $isAdminPreviewMode = $isAdmin && session('admin_user_view', false);
        $isAdminContextRequested = $isAdmin && request()->boolean('adminContext');
        $isAdminContext = $isAdmin && ($isAdminContextRequested || (!$isAdminPreviewMode && !$isUserSource));

        $userListRoute = $detailSource === 'portfolio' ? 'project.portfolio' : 'project.index';
        $userListLabel = $detailSource === 'portfolio' ? 'Archivio Progetti' : 'Progetti Disponibili';
        $isDraft = $project->status === \App\Models\Project::STATUS_DRAFT;
        $isCompleted = $project->status === \App\Models\Project::STATUS_COMPLETED;
        $projectTestimonials = $testimonials ?? collect();
        $openDeleteModal = request()->boolean('openDeleteModal');
        $loginRedirectUrl = route('login', ['redirect' => request()->fullUrl()]);
        $alreadyApplied = auth()->check()
            ? \App\Models\Application::where('user_id', auth()->id())
                ->where('project_id', $project->id)
                ->exists()
            : false;

        $formatHumanDate = function ($value) {
            if (empty($value))
                return 'N/D';
            return \Carbon\Carbon::parse($value)->format('d/m/Y');
        };

        // Gestione Navigazione Sicura
        $previousUrl = url()->previous();
        $currentUrl = url()->current();
        $previousPath = parse_url($previousUrl, PHP_URL_PATH) ?? '';
        $defaultBackUrl = ($isAdminContext && \Illuminate\Support\Facades\Route::has('admin.projects.index'))
            ? route('admin.projects.index')
            : route($userListRoute);

        $isUnsafeBackTarget = $previousUrl === $currentUrl
            || str_contains($previousPath, '/project/create')
            || (str_contains($previousPath, '/project/') && str_contains($previousPath, '/edit'));
        $backUrl = $isUnsafeBackTarget ? $defaultBackUrl : $previousUrl;
        $breadcrumbListUrl = $isAdminContext
            ? route('admin.projects.index')
            : route($userListRoute);
        $breadcrumbListLabel = $isAdminContext
            ? 'Progetti'
            : $userListLabel;

        // Gestione Badge Categoria
        $categoryBadges = [
            'CES' => 'badge-prog-ces',
            'SG' => 'badge-prog-sg',
            'CF' => 'badge-prog-cf',
        ];
        $tag = $project->category->tag ?? 'CES';
        $categoryBadgeClass = $categoryBadges[$tag] ?? 'badge-prog-ces';
        $categoryModalTag = array_key_exists($tag, $categoryBadges) ? $tag : 'CES';
        $categoryName = $project->category->name ?? 'programma selezionato';

        // Configurazione Stato Progetto (Colori semantici)
        $statusConfig = match ($project->status) {
            'published' => ['label' => 'Pubblicato', 'icon' => 'bi-broadcast', 'color' => 'text-success'],
            'completed' => ['label' => 'Completato', 'icon' => 'bi-archive-fill', 'color' => 'text-dark'],
            default => ['label' => 'Bozza', 'icon' => 'bi-pencil-square', 'color' => 'text-secondary'],
        };
        $statusBadgeClass = $statusConfig['color'];
        $statusIconClass = $statusConfig['icon'];
        $statusLabel = $statusConfig['label'];
        $programTooltip = 'Info sul programma ' . $categoryName;

        $durationText = 'N/D';
        if (!empty($project->start_date) && !empty($project->end_date)) {
            $startDate = \Carbon\Carbon::parse($project->start_date);
            $endDate = \Carbon\Carbon::parse($project->end_date);
            $durationDays = max(1, (int) ceil(abs($startDate->diffInSeconds($endDate)) / 86400));

            if ($durationDays > 60) {
                $months = max(1, (int) floor($durationDays / 30));
                $durationText = $months . ' ' . ($months === 1 ? 'Mese' : 'Mesi');
            } else {
                $durationText = $durationDays . ' ' . ($durationDays === 1 ? 'Giorno' : 'Giorni');
            }
        }

        $fullDescriptionHtml = \App\Helpers\RichTextHelper::sanitize($project->full_description);
        $requirementsHtml = \App\Helpers\RichTextHelper::sanitize($project->requirements);
        $travelConditionsHtml = \App\Helpers\RichTextHelper::sanitize($project->travel_conditions);
    @endphp

    <style>
        .rich-text-content p,
        .rich-text-content ul {
            margin-bottom: 0.85rem;
        }

        .rich-text-content p:last-child,
        .rich-text-content ul:last-child {
            margin-bottom: 0;
        }

        .rich-text-content ul {
            padding-left: 1.25rem;
        }

        .rich-text-content li {
            margin-bottom: 0.35rem;
        }

        .rich-text-content a {
            color: var(--bs-primary);
            text-decoration: underline;
        }
    </style>

    <div class="container-fluid px-3 px-md-4 py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">

                <div class="d-md-none mb-3">
                    <a href="{{ $backUrl }}"
                        class="btn btn-ae btn-ae-light border shadow-sm rounded-pill px-3 py-2 text-secondary fw-semibold transition-hover">
                        <i class="bi bi-arrow-left me-2"></i>Indietro
                    </a>
                </div>

                <div class="d-none d-md-block mb-4">
                    <x-breadcrumb>
                        <li class="breadcrumb-item">
                            <a href="{{ $breadcrumbListUrl }}">{{ $breadcrumbListLabel }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $project->title }}</li>
                    </x-breadcrumb>
                </div>

                <article class="card border-0 shadow-sm overflow-hidden mb-4" style="border-radius: 1.25rem;">
                    <div class="row g-0 align-items-stretch">

                        <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-center bg-white">

                            <div class="d-flex flex-column align-items-start gap-2 mb-3">

                                @if ($isAdminContext)
                                    <span class="badge rounded-pill bg-light border px-3 py-2 {{ $statusBadgeClass }}"
                                        style="font-size: 0.85rem;">
                                        <i class="bi {{ $statusIconClass }} me-1"></i>{{ $statusLabel }}
                                    </span>
                                @endif

                                <span class="d-inline-block position-relative z-3" tabindex="0" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="{{ $programTooltip }}">
                                    <button type="button"
                                        class="{{ $categoryBadgeClass }} border-0 shadow-sm px-3 py-1 mt-1"
                                        data-bs-toggle="modal" data-bs-target="#infoModal-{{ $categoryModalTag }}"
                                        style="font-size: 0.9rem;">
                                        {{ $tag }} <i class="bi bi-info-circle ms-1"></i>
                                    </button>
                                </span>

                            </div>

                            <h1 class="display-5 fw-bold mb-3 text-primary" style="line-height: 1.1;">{{ $project->title }}
                            </h1>

                            <p class="lead text-secondary mb-0"
                                style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $project->sum_description }}
                            </p>

                            @if (!$isAdminContext)
                                <div class="mt-4 d-flex flex-wrap gap-2">
                                    @guest
                                        @if ($isCompleted)

                                        @else
                                            <a href="{{ $loginRedirectUrl }}" class="btn btn-ae btn-ae-primary rounded-pill px-4 py-2">
                                                <i class="bi bi-box-arrow-in-right me-2"></i>Accedi per candidarti
                                            </a>
                                        @endif
                                    @endguest

                                    @auth
                                        @if ($isCompleted)

                                        @elseif ($alreadyApplied)
                                            <a href="{{ route('applications.index') }}"
                                                class="btn btn-ae btn-ae-outline-primary rounded-pill px-4 py-2">
                                                <i class="bi bi-file-earmark-text me-2"></i>Vedi Dettaglio Candidatura
                                            </a>
                                        @else
                                            <a href="{{ route('applications.create', $project->id) }}"
                                                class="btn btn-ae btn-ae-primary rounded-pill px-4 py-2 shadow-sm">
                                                <i class="bi bi-send me-2"></i>Candidati ora
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-6 position-relative">
                            <img src="{{ $project->image_url }}" alt="{{ $project->title }}"
                                class="w-100 h-100 object-fit-cover" style="min-height: 350px;">

                            @if (!$isAdmin)
                                @guest
                                    <button type="button" class="btn-favorite shadow-sm m-4" data-bs-toggle="modal"
                                        data-bs-target="#loginRequiredModal">
                                        <i class="bi bi-heart"></i>
                                    </button>
                                @endguest
                                @auth
                                    @php $isFavorite = auth()->user()->favorites->contains($project->id); @endphp
                                    <button type="button" class="btn-favorite js-favorite-toggle shadow-sm m-4"
                                        data-project-id="{{ $project->id }}"
                                        data-url="{{ route('project.favorite.toggle', $project->id) }}"
                                        aria-label="Salva nei preferiti" aria-pressed="{{ $isFavorite ? 'true' : 'false' }}">
                                        <i class="bi bi-heart{{ $isFavorite ? '-fill' : '' }}"></i>
                                    </button>
                                @endauth
                            @endif
                        </div>
                    </div>
                </article>

                @if ($isAdminContext)
                    <section
                        class="bg-white border shadow-sm p-3 mb-5 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3"
                        style="border-radius: 1.25rem;">
                        <div class="text-secondary fw-semibold ps-2 d-none d-md-block">
                            <i class="bi bi-gear-fill me-2"></i>Pannello Operativo
                        </div>

                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="button" class="btn btn-ae btn-ae-outline-danger rounded-pill px-4"
                                data-bs-toggle="modal" data-bs-target="#deleteProjectModal">
                                <i class="bi bi-trash-fill me-2"></i>Elimina
                            </button>

                            @if (!$isDraft)
                                <a href="{{ route('admin.applications.index', $project->id) }}"
                                    class="btn btn-ae btn-ae-outline-secondary rounded-pill px-4">
                                    <i class="bi bi-people-fill me-2"></i>Candidature
                                </a>
                            @endif

                            @if (!$isCompleted)
                                <a href="{{ route('project.edit', ['id' => $project->id, 'adminContext' => $isAdminContext ? 1 : null]) }}"
                                    class="btn btn-ae btn-ae-primary rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-pencil-fill me-2"></i>Modifica Progetto
                                </a>
                            @endif
                        </div>
                    </section>
                @endif

                <section class="mb-5">
                    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 g-md-4">
                        <div class="col">
                            <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover d-flex flex-column align-items-center justify-content-center"
                                style="border-radius: 1.25rem;">
                                <i class="bi bi-person-check-fill fs-2 mb-2 d-block text-primary"></i>
                                <span class="d-block fw-bold fs-6 lh-sm mb-2"
                                    style="min-height: 2.2rem;">{{ $project->requested_people }}</span>
                                <span class="small text-secondary fw-semibold lh-sm">Partecipanti Richiesti</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover d-flex flex-column align-items-center justify-content-center"
                                style="border-radius: 1.25rem;">
                                <i class="bi bi-geo-alt-fill fs-2 mb-2 d-block text-primary"></i>
                                <span class="d-block fw-bold fs-6 lh-sm mb-2"
                                    style="min-height: 2.2rem;">{{ $project->location }}</span>
                                <span class="small text-secondary fw-semibold lh-sm">Luogo</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover d-flex flex-column align-items-center justify-content-center"
                                style="border-radius: 1.25rem;">
                                <i class="bi bi-airplane-fill fs-2 mb-2 d-block text-primary"></i>
                                <span class="d-block fw-bold fs-6 lh-sm mb-2"
                                    style="min-height: 2.2rem;">{{ $formatHumanDate($project->start_date) }}</span>
                                <span class="small text-secondary fw-semibold lh-sm">Inizio Previsto</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover d-flex flex-column align-items-center justify-content-center"
                                style="border-radius: 1.25rem;">
                                <i class="bi bi-calendar2-week-fill fs-2 mb-2 d-block text-primary"></i>
                                <span class="d-block fw-bold fs-6 lh-sm mb-2"
                                    style="min-height: 2.2rem;">{{ $durationText }}</span>
                                <span class="small text-secondary fw-semibold lh-sm">Durata</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover d-flex flex-column align-items-center justify-content-center"
                                style="border-radius: 1.25rem;">
                                <i class="bi bi-calendar2-event-fill fs-2 mb-2 d-block text-primary"></i>
                                <span class="d-block fw-bold fs-6 lh-sm mb-2"
                                    style="min-height: 2.2rem;">{{ $formatHumanDate($project->expire_date) }}</span>
                                <span class="small text-secondary fw-semibold lh-sm">Scadenza Iscrizioni</span>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <section class="mb-5">
                            <h3 class="h4 fw-bold mb-4 text-primary"><i class="bi bi-journal-text me-2"></i>Il viaggio in
                                pillole</h3>
                            <div class="text-secondary fs-6 mb-0 rich-text-content" style="line-height: 1.85;">
                                {!! $fullDescriptionHtml !!}
                            </div>
                        </section>

                        <section>
                            <h3 class="h4 fw-bold mb-4 text-primary"><i class="bi bi-list-check me-2"></i>Requisiti di
                                partecipazione</h3>
                            <div class="text-secondary fs-6 mb-0 rich-text-content" style="line-height: 1.85;">
                                {!! $requirementsHtml !!}
                            </div>
                        </section>
                    </div>

                    <div class="col-lg-4">
                        <div class="bg-white border p-4 shadow-sm mb-4" style="border-radius: 1.25rem;">
                            <h3 class="h6 fw-bold mb-3 text-muted text-uppercase"><i
                                    class="bi bi-wallet2 me-2"></i>Condizioni Economiche</h3>
                            <div class="text-secondary small mb-0 rich-text-content" style="line-height: 1.6;">
                                {!! $travelConditionsHtml !!}
                            </div>
                        </div>

                        <div class="bg-white border p-4 shadow-sm mb-4" style="border-radius: 1.25rem;">
                            <h3 class="h6 fw-bold mb-3 text-muted text-uppercase"><i
                                    class="bi bi-building me-2"></i>L'Associazione</h3>
                            <h4 class="h5 fw-bold mb-2 text-primary">{{ $project->association->name }}</h4>
                            <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                {{ $project->association->description }}
                            </p>
                        </div>

                        {{-- CTA Candidatura --}}
                        @if(!$isAdminContext)
                            @if(!$isCompleted)
                                <div class="bg-light border p-4 shadow-sm mb-4 sticky-top"
                                    style="border-radius: 1.25rem; top: 110px;">
                                    <h3 class="h6 fw-bold mb-2 text-muted text-uppercase">
                                        <i class="bi bi-send me-2"></i>Candidatura
                                    </h3>

                                    @guest
                                        <p class="text-muted small mb-3">
                                            Accedi per candidarti a questo progetto.
                                        </p>
                                        <a href="{{ $loginRedirectUrl }}" class="btn btn-ae btn-ae-primary w-100 rounded-pill">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>Accedi per candidarti
                                        </a>
                                    @endguest

                                    @auth
                                        @if($alreadyApplied)
                                            <p class="text-muted small mb-3">
                                                Hai già inviato una candidatura per questo progetto.
                                            </p>
                                            <a href="{{ route('applications.index') }}"
                                                class="btn btn-ae btn-ae-outline-primary w-100 rounded-pill">
                                                <i class="bi bi-file-earmark-text me-2"></i>Vedi Dettaglio Candidatura
                                            </a>
                                        @else
                                            <p class="text-muted small mb-3">
                                                Invia la tua candidatura per partecipare a questo progetto.
                                            </p>
                                            <a href="{{ route('applications.create', $project->id) }}"
                                                class="btn btn-ae btn-ae-primary w-100 rounded-pill">
                                                <i class="bi bi-send me-2"></i>Candidati ora
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            @else
                                <div class="bg-light border p-4 shadow-sm mb-4 sticky-top"
                                    style="border-radius: 1.25rem; top: 110px;">
                                    <div class="d-flex align-items-start gap-3">

                                        <div>
                                            <h3 class="h6 fw-bold mb-3 text-uppercase text-muted"><i
                                                    class="bi bi-archive-fill me-2"></i>
                                                Progetto Completato
                                            </h3>
                                            <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                                Le candidature sono chiuse. Trovi le testimonianze dei partecipanti più in basso.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($isCompleted && $projectTestimonials->count() > 0)
        <section id="testimonianze" class="py-5 mt-5 bg-light" style="scroll-margin-top: 100px;">
            <div class="container px-3 px-md-4">

                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bold text-dark mb-2">Testimonianze</h2>
                    <p class="text-secondary lead">
                        I racconti di chi ha vissuto questo progetto.
                    </p>
                </div>

                <div class="row g-4 justify-content-center">
                    @foreach ($projectTestimonials as $testimonial)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100 rounded-4 position-relative overflow-hidden transition-hover"
                                style="background-color: #fff;">
                                {{-- Watermark quote --}}
                                <i class="bi bi-quote position-absolute text-light"
                                    style="font-size: 5rem; top: -15px; left: 10px; z-index: 0; opacity: 0.7;"></i>

                                <div class="card-body p-4 d-flex flex-column position-relative z-1 mt-3">
                                    <p class="text-dark fst-italic flex-grow-1 mb-4" style="line-height: 1.6; font-size: 1.05rem;">
                                        "{{ $testimonial->content }}"
                                    </p>

                                    <div class="border-top pt-3 mt-auto d-flex align-items-center gap-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                            style="width: 40px; height: 40px; font-size: 1rem;">
                                            {{ strtoupper(substr($testimonial->author->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $testimonial->author->name ?? 'Partecipante' }}
                                            </h6>
                                            <small class="text-muted">Partecipante</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-5 pt-3">
                    <a href="{{ route('project.index') }}"
                        class="btn btn-lg btn-ae btn-ae-outline-primary rounded-pill px-4 py-2 shadow-sm">
                        Vedi tutti i progetti disponibili <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                    </a>
                </div>

            </div>
        </section>
    @endif

    @if ($isAdminContext)
        <div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                    <div class="modal-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle mb-3"
                                style="width: 80px; height: 80px;">
                                <i class="bi bi-exclamation-triangle-fill text-danger display-5"></i>
                            </div>
                            <h4 class="fw-bold mb-2 text-dark">Elimina Progetto</h4>
                            <p class="text-secondary mb-0">Stai per eliminare definitivamente il progetto
                                <strong>"{{ $project->title }}"</strong>. Questa azione non può essere annullata e rimuoverà
                                anche tutte le candidature associate.
                            </p>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center mt-4">
                            <button type="button" class="btn btn-ae btn-ae-outline-secondary px-4 fw-semibold rounded-pill"
                                data-bs-dismiss="modal">
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
            const shouldOpenDeleteModal = @json($isAdminContext && $openDeleteModal);
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