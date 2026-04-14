@extends('layouts.master')

@section('title', "AE - Progetti Disponibili")

@section('active_progetti', 'active')

@section('body')
    <main class="container py-4">
        <div class="text-center mb-5">
            <h1>Progetti Disponibili</h1>
            <p class="mb-0">Scopri i nostri progetti di volontariato, scambi giovanili e corsi di formazione in tutta
                Europa.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                @php
                    $hasActiveFilters = request()->filled('q')
                        || in_array(request('sort'), ['expiring_soon', 'latest'], true)
                        || request()->filled('date_from')
                        || request()->filled('date_to')
                        || !empty(request('category', []))
                        || !empty(request('duration', []));
                @endphp

                <div class="search-filter-wrapper position-sticky bg-white py-3 px-3 shadow-sm mb-3">
                    <form method="GET" action="{{ route('project.index') }}">
                        @if (request()->filled('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif

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
                            <span class="input-group-text" id="project-search-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-search" viewBox="0 0 16 16">
                                    <path
                                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                                </svg>
                            </span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Titolo, descrizione, paese..." aria-label="Ricerca progetti"
                                aria-describedby="project-search-icon">
                            <button type="submit" class="btn btn-outline-secondary">Cerca</button>
                        </div>
                    </form>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#projectFiltersModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-filter" viewBox="0 0 16 16">
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
                                    <a href="{{ route('project.index', $removeQParams) }}"
                                        class="text-white text-decoration-none ms-1" aria-label="Rimuovi filtro ricerca">&times;</a>
                                </span>
                            @endif

                            @if (request('sort') === 'expiring_soon')
                                @php
                                    $removeSortParams = request()->except('sort', 'page');
                                @endphp
                                <span class="badge bg-secondary rounded-pill">
                                    Ordine: In scadenza
                                    <a href="{{ route('project.index', $removeSortParams) }}"
                                        class="text-white text-decoration-none ms-1" aria-label="Rimuovi ordinamento">&times;</a>
                                </span>
                            @elseif (request('sort') === 'latest')
                                @php
                                    $removeSortParams = request()->except('sort', 'page');
                                @endphp
                                <span class="badge bg-secondary rounded-pill">
                                    Ordine: Più recenti
                                    <a href="{{ route('project.index', $removeSortParams) }}"
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
                                        <a href="{{ route('project.index', $removeDurationParams) }}"
                                            class="text-white text-decoration-none ms-1" aria-label="Rimuovi durata breve">&times;</a>
                                    </span>
                                @elseif ($duration === 'medium')
                                    <span class="badge bg-secondary rounded-pill">
                                        Durata: Media
                                        <a href="{{ route('project.index', $removeDurationParams) }}"
                                            class="text-white text-decoration-none ms-1" aria-label="Rimuovi durata media">&times;</a>
                                    </span>
                                @elseif ($duration === 'long')
                                    <span class="badge bg-secondary rounded-pill">
                                        Durata: Lunga
                                        <a href="{{ route('project.index', $removeDurationParams) }}"
                                            class="text-white text-decoration-none ms-1" aria-label="Rimuovi durata lunga">&times;</a>
                                    </span>
                                @elseif ($duration === 'very_long')
                                    <span class="badge bg-secondary rounded-pill">
                                        Durata: Molto lunga
                                        <a href="{{ route('project.index', $removeDurationParams) }}"
                                            class="text-white text-decoration-none ms-1"
                                            aria-label="Rimuovi durata molto lunga">&times;</a>
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
                                        <a href="{{ route('project.index', $removeCategoryParams) }}"
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
                                    <a href="{{ route('project.index', $removeDateFromParams) }}"
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
                                    <a href="{{ route('project.index', $removeDateToParams) }}"
                                        class="text-white text-decoration-none ms-1"
                                        aria-label="Rimuovi data fine periodo">&times;</a>
                                </span>
                            @endif

                                <a href="{{ route('project.index') }}" class="btn btn-sm btn-outline-danger">Rimuovi tutti i
                                    filtri</a>
                            </div>
                        </div>
                    @endif
                </div>
        @if ($projects->isEmpty())
            <div class="my-4">
                <div class="text-center mb-0" role="status">
                    Nessun progetto trovato con i criteri selezionati.
                </div>
            </div>
        @else
            <div class="my-4">
                <x-project-grid :projects="$projects" />
            </div>

            <div class="d-flex justify-content-center mt-5 mb-5">
                {{ $projects->links() }}
            </div>
        @endif
            </div>
        </div>

        <div class="modal fade" id="projectFiltersModal" tabindex="-1" aria-labelledby="projectFiltersModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectFiltersModalLabel">Filtri Progetti</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form method="GET" action="{{ route('project.index') }}">
                        @if (request()->filled('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif

                        <div class="modal-body">
                            <input type="hidden" name="q" value="{{ request('q') }}">

                            <div class="mb-4">
                                <label for="sort" class="form-label fw-semibold">Ordina per</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="relevance" {{ request('sort', 'relevance') === 'relevance' ? 'selected' : '' }}>Rilevanza</option>
                                    <option value="expiring_soon" {{ request('sort') === 'expiring_soon' ? 'selected' : '' }}>
                                        In scadenza</option>
                                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Più recenti
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
                            <a href="{{ route('project.index') }}" class="btn btn-outline-secondary">Cancella filtri</a>
                            <button type="submit" class="btn btn-success">Applica filtri</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <style>
        .search-filter-wrapper {
            top: 56px;
            /* Adattare ai pixel esatti dell'altezza della Navbar su mobile */
            z-index: 1020;
        }

        @media (min-width: 992px) {
            .search-filter-wrapper {
                position: static !important;
                background-color: transparent !important;
                box-shadow: none !important;
                border: none !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }
    </style>
@endsection