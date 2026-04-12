@props(['current', 'max'])

@php
    // Calcolo la percentuale in modo sicuro
    $percentage = $max > 0 ? round(($current / $max) * 100) : 0;
    $percentage = $percentage > 100 ? 100 : $percentage;

    // Determino i colori in base alla percentuale
    if ($percentage >= 100) {
        // Progetto Pieno -> Tutto Grigio
        $colorClass = 'progress-fill-full';
        $textClass = 'text-full';
    } elseif ($percentage >= 80) {
        // Quasi Pieno -> Rosso
        $colorClass = 'progress-fill-danger';
        // $textClass = 'text-dark';
    } elseif ($percentage >= 50) {
        // A metà -> Arancione
        $colorClass = 'progress-fill-warning';
        // $textClass = 'text-dark';
    } else {
        // Pochi iscritti -> Verde
        $colorClass = 'progress-fill-success';
        // $textClass = 'text-dark';
    }
@endphp

<div class="participants-progress {{ $textClass ?? '' }}">
    <div class="d-flex align-items-center flex-column">
        <span>{{ $current }}/{{ $max }} <i class="bi bi-person-check-fill ms-1"></i></span>
        <div class="project-progress-bar">
            <div class="project-progress-fill {{ $colorClass }}" style="width: {{ $percentage }}%;"></div>
        </div>
    </div>
</div>
