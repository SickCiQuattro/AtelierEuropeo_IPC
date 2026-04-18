@extends('layouts.master')

@section('title', 'Atelier Europeo')

@section('active_home', 'active')

@section('body')

    <div class="home-page">

        <section class="hero-section" style="background-image: url('{{ asset('img/hero-background.jpg') }}');">
            <div class="hero-overlay"></div>
            <div class="container py-5 text-center d-flex flex-column align-items-center justify-content-center"
                style="min-height: 420px;">
                <h1 class="fw-bold text-warning mb-3 home-hero-title">Atelier Europeo</h1>
                <p class="text-white col-md-8 mx-auto home-hero-subtitle">Opportunità per crescere, viaggiare e
                    imparare.</p>
                <a href="{{ route('project.index') }}"
                    class="btn btn-ae btn-ae-warning btn-ae-pill px-4 py-2 mt-3 shadow-sm">
                    Trova la tua opportunità
                </a>
            </div>
        </section>

        <section style="background-color: #F9FAFB;">
            <div class="container py-5 text-center">
                <h2 class="section-title text-dark mb-4">Chi Siamo</h2>
                <p class="main-text mx-auto mb-5" style="max-width: 780px;">
                    Atelier Europeo è un'associazione senza scopo di lucro nata il 9 maggio 2013,
                    con l'obiettivo di promuovere la cittadinanza europea attiva e avvicinare
                    i giovani e le realtà locali alle opportunità offerte dall'Unione Europea.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-4 mb-5">
                    <div
                        class="card border-0 shadow-sm rounded-4 card-home stat-card d-flex align-items-center justify-content-center">
                        <div class="text-center">
                            <p class="stat-number text-dark mb-1">10+</p>
                            <p class="main-text mb-0">anni di esperienza</p>
                        </div>
                    </div>
                    <div
                        class="card border-0 shadow-sm rounded-4 card-home stat-card d-flex align-items-center justify-content-center">
                        <div class="text-center">
                            <p class="stat-number text-dark mb-1">100+</p>
                            <p class="main-text mb-0">partner in tutta Europa</p>
                        </div>
                    </div>
                    <div
                        class="card border-0 shadow-sm rounded-4 card-home stat-card d-flex align-items-center justify-content-center">
                        <div class="text-center">
                            <p class="stat-number text-dark mb-1">200+</p>
                            <p class="main-text mb-0">progetti realizzati</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('about') }}" class="btn btn-ae btn-ae-pill btn-ae-outline-primary px-4 py-2">
                    Scopri chi siamo <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                </a>
            </div>
        </section>

        <section>
            <div class="container py-5">
                <div class="text-center mb-5">
                    <h2 class="section-title text-dark">Scopri. Partecipa.</h2>
                    <p class="section-subtitle">Vivi nuove esperienze ed entra nel cuore dell'Europa.</p>
                </div>
                <div class="program-cards-grid">

                    <div class="card card-home program-card text-center h-100">
                        <div class="card-body d-flex flex-column align-items-center">
                            <i class="bi bi-heart-fill text-prog-ces d-block program-card-icon"></i>
                            <h5 class="program-card-title mb-2 text-prog-ces">Corpo Europeo di Solidarietà</h5>
                            <p class="program-card-text text-secondary mb-0">
                                Dedica fino a 12 mesi al volontariato in Europa. Aiuta la comunità,
                                sviluppa nuove competenze e vivi un'esperienza che cambierà la tua vita.
                            </p>
                        </div>
                        <div class="card-footer program-card-footer bg-transparent border-0">
                            <button type="button" class="btn btn-ae btn-ae-square btn-prog-ces-outline"
                                data-bs-toggle="modal" data-bs-target="#infoModal-CES">Scopri di più <i
                                    class="bi bi-arrow-right ms-1" aria-hidden="true"></i></button>
                        </div>
                    </div>

                    <div class="card card-home program-card text-center h-100">
                        <div class="card-body d-flex flex-column align-items-center">
                            <i class="bi bi-people-fill text-prog-sg d-block program-card-icon"></i>
                            <h5 class="program-card-title mb-2 text-prog-sg">Scambi Giovanili</h5>
                            <p class="program-card-text text-secondary mb-0">
                                Partecipa a progetti internazionali co-finanziati da Erasmus+.
                                Condividi culture, crea legami in tutta Europa e scopri nuove prospettive.
                            </p>
                        </div>
                        <div class="card-footer program-card-footer bg-transparent border-0">
                            <button type="button" class="btn btn-ae btn-ae-square btn-prog-sg-outline"
                                data-bs-toggle="modal" data-bs-target="#infoModal-SG">Scopri di più <i
                                    class="bi bi-arrow-right ms-1" aria-hidden="true"></i></button>
                        </div>
                    </div>

                    <div class="card card-home program-card text-center h-100">
                        <div class="card-body d-flex flex-column align-items-center">
                            <i class="bi bi-mortarboard-fill text-prog-cf d-block program-card-icon"></i>
                            <h5 class="program-card-title mb-2 text-prog-cf">Corsi di Formazione</h5>
                            <p class="program-card-text text-secondary mb-0">
                                Acquisisci nuove competenze attraverso corsi specializzati.
                                Formazione professionale, networking e strumenti pratici per il tuo futuro
                                nel settore giovanile.
                            </p>
                        </div>
                        <div class="card-footer program-card-footer bg-transparent border-0">
                            <button type="button" class="btn btn-ae btn-ae-square btn-prog-cf-outline"
                                data-bs-toggle="modal" data-bs-target="#infoModal-CF">Scopri di più <i
                                    class="bi bi-arrow-right ms-1" aria-hidden="true"></i></button>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section style="background-color: #F9FAFB;">
            <div class="container py-5">
                <div class="text-center mb-5">
                    <h2 class="section-title text-dark">Creiamo. Connettiamo.</h2>
                    <p class="section-subtitle">Scopri i progetti in evidenza.</p>
                </div>

                @if ($featuredProjects->isEmpty())
                    <div class="col-12 text-center py-4">
                        <p class="lead">Nessun progetto disponibile al momento.</p>
                    </div>
                @else
                    <div class="featured-projects-grid">
                        <x-project-grid :projects="$featuredProjects" />
                    </div>
                @endif
                <div class="text-center mt-3">
                    <a href="{{ route('project.index') }}" class="btn btn-ae btn-ae-pill btn-ae-outline-primary px-4 py-2">
                        Vedi tutti i progetti disponibili <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </section>

        <section>
            <div class="container pt-4 pb-5">

                <div class="text-center mb-4">
                    <h2 class="section-title text-dark">Viviamo. Raccontiamo.</h2>
                    <p class="section-subtitle">
                        Le testimonianze di chi ha vissuto l'Europa con Atelier Europeo.
                    </p>
                </div>

                @if ($randomTestimonials && $randomTestimonials->count() > 0)

                    {{-- Desktop Carousel --}}
                    <div class="d-none d-lg-block">
                        <div id="testimonialsCarouselDesktop" class="carousel slide carousel-dark testimonials-carousel"
                            data-bs-ride="carousel">
                            <div class="carousel-inner px-4 px-md-5">
                                @foreach ($randomTestimonials->chunk(3) as $index => $chunk)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <div class="d-flex justify-content-center gap-4 py-3">
                                            @foreach ($chunk as $testimonial)
                                                <div class="testimonial-card">
                                                    <div class="quote"><i class="bi bi-quote"></i></div>
                                                    <p class="author">{{ $testimonial->author->name }}</p>
                                                    <p class="project">{{ $testimonial->project->title }}</p>
                                                    <p class="text">{{ $testimonial->content }}</p>
                                                    <a href="{{ route('project.show', ['project' => $testimonial->project->id]) }}"
                                                        class="link">
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
                                    <button type="button" data-bs-target="#testimonialsCarouselDesktop"
                                        data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}">
                                    </button>
                                @endforeach
                            </div>

                            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarouselDesktop"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Precedente</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarouselDesktop"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Successivo</span>
                            </button>
                        </div>
                    </div>

                    {{-- Mobile Carousel --}}
                    <div class="d-lg-none d-block">
                        <div id="testimonialsCarouselMobile" class="carousel slide carousel-dark testimonials-carousel"
                            data-bs-ride="carousel">
                            <div class="carousel-inner px-4 px-md-5">
                                @foreach ($randomTestimonials as $index => $testimonial)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <div class="d-flex justify-content-center py-3">
                                            <div class="testimonial-card">
                                                <div class="quote">""</div>
                                                <p class="author">{{ $testimonial->author->name }}</p>
                                                <p class="project">{{ $testimonial->project->title }}</p>
                                                <p class="text">{{ $testimonial->content }}</p>
                                                <a href="{{ route('project.show', ['project' => $testimonial->project->id]) }}"
                                                    class="link">
                                                    Vai al progetto <i class="bi bi-arrow-right project-arrow"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="carousel-indicators testimonial-indicators">
                                @foreach ($randomTestimonials as $index => $testimonial)
                                    <button type="button" data-bs-target="#testimonialsCarouselMobile"
                                        data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}">
                                    </button>
                                @endforeach
                            </div>

                            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarouselMobile"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Precedente</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarouselMobile"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Successivo</span>
                            </button>
                        </div>
                    </div>

                @endif
            </div>
        </section>

    </div>

@endsection