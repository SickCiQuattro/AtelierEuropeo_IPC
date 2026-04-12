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
    <div class="badge-departure">
        <span style="rotate: 45deg; font-size: 10px;"><i class="bi bi-airplane-fill"></i></span>
        <b>{{ \Carbon\Carbon::parse($project->start_date)->translatedFormat('M') }}</b>
        <b>{{ $project->start_date->format('j') }}</b>
    </div>
    <div>
        <img src="{{ $project->image_url }}" alt="{{ $project->title }}" class="project-card-image">
        @if ($showFavoriteIcon)
            <button type="button" class="btn-favorite" data-project-id="{{ $project->id }}">
                <i class="bi bi-heart{{ auth()->user() && auth()->user()->favorites->contains($project->id) ? '-fill' : '' }}"
                    style="opacity: 1.0 !important;"></i>
            </button>
        @endif
    </div>
    <span class="badge-duration"><i class="bi bi-calendar2-week-fill"></i> <b>{{ $durationText }}</b></span>


    <div class="project-card-body">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="bi bi-geo-alt-fill me-2"></i>{{ $project->location }}</span>
            <button type="button" class="{{ $badge }}" data-bs-toggle="modal"
                data-bs-target="#categoryModal-{{ $category->name }}">{{ $category->tag }} <i
                    class="bi bi-info-circle ms-1"></i></button>
        </div>
        {{-- Titolo --}}
        <h4 class="project-card-title">{{ $project->title }}</h4>
        {{-- Breve Descrizione --}}
        <p class="project-card-description">{{ $project->sum_description }}</p>
        {{-- Footer --}}
        <div class="d-flex justify-content-between align-items-center">
            <x-participants-progress :current="$approvedCount" :max="$project->requested_people" />
            <span><i class="bi bi-calendar2-event-fill me-2"></i>Scadenza:
                {{ $project->expire_date->format('d/m/Y') }}</span>
        </div>
    </div>
</div>
