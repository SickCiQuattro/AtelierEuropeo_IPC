@props([
    'projects',
    'showFavoriteIcon' => true,
    'source' => null,
])

<div class="project-grid my-5">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($projects as $project)
            <div class="col d-flex justify-content-center">
                {{-- Qui richiami il componente della singola card che abbiamo creato prima --}}
                <x-project-card :project="$project" :showFavoriteIcon="$showFavoriteIcon" :source="$source" />
            </div>
        @endforeach
    </div>
</div>