@props([
    'project',
    'showFavoriteIcon' => false,
    'source' => null,
])

@php
    $categoryBadges = [
        'CES' => 'badge-prog-ces',
        'SG' => 'badge-prog-sg',
        'CF' => 'badge-prog-cf',
    ];

    $category = $project->category;
    $badge = $categoryBadges[$category->tag] ?? 'badge-prog-ces';

    $approvedCount = $project->application->where('status', 'approved')->count();

    $startDate = null;
    $endDate = null;
    $expireDate = null;

    try {
        if (!empty($project->start_date)) {
            $startDate = \Carbon\Carbon::parse($project->start_date)->startOfDay();
        }
    } catch (\Throwable) {
        $startDate = null;
    }

    try {
        if (!empty($project->end_date)) {
            $endDate = \Carbon\Carbon::parse($project->end_date)->startOfDay();
        }
    } catch (\Throwable) {
        $endDate = null;
    }

    try {
        if (!empty($project->expire_date)) {
            $expireDate = \Carbon\Carbon::parse($project->expire_date)->startOfDay();
        }
    } catch (\Throwable) {
        $expireDate = null;
    }

    $durationText = 'Durata da definire';
    if ($startDate && $endDate) {
        $secondsDiff = abs($startDate->diffInSeconds($endDate));
        $durationDays = max(1, (int) ceil($secondsDiff / 86400));

        if ($durationDays > 60) {
            $months = max(1, (int) floor($durationDays / 30));
            $durationText = $months . ' ' . ($months == 1 ? 'Mese' : 'Mesi');
        } else {
            $durationText = $durationDays . ' ' . ($durationDays == 1 ? 'Giorno' : 'Giorni');
        }
    }

    $isAdminUserPreview = auth()->check()
        && auth()->user()->role === 'admin'
        && session('admin_user_view', false);

    $resolvedSource = null;
    if (in_array((string) $source, ['projects', 'portfolio'], true)) {
        $resolvedSource = (string) $source;
    } elseif (request()->routeIs('project.index')) {
        $resolvedSource = 'projects';
    } elseif (request()->routeIs('project.portfolio')) {
        $resolvedSource = 'portfolio';
    }

    $projectDetailsRouteParams = ['project' => $project->id];
    if ($resolvedSource !== null) {
        $projectDetailsRouteParams['source'] = $resolvedSource;
    }
@endphp

<div class="project-card d-flex flex-column h-100">
    <a href="{{ route('project.show', $projectDetailsRouteParams) }}" class="stretched-link"></a>

    <div class="project-card-media">
        <img src="{{ $project->image_url }}" alt="{{ $project->title }}" class="project-card-image">

        @if ($project->status == 'published' && $startDate)
            <div class="badge-departure shadow-sm">
                <div class="badge-departure-top">
                    <i class="bi bi-airplane-fill"></i>
                    <span class="badge-departure-month">{{ $startDate->translatedFormat('M') }}</span>
                </div>
                <div class="badge-departure-day">{{ $startDate->format('j') }}</div>
            </div>
        @endif

        @if ($showFavoriteIcon && !$isAdminUserPreview)
            @guest
                <button type="button" class="btn-favorite z-3" data-project-id="{{ $project->id }}" data-bs-toggle="modal"
                    data-bs-target="#loginRequiredModal">
                    <i class="bi bi-heart"></i>
                </button>
            @endguest

            @auth
                <button type="button" class="btn-favorite js-favorite-toggle z-3" data-project-id="{{ $project->id }}"
                    data-url="{{ route('project.favorite.toggle', $project->id) }}" aria-label="Salva nei preferiti"
                    aria-pressed="{{ auth()->user()->favorites->contains($project->id) ? 'true' : 'false' }}">
                    <i class="bi bi-heart{{ auth()->user()->favorites->contains($project->id) ? '-fill' : '' }}"></i>
                </button>
            @endauth
        @endif

        <span class="badge-duration shadow-sm"><i class="bi bi-calendar2-week-fill"></i> {{ $durationText }}</span>
    </div>

    <div class="project-card-body d-flex flex-column flex-grow-1">
        {{-- Header Location & Badge --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-body-secondary small fw-semibold"><i
                    class="bi bi-geo-alt-fill me-1"></i>{{ $project->location }}</span>
            <span class="d-inline-block position-relative z-3" tabindex="0" data-bs-toggle="tooltip"
                data-bs-placement="top" title="Clicca per info sul programma {{ $category->name }}">
                <button type="button" class="{{ $badge }} position-relative z-3" data-bs-toggle="modal"
                    data-bs-target="#infoModal-{{ $category->tag }}">{{ $category->tag }} <i
                        class="bi bi-info-circle ms-1"></i></button>
            </span>
        </div>

        {{-- Titolo --}}
        <h4 class="project-card-title mb-2">{{ $project->title }}</h4>

        {{-- Breve Descrizione (Troncata a 3 righe se supera il limite) --}}
        <p class="project-card-description mb-4">{{ $project->sum_description }}</p>

        {{-- Footer (Ancorato in basso. Layout flessibile per non far accavallare gli elementi) --}}
        <div class="project-card-footer mt-auto pt-3" style="border-top: 1px solid #dee2e6;">
            @if ($project->status == 'published')
                <div class="d-flex justify-content-between align-items-center gap-2">

                    <div class="position-relative z-3">
                        <x-participants-progress :current="$approvedCount" :max="$project->requested_people" />
                    </div>

                    <span class="project-card-deadline small text-body-secondary fw-semibold text-end">
                        <i class="bi bi-calendar2-event-fill me-1"></i>
                        @if ($expireDate)
                            Scad. {{ $expireDate->format('d/m/Y') }}
                        @else
                            Scadenza da definire
                        @endif
                    </span>
                </div>
            @else
                <div class="text-center position-relative z-3">
                    <span class="project-status-ended">
                        <i class="bi bi-calendar3"></i>
                        @if ($endDate)
                            Terminato il {{ $endDate->format('d/m/Y') }}
                        @else
                            Terminato
                        @endif
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>

@once
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
@endonce