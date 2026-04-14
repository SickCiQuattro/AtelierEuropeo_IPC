@props(['project', 'showFavoriteIcon' => false])

@php
    $categoryBadges = [
        'CES' => 'badge-prog-ces',
        'SG' => 'badge-prog-sg',
        'CF' => 'badge-prog-cf',
    ];

    $category = $project->category;
    $badge = $categoryBadges[$category->tag];

    $approvedCount = $project->application->where('status', 'approved')->count();

    $durationText = 'N/D';
    if (!empty($project->start_date) && !empty($project->end_date)) {
        $startDate = \Carbon\Carbon::parse($project->start_date);
        $endDate = \Carbon\Carbon::parse($project->end_date);

        $durationDays = $startDate->diffInDays($endDate);

        if ($durationDays > 60) {
            // Se supera i 60 giorni, mostra in mesi e giorni
            $months = floor($durationDays / 30);

            $durationText = $months . ' ' . ($months == 1 ? 'Mese' : 'Mesi');
        } else {
            $durationText = $durationDays . ' ' . ($durationDays == 1 ? 'Giorno' : 'Giorni');
        }
    }
@endphp

<div class="project-card">
    <a href="{{ route('project.show', ['project' => $project->id]) }}" class="stretched-link"></a>
    @if ($project->status == 'published')
        <div class="badge-departure">
            <span style="rotate: 45deg; font-size: 12px;"><i class="bi bi-airplane-fill"></i></span>
            <b>{{ \Carbon\Carbon::parse($project->start_date)->translatedFormat('M') }}</b>
            <b>{{ $project->start_date->format('j') }}</b>
        </div>
    @endif
    <div>
        <img src="{{ $project->image_url }}" alt="{{ $project->title }}" class="project-card-image">
        @if ($showFavoriteIcon)
            @guest
                <button type="button" class="btn-favorite" data-project-id="{{ $project->id }}" data-bs-toggle="modal"
                    data-bs-target="#loginRequiredModal">
                    <i class="bi bi-heart"></i>
                </button>
            @endguest

            @auth
                <button type="button" class="btn-favorite" data-project-id="{{ $project->id }}">
                    <i class="bi bi-heart{{ auth()->user()->favorites->contains($project->id) ? '-fill' : '' }}"></i>
                </button>
            @endauth
        @endif
    </div>

    <span class="badge-duration"><i class="bi bi-calendar2-week-fill"></i> <b>{{ $durationText }}</b></span>


    <div class="project-card-body">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="bi bi-geo-alt-fill me-2"></i>{{ $project->location }}</span>
            <button type="button" class="{{ $badge }}" data-bs-toggle="modal"
                data-bs-target="#infoModal-{{ $category->tag }}">{{ $category->tag }} <i
                    class="bi bi-info-circle ms-1"></i></button>
        </div>
        {{-- Titolo --}}
        <h4 class="project-card-title">{{ $project->title }}</h4>
        {{-- Breve Descrizione --}}
        <p class="project-card-description">{{ $project->sum_description }}</p>
        {{-- Footer --}}
        @if ($project->status == 'published')
            <div class="d-flex justify-content-between align-items-center">
                <x-participants-progress :current="$approvedCount" :max="$project->requested_people" />
                <span><i clas s="bi bi-calendar2-event-fill me-2"></i>Scadenza:
                    {{ $project->expire_date->format('d/m/Y') }}</span>
            </div>
        @else
            <span class="text-center"><i class="bi bi-calendar-check-fill me-2"></i>Terminato il:
                {{ $project->end_date->format('d/m/Y') }}</span>
        @endif

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
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annulla</button>
                        <a href="{{ route('login') }}" class="btn btn-primary">Accedi</a>
                    </div>
                </div>
            </div>
        </div>
    @endguest
@endonce