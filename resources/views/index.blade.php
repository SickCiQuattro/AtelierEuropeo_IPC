@extends('layouts.master')

@section('title', 'Atelier Europeo')

@section('active_home', 'active')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('body')

    <section class="hero-section" style="background-image: url('{{ asset('img/hero-background.jpg') }}');">
        <div class="hero-overlay"></div>
        <div class="container py-5 text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 420px;">
            <h1 class="section-title text-warning mb-3">Atelier Europeo</h1>
            <p class="section-subtitle text-white mb-4">Opportunità per crescere, viaggiare e imparare.</p>
        </div>
    </section>

    <section style="background-color: #F9FAFB;">
        <div class="container py-5 text-center">
            <h2 class="section-title mb-4">Chi Siamo</h2>
            <p class="main-text mx-auto mb-5" style="max-width: 780px;">
                Atelier Europeo è un'associazione senza scopo di lucro nata il 9 maggio 2013,
                con l'obiettivo di promuovere la cittadinanza europea attiva e avvicinare
                i giovani e le realtà locali alle opportunità offerte dall'Unione Europea.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
                <div class="card border-0 card-home stat-card-1 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <p class="stat-number mb-1">10+</p>
                        <p class="stat-label mb-0">anni di esperienza</p>
                    </div>
                </div>
                <div class="card border-0 card-home stat-card-2 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <p class="stat-number mb-1">100+</p>
                        <p class="stat-label mb-0">partner in tutta Europa</p>
                    </div>
                </div>
                <div class="card border-0 card-home stat-card-3 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <p class="stat-number mb-1">200+</p>
                        <p class="stat-label mb-0">progetti realizzati</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('about') }}" class="btn-chi-siamo">
                Scopri chi siamo →
            </a>
        </div>
    </section>

    <section>
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="section-title">Scopri. Partecipa.</h2>
                <p class="section-subtitle">Vivi nuove esperienze ed entra nel cuore dell'Europa.</p>
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-4">

                <div class="card border-0 card-home program-card position-relative text-center">
                    <div style="position: absolute; top: 1px; left: 16px; right: 16px;">
                        <i class="bi bi-heart-fill text-prog-ces d-block" style="font-size: 2rem;"></i>
                        <h5 class="program-card-title mb-1 text-prog-ces">Corpo Europeo di Solidarietà</h5>
                        <p class="program-card-text text-secondary mb-0">
                            Dedica fino a 12 mesi al volontariato in Europa. Aiuta la comunità,
                            sviluppa nuove competenze e vivi un'esperienza che cambierà la tua vita.
                        </p>
                    </div>
                    <hr class="program-card-divider">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 56px; display: flex; align-items: center; justify-content: center;">
                        <button type="button" class="btn-ces" data-bs-toggle="modal" data-bs-target="#infoModal-CES">Scopri di più →</button>
                    </div>
                </div>

                <div class="card border-0 card-home program-card position-relative text-center">
                    <div style="position: absolute; top: 1px; left: 16px; right: 16px;">
                        <i class="bi bi-people-fill text-prog-sg d-block" style="font-size: 2rem;"></i>
                        <h5 class="program-card-title mb-1 text-prog-sg">Scambi Giovanili</h5>
                        <p class="program-card-text text-secondary mb-0">
                            Partecipa a progetti internazionali co-finanziati da Erasmus+.
                            Condividi culture, crea legami in tutta Europa e scopri nuove prospettive.
                        </p>
                    </div>
                    <hr class="program-card-divider">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 56px; display: flex; align-items: center; justify-content: center;">
                        <button type="button" class="btn-sg" data-bs-toggle="modal" data-bs-target="#infoModal-SG">Scopri di più →</button>
                    </div>
                </div>

                <div class="card border-0 card-home program-card position-relative text-center">
                    <div style="position: absolute; top: 1px; left: 16px; right: 16px;">
                        <i class="bi bi-mortarboard-fill text-prog-cf d-block" style="font-size: 2rem;"></i>
                        <h5 class="program-card-title mb-1 text-prog-cf">Corsi di Formazione</h5>
                        <p class="program-card-text text-secondary mb-0">
                            Acquisisci nuove competenze attraverso corsi specializzati.
                            Formazione professionale, networking e strumenti pratici per il tuo futuro
                            nel settore giovanile.
                        </p>
                    </div>
                    <hr class="program-card-divider">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 56px; display: flex; align-items: center; justify-content: center;">
                        <button type="button" class="btn-cf" data-bs-toggle="modal" data-bs-target="#infoModal-CF">Scopri di più →</button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section style="background-color: #F9FAFB;">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="section-title">Creiamo. Connettiamo.</h2>
                <p class="section-subtitle">Scopri i progetti in evidenza.</p>
            </div>

            @if ($featuredProjects->isEmpty())
                <div class="col-12 text-center py-4">
                    <p class="lead">Nessun progetto disponibile al momento.</p>
                </div>
            @else
                <x-project-grid :projects="$featuredProjects" />
            @endif
        </div>
    </section>

    <section>
        <div class="container pt-4 pb-5">

            <div class="text-center mb-4">
                <h2 class="section-title">Viviamo. Raccontiamo.</h2>
                <p class="section-subtitle">
                    Le testimonianze di chi ha vissuto l'Europa con Atelier Europeo.
                </p>
            </div>

            @if ($randomTestimonials && $randomTestimonials->count() > 0)

                {{-- Desktop Carousel --}}
                <div class="d-none d-lg-flex align-items-center justify-content-center gap-4">
                    <button class="testimonial-arrow" type="button" data-bs-target="#testimonialsCarouselDesktop" data-bs-slide="prev">
                        <i class="bi bi-arrow-left"></i>
                    </button>

                    <div class="flex-grow-1" style="max-width: 1140px;">
                        <div id="testimonialsCarouselDesktop" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach ($randomTestimonials->chunk(3) as $index => $chunk)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <div class="d-flex justify-content-center gap-4 py-3">
                                            @foreach ($chunk as $testimonial)
                                                <div class="testimonial-card">
                                                    <div class="quote">""</div>
                                                    <p class="author">{{ $testimonial->author->name }}</p>
                                                    <p class="project">{{ $testimonial->project->title }}</p>
                                                    <p class="text">{{ $testimonial->content }}</p>
                                                    <a href="{{ route('project.show', ['project' => $testimonial->project->id]) }}" class="link">
                                                        Vai al progetto <i class="bi bi-arrow-right project-arrow"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="carousel-indicators testimonial-indicators">
                                @foreach ($randomTestimonials->chunk(3) as $index => $chunk)
                                    <button type="button"
                                        data-bs-target="#testimonialsCarouselDesktop"
                                        data-bs-slide-to="{{ $index }}"
                                        class="{{ $index === 0 ? 'active' : '' }}">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <button class="testimonial-arrow" type="button" data-bs-target="#testimonialsCarouselDesktop" data-bs-slide="next">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

                {{-- Mobile Carousel --}}
                <div class="d-lg-none d-flex align-items-center justify-content-center gap-2">
                    <button class="testimonial-arrow flex-shrink-0" type="button" data-bs-target="#testimonialsCarouselMobile" data-bs-slide="prev">
                        <i class="bi bi-arrow-left"></i>
                    </button>

                    <div class="flex-grow-1 overflow-hidden">
                        <div id="testimonialsCarouselMobile" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach ($randomTestimonials as $index => $testimonial)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <div class="d-flex justify-content-center py-3">
                                            <div class="testimonial-card">
                                                <div class="quote">""</div>
                                                <p class="author">{{ $testimonial->author->name }}</p>
                                                <p class="project">{{ $testimonial->project->title }}</p>
                                                <p class="text">{{ $testimonial->content }}</p>
                                                <a href="{{ route('project.show', ['project' => $testimonial->project->id]) }}" class="link">
                                                    Vai al progetto <i class="bi bi-arrow-right project-arrow"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="carousel-indicators testimonial-indicators">
                                @foreach ($randomTestimonials as $index => $testimonial)
                                    <button type="button"
                                        data-bs-target="#testimonialsCarouselMobile"
                                        data-bs-slide-to="{{ $index }}"
                                        class="{{ $index === 0 ? 'active' : '' }}">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <button class="testimonial-arrow flex-shrink-0" type="button" data-bs-target="#testimonialsCarouselMobile" data-bs-slide="next">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

            @endif
        </div>
    </section>

@endsection