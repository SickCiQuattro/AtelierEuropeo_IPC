<!DOCTYPE html>

<html>

<head>
    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('img/ae-icon.svg') }}" type="image/svg+xml">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ url('/') }}/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Alpine.js per funzionalità interattive -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Bootstrap Icons-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Flag Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('img/ae-icon.svg') }}" alt="Atelier Europeo" height="40">
            </a>

            <!-- Contenitore per toggle button e dropdown utente -->
            <div class="d-flex align-items-center order-lg-2">
                @if (auth()->check())
                    <!-- Dropdown utente sempre visibile -->
                    <div class="dropdown me-2">
                        <button class="btn btn-dark dropdown-toggle d-flex align-items-center gap-2" type="button"
                            id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center gap-2">
                                <!-- Icona utente -->
                                <i class="bi bi-person-fill fs-5"></i>
                                <!-- Nome utente (nascosto su mobile) -->
                                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="bi bi-person me-1"></i>
                                    {{ auth()->user()->name }}
                                </h6>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <!-- Profilo utente -->
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person-gear me-2"></i>
                                    @if(app()->getLocale() === 'it')
                                        Il Mio Profilo
                                    @else
                                        My Profile
                                    @endif
                                </a>
                            </li>

                            @if (auth()->user()->role !== 'admin')
                                <!-- Le mie candidature (solo per utenti non admin) -->
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('applications.index') }}">
                                        <i class="bi bi-file-earmark-text me-2"></i>
                                        @if(app()->getLocale() === 'it')
                                            Le Mie Candidature
                                        @else
                                            My Applications
                                        @endif
                                    </a>
                                </li>
                                <!-- I miei preferiti (solo per utenti non admin) -->
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('favorites.index') }}">
                                        <i class="bi bi-heart-fill me-2"></i>
                                        @if(app()->getLocale() === 'it')
                                            I Miei Preferiti
                                        @else
                                            My Favorites
                                        @endif
                                    </a>
                                </li>
                            @else
                                <!-- Dashboard admin (solo per admin) -->
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>
                                        @if(app()->getLocale() === 'it')
                                            Dashboard Admin
                                        @else
                                            Admin Dashboard
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('project.create') }}">
                                        <i class="bi bi-plus-circle me-2"></i>
                                        @if(app()->getLocale() === 'it')
                                            Nuovo Progetto
                                        @else
                                            New Project
                                        @endif
                                    </a>
                                </li>
                            @endif

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <!-- Logout -->
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>
                                        @if(app()->getLocale() === 'it')
                                            Esci
                                        @else
                                            Logout
                                        @endif
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <!-- Bottoni di accesso per utenti non autenticati -->
                    <div class="d-flex gap-2 me-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-light">{{ __('common.login') }}</a>
                        <a href="{{ route('register') }}" class="btn btn-warning">{{ __('common.register') }}</a>
                    </div>
                @endif

                <!-- Language Selector -->
                <div class="dropdown me-3">
                    <button class="btn btn-light dropdown-toggle btn-sm" type="button" id="languageDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        @if(app()->getLocale() === 'it')
                            <span class="fi fi-it" style="font-size: 0.8em;"></span> IT
                        @else
                            <span class="fi fi-gb" style="font-size: 0.8em;"></span> EN
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('lang.switch', 'it') }}">
                                <span class="fi fi-it" style="font-size: 0.8em;"></span> {{ __('common.italian') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">
                                <span class="fi fi-gb" style="font-size: 0.8em;"></span> {{ __('common.english') }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Toggle button della navbar -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Menu collassabile -->
            <div class="collapse navbar-collapse order-lg-1" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link @yield('active_home')" aria-current="page"
                            href="{{ route('home') }}">{{ __('common.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @yield('active_chi-siamo')" aria-current="page"
                            href="{{ route('about') }}">{{ __('common.about') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @yield('active_portfolio')" aria-current="page"
                            href="{{ route('project.portfolio') }}">{{ __('projects.title') }} Portfolio</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle @yield('active_viaggiare')" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            @if(app()->getLocale() === 'it')
                                Viaggiare all'Estero
                            @else
                                Travel Abroad
                            @endif
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('corpo-europeo')}}">
                                    @if(app()->getLocale() === 'it')
                                        Corpo Europeo di Solidarietà
                                    @else
                                        European Solidarity Corps
                                    @endif
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('scambi-giovanili')}}">
                                    @if(app()->getLocale() === 'it')
                                        Scambi Giovanili
                                    @else
                                        Youth Exchanges
                                    @endif
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('corsi-formazione')}}">
                                    @if(app()->getLocale() === 'it')
                                        Corsi di Formazione
                                    @else
                                        Training Courses
                                    @endif
                                </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @yield('active_progetti')" aria-current="page"
                            href="{{ route('project.index') }}">
                            @if(app()->getLocale() === 'it')
                                Progetti disponibili
                            @else
                                Available Projects
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @yield('active_contatti')" aria-current="page"
                            href="{{ route('contact') }}">{{ __('common.contact') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @yield('breadcrumb')

    @yield('body')

    <!-- Footer -->
    <footer class="bg-dark text-light mt-auto" data-bs-theme="dark">
        <div class="container py-4">
            <div class="row g-4">
                <!-- Colonna Logo e Descrizione -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/ae-icon.svg') }}" alt="Atelier Europeo" height="40" class="me-2">
                        <h5 class="mb-0 text-warning">Atelier Europeo</h5>
                    </div>
                    <p class="text-light-emphasis small mb-3">
                        @if(app()->getLocale() === 'it')
                            Organizziamo progetti di mobilità europea per giovani: Corpo Europeo di Solidarietà,
                            Scambi Giovanili e Corsi di Formazione per costruire un'Europa più inclusiva e solidale.
                        @else
                            We organize European mobility projects for young people: European Solidarity Corps,
                            Youth Exchanges and Training Courses to build a more inclusive and supportive Europe.
                        @endif
                    </p>
                    <!-- Social Media -->
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/AtelierEuropeo/" target="_blank"
                            class="btn btn-outline-light btn-sm">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://www.instagram.com/ateliereuropeo/" target="_blank"
                            class="btn btn-outline-light btn-sm">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/atelier-europeo/" target="_blank"
                            class="btn btn-outline-light btn-sm">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    </div>
                </div>

                <!-- Colonna Link Rapidi -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="text-white mb-3">
                        @if(app()->getLocale() === 'it')
                            Link Rapidi
                        @else
                            Quick Links
                        @endif
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link p-1">
                                {{ __('common.home') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('about') }}" class="nav-link p-1">
                                {{ __('common.about') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('project.portfolio') }}" class="nav-link p-1">
                                @if(app()->getLocale() === 'it')
                                    Portfolio progetti
                                @else
                                    Projects Portfolio
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('project.index') }}" class="nav-link p-1">
                                @if(app()->getLocale() === 'it')
                                    Progetti disponibili
                                @else
                                    Available Projects
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('contact') }}" class="nav-link p-1">
                                {{ __('common.contact') }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Colonna Programmi -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white mb-3">
                        @if(app()->getLocale() === 'it')
                            I Nostri Programmi
                        @else
                            Our Programs
                        @endif
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('corpo-europeo') }}" class="nav-link p-1">
                                @if(app()->getLocale() === 'it')
                                    Corpo Europeo di Solidarietà
                                @else
                                    European Solidarity Corps
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('scambi-giovanili') }}" class="nav-link p-1">
                                @if(app()->getLocale() === 'it')
                                    Scambi Giovanili
                                @else
                                    Youth Exchanges
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('corsi-formazione') }}" class="nav-link p-1">
                                @if(app()->getLocale() === 'it')
                                    Corsi di Formazione
                                @else
                                    Training Courses
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Colonna Contatti -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white mb-3">
                        @if(app()->getLocale() === 'it')
                            Contatti
                        @else
                            Contact
                        @endif
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item d-flex align-items-start py-1">
                            <i class="bi bi-geo-alt-fill text-white me-2 mt-1"></i>
                            <div>
                                <a href="https://maps.google.com/?q=Via+Salgari+43/b,+25125+Brescia,+Italia"
                                    target="_blank" class="nav-link p-0">
                                    <small>
                                        C/o CSV, Via Salgari 43/b<br>
                                        25125 Brescia, Italia
                                    </small>
                                </a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="tel:+390302284900" class="nav-link p-1 d-flex align-items-center">
                                <i class="bi bi-telephone-fill text-white me-2"></i>
                                +39 030 22 84 900
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="mailto:info@ateliereuropeo.eu" class="nav-link p-1 d-flex align-items-center">
                                <i class="bi bi-envelope-fill text-white me-2"></i>
                                info@ateliereuropeo.eu
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Separatore -->
            <hr class="my-4 border-secondary">

            <!-- Copyright e Info Legali -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-light-emphasis">
                        @if(app()->getLocale() === 'it')
                            © {{ date('Y') }} Atelier Europeo. Tutti i diritti riservati.
                        @else
                            © {{ date('Y') }} Atelier Europeo. All rights reserved.
                        @endif
                    </small>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex flex-wrap justify-content-md-end gap-3">
                        <a href="#" class="nav-link text-light-emphasis p-0">
                            @if(app()->getLocale() === 'it')
                                Privacy Policy
                            @else
                                Privacy Policy
                            @endif
                        </a>
                        <a href="#" class="nav-link text-light-emphasis p-0">
                            @if(app()->getLocale() === 'it')
                                Cookie Policy
                            @else
                                Cookie Policy
                            @endif
                        </a>
                        <small class="text-light-emphasis">
                            P.IVA: 03747110983
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')

</body>

</html>