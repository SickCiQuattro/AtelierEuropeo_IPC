@extends('layouts.master')

@section('title', 'AE - I Miei Preferiti')

@section('active_preferiti', 'active')

@section('body')
    <main class="container py-4 mb-5 projects-list-page">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-dark mb-3">I Miei Preferiti</h1>
            <p class="lead text-body-secondary col-md-8 mx-auto">Gestisci i progetti che hai salvato per consultarli o
                candidarti in un secondo momento.</p>
        </div>

        @php
            $hasActiveFilters = request()->filled('q')
                || in_array(request('sort'), ['expiring_soon', 'latest'], true)
                || request()->filled('date_from')
                || request()->filled('date_to')
                || !empty(request('category', []))
                || !empty(request('duration', []));
        @endphp

        @if ($hasAnyFavorites)
            <div class="search-filter-wrapper position-sticky bg-white py-3 px-3 shadow-sm mb-3">
                <form method="GET" action="{{ route('favorites.index') }}">
                    @foreach ((array) request('category', []) as $categoryId)
                        <input type="hidden" name="category[]" value="{{ $categoryId }}">
                    @endforeach

                    @foreach ((array) request('duration', []) as $duration)
                        <input type="hidden" name="duration[]" value="{{ $duration }}">
                    @endforeach

                    @if (request()->filled('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    @if (request()->filled('date_from'))
                        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    @endif

                    @if (request()->filled('date_to'))
                        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    @endif

                    <div class="input-group">
                        <span class="input-group-text" id="favorite-search-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-search" viewBox="0 0 16 16">
                                <path
                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                            </svg>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                            placeholder="Titolo, descrizione, paese..." aria-label="Ricerca preferiti"
                            aria-describedby="favorite-search-icon">
                        <button type="submit" class="btn btn-ae btn-ae-outline-secondary">Cerca</button>
                    </div>
                </form>

                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-ae btn-ae-outline-secondary" data-bs-toggle="modal"
                        data-bs-target="#favoriteFiltersModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filter"
                            viewBox="0 0 16 16">
                            <path
                                d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5" />
                        </svg>
                        Filtri
                    </button>
                </div>

                @if ($hasActiveFilters)
                    <div class="mt-3">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="fw-semibold">Filtri Attivi:</span>

                            @if (request()->filled('q'))
                                @php
                                    $removeQParams = request()->except('q', 'page');
                                @endphp
                                <span class="badge bg-secondary rounded-pill">
                                    Ricerca: {{ request('q') }}
                                    <a href="{{ route('favorites.index', $removeQParams) }}"
                                        class="text-white text-decoration-none ms-1" aria-label="Rimuovi filtro ricerca">&times;</a>
                                </span>
                            @endif

                            @if (request('sort') === 'expiring_soon')
                                @php
                                    $removeSortParams = request()->except('sort', 'page');
                                @endphp
                                <span class="badge bg-secondary rounded-pill">
                                    Ordine: In scadenza
                                    <a href="{{ route('favorites.index', $removeSortParams) }}"
                                        class="text-white text-decoration-none ms-1" aria-label="Rimuovi ordinamento">&times;</a>
                                </span>
                            @elseif (request('sort') === 'latest')
                                @php
                                    $removeSortParams = request()->except('sort', 'page');
                                @endphp
                                <span class="badge bg-secondary rounded-pill">
                                    Ordine: Piu recenti
                                    <a href="{{ route('favorites.index', $removeSortParams) }}"
                                        class="text-white text-decoration-none ms-1" aria-label="Rimuovi ordinamento">&times;</a>
                                </span>
                            @endif

                            @foreach ((array) request('duration', []) as $duration)
                                @php
                                    $newDurations = array_values(array_filter((array) request('duration', []), fn($d) => $d !== $duration));
                                    $removeDurationParams = request()->except('duration', 'page');
                                    if (!empty($newDurations)) {
                                        $removeDurationParams['duration'] = $newDurations;
                                    }
                                @endphp
                                @if ($duration === 'short')
                                    <span class="badge bg-secondary rounded-pill">
                                        Durata: Breve
                                        <a href="{{ route('favorites.index', $removeDurationParams) }}"
                                            class="text-white text-decoration-none ms-1" aria-label="Rimuovi durata breve">&times;</a>
                                    </span>
                                @elseif ($duration === 'medium')
                                    <span class="badge bg-secondary rounded-pill">
                                        Durata: Media
                                        <a href="{{ route('favorites.index', $removeDurationParams) }}"
                                            class="text-white text-decoration-none ms-1" aria-label="Rimuovi durata media">&times;</a>
                                    </span>
                                @elseif ($duration === 'long')
                                    <span class="badge bg-secondary rounded-pill">
                                        Durata: Lunga
                                        <a href="{{ route('favorites.index', $removeDurationParams) }}"
                                            class="text-white text-decoration-none ms-1" aria-label="Rimuovi durata lunga">&times;</a>
                                    </span>
                                @elseif ($duration === 'very_long')
                                    <span class="badge bg-secondary rounded-pill">
                                        Durata: Molto lunga
                                        <a href="{{ route('favorites.index', $removeDurationParams) }}"
                                            class="text-white text-decoration-none ms-1" aria-label="Rimuovi durata molto lunga">&times;</a>
                                    </span>
                                @endif
                            @endforeach

                            @foreach ((array) request('category', []) as $categoryId)
                                @php
                                    $activeCategory = $categories->firstWhere('id', (int) $categoryId);
                                    $newCategories = array_values(array_filter((array) request('category', []), fn($id) => (string) $id !== (string) $categoryId));
                                    $removeCategoryParams = request()->except('category', 'page');
                                    if (!empty($newCategories)) {
                                        $removeCategoryParams['category'] = $newCategories;
                                    }
                                @endphp
                                @if ($activeCategory)
                                    <span class="badge bg-secondary rounded-pill">
                                        Categoria: {{ $activeCategory->name }}
                                        <a href="{{ route('favorites.index', $removeCategoryParams) }}"
                                            class="text-white text-decoration-none ms-1"
                                            aria-label="Rimuovi categoria {{ $activeCategory->name }}">&times;</a>
                                    </span>
                                @endif
                            @endforeach

                            @if (request()->filled('date_from'))
                                @php
                                    $removeDateFromParams = request()->except('date_from', 'page');
                                @endphp
                                <span class="badge bg-secondary rounded-pill">
                                    Da: {{ request('date_from') }}
                                    <a href="{{ route('favorites.index', $removeDateFromParams) }}"
                                        class="text-white text-decoration-none ms-1"
                                        aria-label="Rimuovi data inizio periodo">&times;</a>
                                </span>
                            @endif

                            @if (request()->filled('date_to'))
                                @php
                                    $removeDateToParams = request()->except('date_to', 'page');
                                @endphp
                                <span class="badge bg-secondary rounded-pill">
                                    A: {{ request('date_to') }}
                                    <a href="{{ route('favorites.index', $removeDateToParams) }}"
                                        class="text-white text-decoration-none ms-1" aria-label="Rimuovi data fine periodo">&times;</a>
                                </span>
                            @endif

                            <a href="{{ route('favorites.index') }}" class="btn btn-ae btn-sm btn-ae-outline-danger">Rimuovi
                                tutti i
                                filtri</a>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if (!$hasAnyFavorites)
            <div class="text-center py-5 my-5 bg-light rounded-4">
                <i class="bi bi-heartbreak text-muted mb-3" style="font-size: 4rem; opacity: 0.5;"></i>
                <h3 class="section-title">Nessun progetto salvato</h3>
                <p class="main-text mb-4 mx-auto" style="max-width: 500px;">
                    Non hai ancora aggiunto alcun progetto ai tuoi preferiti. Esplora le opportunita disponibili in Europa e
                    salva quelle che ti interessano di piu cliccando sul cuore.
                </p>
                <a href="{{ route('project.index') }}" class="btn btn-ae-primary btn-lg px-4 rounded-pill shadow-sm">
                    <i class="bi bi-search me-2"></i>Esplora i Progetti
                </a>
            </div>
        @elseif ($favoriteProjects->isEmpty())
            <div class="my-4">
                <div class="text-center mb-0" role="status">
                    Nessun progetto trovato con i criteri selezionati.
                </div>
            </div>
        @else
            <div class="my-4">
                <x-project-grid :projects="$favoriteProjects" />
            </div>

            @if ($favoriteProjects->hasPages())
                <div class="d-flex justify-content-center mt-5 mb-5">
                    {{ $favoriteProjects->links() }}
                </div>
            @endif
        @endif

        @if ($hasAnyFavorites)
            <div class="modal fade" id="favoriteFiltersModal" tabindex="-1" aria-labelledby="favoriteFiltersModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="favoriteFiltersModalLabel">Filtri Preferiti</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form method="GET" action="{{ route('favorites.index') }}">
                            <div class="modal-body">
                                <input type="hidden" name="q" value="{{ request('q') }}">

                                <div class="mb-4">
                                    <label for="sort" class="form-label fw-semibold">Ordina per</label>
                                    <select class="form-select" id="sort" name="sort">
                                        <option value="relevance" {{ request('sort', 'relevance') === 'relevance' ? 'selected' : '' }}>Rilevanza</option>
                                        <option value="expiring_soon" {{ request('sort') === 'expiring_soon' ? 'selected' : '' }}>
                                            In scadenza</option>
                                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Piu recenti
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <p class="form-label fw-semibold mb-2">Durata</p>
                                    <div class="row g-2">
                                        <div class="col-12 col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="duration[]" value="short"
                                                    id="duration-short" {{ in_array('short', (array) request('duration', []), true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="duration-short">Breve (&lt; 15
                                                    giorni)</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="duration[]" value="medium"
                                                    id="duration-medium" {{ in_array('medium', (array) request('duration', []), true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="duration-medium">Media (15 - 60
                                                    giorni)</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="duration[]" value="long"
                                                    id="duration-long" {{ in_array('long', (array) request('duration', []), true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="duration-long">Lunga (60 - 180
                                                    giorni)</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="duration[]"
                                                    value="very_long" id="duration-very-long" {{ in_array('very_long', (array) request('duration', []), true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="duration-very-long">Molto lunga (&gt; 180
                                                    giorni)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <p class="form-label fw-semibold mb-2">Periodo</p>
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label for="date_from" class="form-label">Da</label>
                                            <input type="date" class="form-control" id="date_from" name="date_from"
                                                value="{{ request('date_from') }}">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="date_to" class="form-label">A</label>
                                            <input type="date" class="form-control" id="date_to" name="date_to"
                                                value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-1">
                                    <p class="form-label fw-semibold mb-2">Categoria</p>
                                    <div class="row g-2">
                                        @foreach ($categories as $category)
                                            <div class="col-12 col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="category[]"
                                                        value="{{ $category->id }}" id="category-{{ $category->id }}" {{ in_array((string) $category->id, array_map('strval', (array) request('category', [])), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="category-{{ $category->id }}">{{ $category->name }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <a href="{{ route('favorites.index') }}" class="btn btn-ae btn-ae-outline-secondary">Cancella
                                    filtri</a>
                                <button type="submit" class="btn btn-ae btn-ae-success">Applica filtri</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </main>
@endsection