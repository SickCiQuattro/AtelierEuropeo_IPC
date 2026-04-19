@extends('layouts.master')

@section('title', 'AE - Anteprima Progetto')

@section('active_progetti', 'active')

@section('body')
    @php
        $formatHumanDate = function ($value) {
            if (empty($value)) {
                return 'Da definire';
            }

            try {
                return \Carbon\Carbon::parse($value)->format('d/m/Y');
            } catch (\Throwable) {
                return 'Da definire';
            }
        };

        $categoryBadges = [
            'CES' => 'badge-prog-ces',
            'SG' => 'badge-prog-sg',
            'CF' => 'badge-prog-cf',
        ];

        $tag = $previewProject->category->tag ?? 'CES';
        $categoryBadgeClass = $categoryBadges[$tag] ?? 'badge-prog-ces';

        $previewCardProject = clone $previewProject;
        $previewCardProject->status = 'published';
    @endphp

    <style>
        .project-preview-banner {
            position: sticky;
            top: 90px;
            z-index: 1030;
        }

        @media (max-width: 991.98px) {
            .project-preview-banner {
                top: 70px;
            }
        }

        @media (max-width: 575.98px) {
            .project-preview-banner {
                top: 60px;
            }
        }

        .project-preview-card-shell .stretched-link,
        .project-preview-card-shell .btn-favorite {
            pointer-events: none;
        }
    </style>

    <div class="project-preview-banner bg-warning border-bottom border-warning-subtle shadow-sm">
        <div class="container-fluid px-3 px-md-4 py-3 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-2">
            <p class="mb-0 fw-semibold text-dark">
                Modalità Anteprima: stai visualizzando una simulazione. I dati non sono ancora salvati o pubblicati.
            </p>
            <button type="button" class="btn btn-ae btn-ae-dark rounded-pill px-4"
                onclick="if(window.opener){ window.close(); } else { window.history.back(); }">
                <i class="bi bi-x-circle me-2"></i>Chiudi Anteprima
            </button>
        </div>
    </div>

    <main class="container-fluid px-3 px-md-4 py-4">
        <section class="mb-5">
            <h2 class="h4 fw-bold text-primary mb-3">Vista nell'elenco progetti:</h2>
            <div class="bg-light border rounded-4 p-4 p-md-5 d-flex justify-content-center">
                <div class="project-preview-card-shell">
                    <x-project-card :project="$previewCardProject" :showFavoriteIcon="false" />
                </div>
            </div>
        </section>

        <hr class="my-5">

        <section>
            <h2 class="h4 fw-bold text-primary mb-3">Vista della pagina completa:</h2>

            <article class="card border-0 shadow-sm overflow-hidden mb-4" style="border-radius: 1.25rem;">
                <div class="row g-0 align-items-stretch">

                    <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-center bg-white">
                        <div class="d-flex flex-column align-items-start gap-2 mb-3">
                            <span class="badge rounded-pill bg-light border px-3 py-2 text-secondary shadow-sm"
                                style="font-size: 0.85rem;">
                                <i class="bi bi-eye-fill me-1"></i> Stato: Anteprima
                            </span>

                            <span class="d-inline-block position-relative z-3" tabindex="0" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Info sul programma {{ $previewProject->category->name ?? 'Programma' }}">
                                <button type="button" class="{{ $categoryBadgeClass }} border-0 shadow-sm px-3 py-1 mt-1"
                                    data-bs-toggle="modal" data-bs-target="#infoModal-{{ $tag }}" style="font-size: 0.9rem;">
                                    {{ $tag }} <i class="bi bi-info-circle ms-1"></i>
                                </button>
                            </span>
                        </div>

                        <h1 class="display-5 fw-bold mb-3 text-primary" style="line-height: 1.1;">{{ $previewProject->title }}</h1>

                        <p class="lead text-secondary mb-0"
                            style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $previewProject->sum_description }}
                        </p>
                    </div>

                    <div class="col-lg-6 position-relative">
                        <img src="{{ $previewProject->image_url }}" alt="{{ $previewProject->title }}"
                            class="w-100 h-100 object-fit-cover" style="min-height: 350px;">
                    </div>
                </div>
            </article>

            <section class="mb-5">
                <div class="row g-3 g-md-4">
                    <div class="col-6 col-md-3">
                        <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover" style="border-radius: 1.25rem;">
                            <i class="bi bi-person-badge-fill fs-2 mb-2 d-block text-primary"></i>
                            <span class="d-block fw-bold fs-5">{{ $previewProject->requested_people !== null ? $previewProject->requested_people : 'Da definire' }}</span>
                            <span class="small text-secondary fw-semibold">Richiesti</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover" style="border-radius: 1.25rem;">
                            <i class="bi bi-geo-alt-fill fs-2 mb-2 d-block text-primary"></i>
                            <span class="d-block fw-bold fs-6">{{ $previewProject->location }}</span>
                            <span class="small text-secondary fw-semibold">Luogo</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover" style="border-radius: 1.25rem;">
                            <i class="bi bi-calendar2-week-fill fs-2 mb-2 d-block text-primary"></i>
                            <span class="d-block fw-bold fs-6">{{ $formatHumanDate($previewProject->start_date) }}</span>
                            <span class="small text-secondary fw-semibold">Inizio Previsto</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-white border p-4 text-center h-100 shadow-sm transition-hover" style="border-radius: 1.25rem;">
                            <i class="bi bi-calendar-check-fill fs-2 mb-2 d-block text-primary"></i>
                            <span class="d-block fw-bold fs-6">{{ $formatHumanDate($previewProject->expire_date) }}</span>
                            <span class="small text-secondary fw-semibold">Scadenza Iscrizioni</span>
                        </div>
                    </div>
                </div>
            </section>

            <div class="row g-4 mb-5">
                <div class="col-lg-8">
                    <section class="mb-5">
                        <h3 class="h4 fw-bold mb-4 text-primary"><i class="bi bi-journal-text me-2"></i>Il viaggio in pillole</h3>
                        <p class="text-secondary fs-6 mb-0" style="white-space: pre-line; line-height: 1.85;">{{ $previewProject->full_description }}</p>
                    </section>

                    <section>
                        <h3 class="h4 fw-bold mb-4 text-primary"><i class="bi bi-list-check me-2"></i>Requisiti di partecipazione</h3>
                        <p class="text-secondary fs-6 mb-0" style="white-space: pre-line; line-height: 1.85;">{{ $previewProject->requirements }}</p>
                    </section>
                </div>

                <div class="col-lg-4">
                    <div class="bg-white border p-4 shadow-sm mb-4" style="border-radius: 1.25rem;">
                        <h3 class="h6 fw-bold mb-3 text-muted text-uppercase"><i class="bi bi-wallet2 me-2"></i>Condizioni Economiche</h3>
                        <p class="text-secondary small mb-0" style="white-space: pre-line; line-height: 1.6;">{{ $previewProject->travel_conditions }}</p>
                    </div>

                    <div class="bg-light border p-4 shadow-sm" style="border-radius: 1.25rem;">
                        <h3 class="h6 fw-bold mb-3 text-muted text-uppercase"><i class="bi bi-building me-2"></i>L'Associazione</h3>
                        <h4 class="h5 fw-bold mb-2 text-primary">{{ $previewProject->association->name ?? 'Associazione da definire' }}</h4>
                        <p class="text-secondary small mb-0" style="line-height: 1.6;">{{ $previewProject->association->description ?? 'Descrizione non disponibile in anteprima.' }}</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection