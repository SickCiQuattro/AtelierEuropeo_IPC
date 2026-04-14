<!DOCTYPE html>

<html>

<head>
    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('img/ae-icon.svg') }}" type="image/svg+xml">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS -->
    {{--
    <link rel="stylesheet" href="{{ url('/') }}/css/style.css"> --}}
    <link rel="stylesheet" href="{{ url('/') }}/css/project.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

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
    @php
        $isAdmin = auth()->check() && auth()->user()->isAdmin();
        $isAdminPreview = $isAdmin && session('admin_user_view', false) && !request()->routeIs('admin.*');
        $showAdminNav = $isAdmin && !$isAdminPreview;
        $brandRoute = $showAdminNav ? route('admin.dashboard') : route('home');
        $isWideLocale = in_array(app()->getLocale(), ['fr', 'es'], true);
    @endphp

    <nav class="navbar navbar-expand-lg navbar-dark main-navbar {{ $isWideLocale ? 'main-navbar--wide-locale' : '' }}">
        <div class="container d-flex flex-wrap align-items-center main-navbar-inner">
            <!-- Logo (Sinistra) -->
            <a class="navbar-brand d-flex align-items-center me-lg-4" href="{{ $brandRoute }}">
                <img src="{{ asset('img/ae-icon.svg') }}" alt="Atelier Europeo" class="navbar-logo">

                @if ($showAdminNav)
                    <span class="ms-2 small text-warning fw-bold">{{ __('master.admin_panel') }}</span>
                @endif
            </a>

            <!-- Destra: utente + lingua + hamburger (sempre visibili su mobile) -->
            <div class="d-flex align-items-center gap-2 ms-auto order-2 order-lg-3 navbar-actions">
                @guest
                    <!-- Variante 1: GUEST -->
                    <div class="dropdown">
                        <button class="btn border-0 text-white p-0 navbar-icon-trigger d-inline-flex align-items-center"
                            type="button" id="guestUserDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            aria-label="{{ __('master.aria.user_menu') }}">
                            <i class="bi bi-person-fill fs-5"></i>
                            <i class="bi bi-caret-down-fill ms-1 small"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3 shadow border-0 navbar-access-dropdown"
                            aria-labelledby="guestUserDropdown">
                            <div class="d-grid gap-2" style="min-width: 8rem;">
                                <a href="{{ route('login') }}"
                                    class="btn btn-ae btn-ae-square btn-ae-primary d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                    <span>{{ __('master.auth.login') }}</span>
                                </a>
                                <a href="{{ route('register') }}"
                                    class="btn btn-ae btn-ae-square btn-ae-warning d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-plus-square"></i>
                                    <span>{{ __('master.auth.register') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endguest

                @auth
                    @if ($isAdmin)
                        <!-- Variante 3: ADMIN -->
                        <div class="dropdown">
                            <button class="btn btn-outline-warning btn-sm dropdown-toggle fw-semibold" type="button"
                                id="adminUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('master.auth.admin') }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3 shadow border-0"
                                aria-labelledby="adminUserDropdown">
                                <div class="d-grid gap-2" style="min-width: 8rem;">
                                    <a href="{{ route('profile.edit') }}"
                                        class="btn btn-ae btn-ae-square btn-ae-primary d-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-person-fill-gear"></i>
                                        <span>{{ __('master.auth.profile') }}</span>
                                    </a>

                                    @if ($isAdminPreview)
                                        <a href="{{ route('admin.return') }}"
                                            class="btn btn-ae btn-ae-square btn-ae-secondary d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                            <span>{{ __('master.auth.return_admin') }}</span>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.view-user') }}"
                                            class="btn btn-ae btn-ae-square btn-ae-secondary d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-eye-fill"></i>
                                            <span>{{ __('master.auth.user_view') }}</span>
                                        </a>
                                    @endif
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-ae btn-ae-square btn-ae-outline-danger w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-box-arrow-right"></i>
                                            <span>{{ __('master.auth.logout') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Variante 2: USER -->
                        <div class="dropdown">
                            <button class="btn btn-outline-warning btn-sm dropdown-toggle fw-semibold" type="button"
                                id="standardUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->name ?? __('master.auth.user_fallback') }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3 shadow border-0"
                                aria-labelledby="standardUserDropdown">
                                <div class="d-grid gap-2" style="min-width: 8rem;">
                                    <a href="{{ route('profile.edit') }}"
                                        class="btn btn-ae btn-ae-square btn-ae-primary d-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-person-fill-gear"></i>
                                        <span>{{ __('master.auth.profile') }}</span>
                                    </a>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-outline-danger w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-box-arrow-right"></i>
                                            <span>{{ __('master.auth.logout') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth

                <!-- Bottone lingua (apre modale) -->
                <button class="btn border-0 text-white p-0 navbar-icon-trigger navbar-lang-trigger" type="button"
                    data-bs-toggle="modal" data-bs-target="#languageModal"
                    aria-label="{{ __('master.aria.open_language_selector') }}">
                    @if (app()->getLocale() === 'it')
                        <span class="fi fi-it"></span>
                    @elseif(app()->getLocale() === 'en')
                        <span class="fi fi-gb"></span>
                    @elseif(app()->getLocale() === 'es')
                        <span class="fi fi-es"></span>
                    @else
                        <span class="fi fi-fr"></span>
                    @endif
                </button>

                <!-- Hamburger -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbarLinks"
                    aria-controls="mainNavbarLinks" aria-expanded="false"
                    aria-label="{{ __('master.aria.toggle_navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Centro: Link navigazione -->
            <div class="collapse navbar-collapse order-3 order-lg-2 w-100 flex-lg-grow-1 mt-3 mt-lg-0 main-navbar-collapse"
                id="mainNavbarLinks">
                <hr class="d-lg-none border-light opacity-50 mt-0 mb-3">

                <ul class="navbar-nav ms-lg-3 me-lg-auto gap-lg-2">
                    @if ($showAdminNav)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}"
                                href="{{ route('admin.dashboard') }}">{{ __('master.nav.dashboard') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('project.*') ? 'is-active' : '' }}"
                                href="{{ route('project.index') }}">{{ __('master.nav.projects') }}</a>
                        </li>
                        <li class="nav-item">
                            {{-- TODO: inserire route dedicata alla lista candidature admin (globale) --}}
                            <a class="nav-link {{ request()->routeIs('admin.applications.*') ? 'is-active' : '' }}"
                                href="{{ route('admin.dashboard') }}">{{ __('master.nav.applications') }}</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}"
                                href="{{ route('home') }}">{{ __('master.nav.home') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('about') ? 'is-active' : '' }}"
                                href="{{ route('about') }}">{{ __('master.nav.about') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('project.index') ? 'is-active' : '' }}"
                                href="{{ route('project.index') }}">{{ __('master.nav.available_projects') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('project.portfolio') ? 'is-active' : '' }}"
                                href="{{ route('project.portfolio') }}">{{ __('master.nav.project_archive') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contact') ? 'is-active' : '' }}"
                                href="{{ route('contact') }}">{{ __('master.nav.contacts') }}</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Modale Lingua -->
    <div class="modal fade" id="languageModal" tabindex="-1" aria-labelledby="languageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content language-modal-content">
                <div class="modal-header border-0 pb-1">
                    <h5 class="modal-title" id="languageModalLabel">{{ __('master.language_modal.title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('master.aria.close') }}"></button>
                </div>
                <div class="modal-body pt-1">
                    <hr class="language-divider mt-0 mb-3">
                    <p class="text-secondary small mb-3">
                        {{ __('master.language_modal.description') }}
                    </p>
                    <hr class="language-divider mt-0 mb-3">

                    <div class="language-options-grid">
                        {{-- TODO: mantenere/estendere la logica Laravel di cambio lingua --}}
                        <a href="{{ route('lang.switch', ['locale' => 'it']) }}"
                            class="language-option-card p-3 border text-decoration-none">
                            <div class="language-option-head">
                                <strong>{{ __('master.language_modal.options.it.label', [], 'it') }}</strong>
                                <span class="fi fi-it language-option-flag" aria-hidden="true"></span>
                            </div>
                            <small>{{ __('master.language_modal.options.it.description', [], 'it') }}</small>
                        </a>

                        <a href="{{ route('lang.switch', ['locale' => 'en']) }}"
                            class="language-option-card p-3 border text-decoration-none">
                            <div class="language-option-head">
                                <strong>{{ __('master.language_modal.options.en.label', [], 'en') }}</strong>
                                <span class="fi fi-gb language-option-flag" aria-hidden="true"></span>
                            </div>
                            <small>{{ __('master.language_modal.options.en.description', [], 'en') }}</small>
                        </a>

                        <a href="{{ route('lang.switch', ['locale' => 'es']) }}"
                            class="language-option-card p-3 border text-decoration-none">
                            <div class="language-option-head">
                                <strong>{{ __('master.language_modal.options.es.label', [], 'es') }}</strong>
                                <span class="fi fi-es language-option-flag" aria-hidden="true"></span>
                            </div>
                            <small>{{ __('master.language_modal.options.es.description', [], 'es') }}</small>
                        </a>

                        <a href="{{ route('lang.switch', ['locale' => 'fr']) }}"
                            class="language-option-card p-3 border text-decoration-none">
                            <div class="language-option-head">
                                <strong>{{ __('master.language_modal.options.fr.label', [], 'fr') }}</strong>
                                <span class="fi fi-fr language-option-flag" aria-hidden="true"></span>
                            </div>
                            <small>{{ __('master.language_modal.options.fr.description', [], 'fr') }}</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @yield('breadcrumb')

    @yield('body')

    <!-- Footer -->
    <footer class="footer-main bg-primary text-white mt-auto">
        <div class="container py-4 py-md-5">
            <!-- Desktop: griglia 4 colonne -->
            <div class="row g-4 d-none d-md-flex justify-content-between">
                <div class="col-md-5 col-lg-4">
                    <div class="d-flex align-items-center mb-3">
                        <a href="{{ $brandRoute }}" class="d-inline-flex align-items-center text-decoration-none">
                            <img src="{{ asset('img/ae-icon.svg') }}" alt="Atelier Europeo" height="40" class="me-2">
                            <h5 class="mb-0 footer-brand-title">{{ __('master.brand') }}</h5>
                        </a>
                    </div>
                    <p class="footer-text mb-0">
                        {{ __('master.footer.programs_description') }}
                    </p>
                </div>

                <div class="col-md-auto">
                    <h6 class="footer-title mb-3">{{ __('master.footer.quick_links') }}</h6>
                    <ul class="list-unstyled mb-0 d-grid gap-2">
                        <li><a class="footer-link" href="{{ route('home') }}">{{ __('master.nav.home') }}</a></li>
                        <li><a class="footer-link" href="{{ route('about') }}">{{ __('master.nav.about') }}</a></li>
                        <li><a class="footer-link"
                                href="{{ route('project.index') }}">{{ __('master.nav.available_projects') }}</a>
                        </li>
                        <li><a class="footer-link"
                                href="{{ route('project.portfolio') }}">{{ __('master.nav.project_archive') }}</a>
                        </li>
                        <li><a class="footer-link" href="{{ route('contact') }}">{{ __('master.nav.contacts') }}</a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-auto">
                    <h6 class="footer-title mb-3">{{ __('master.footer.contacts') }}</h6>
                    <ul class="list-unstyled mb-0 d-grid gap-2">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-telephone-fill"></i>
                            <a class="footer-link" href="tel:+390302284900">+39 030 22 84 900</a>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-envelope-fill"></i>
                            <a class="footer-link" href="mailto:info@ateliereuropeo.eu">info@ateliereuropeo.eu</a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-auto">
                    <h6 class="footer-title mb-3">{{ __('master.footer.social') }}</h6>
                    <ul class="list-unstyled mb-0 d-grid gap-2">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-facebook"></i>
                            <a class="footer-link" href="https://www.facebook.com/AtelierEuropeo/" target="_blank"
                                rel="noopener noreferrer">Facebook</a>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-instagram"></i>
                            <a class="footer-link" href="https://www.instagram.com/ateliereuropeo/" target="_blank"
                                rel="noopener noreferrer">Instagram</a>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-linkedin"></i>
                            <a class="footer-link" href="https://www.linkedin.com/company/atelier-europeo/"
                                target="_blank" rel="noopener noreferrer">LinkedIn</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Mobile: accordion -->
            <div class="d-md-none d-block">
                <div class="d-flex align-items-start mb-4 px-3">
                    <a href="{{ $brandRoute }}" class="flex-shrink-0 me-3 text-decoration-none mt-1">
                        <img src="{{ asset('img/ae-icon.svg') }}" alt="Atelier Europeo" height="48">
                    </a>

                    <div>
                        <h5 class="mb-1 footer-brand-title">{{ __('master.brand') }}</h5>
                        <p class="footer-text mb-0 small" style="line-height: 1.4;">
                            {{ __('master.footer.programs_description') }}
                        </p>
                    </div>
                </div>

                <hr class="footer-divider">

                <div class="accordion accordion-flush footer-accordion" id="footerAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="footerLinksHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#footerLinksCollapse" aria-expanded="false"
                                aria-controls="footerLinksCollapse">
                                {{ __('master.footer.quick_links') }}
                            </button>
                        </h2>
                        <div id="footerLinksCollapse" class="accordion-collapse collapse"
                            aria-labelledby="footerLinksHeading" data-bs-parent="#footerAccordion">
                            <div class="accordion-body">
                                <ul class="list-unstyled mb-0 d-grid gap-2">
                                    <li><a class="footer-link"
                                            href="{{ route('home') }}">{{ __('master.nav.home') }}</a></li>
                                    <li><a class="footer-link"
                                            href="{{ route('about') }}">{{ __('master.nav.about') }}</a></li>
                                    <li><a class="footer-link"
                                            href="{{ route('project.index') }}">{{ __('master.nav.available_projects') }}</a>
                                    </li>
                                    <li><a class="footer-link"
                                            href="{{ route('project.portfolio') }}">{{ __('master.nav.project_archive') }}</a>
                                    </li>
                                    <li><a class="footer-link"
                                            href="{{ route('contact') }}">{{ __('master.nav.contacts') }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="footerContactsHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#footerContactsCollapse" aria-expanded="false"
                                aria-controls="footerContactsCollapse">
                                {{ __('master.footer.contacts') }}
                            </button>
                        </h2>
                        <div id="footerContactsCollapse" class="accordion-collapse collapse"
                            aria-labelledby="footerContactsHeading" data-bs-parent="#footerAccordion">
                            <div class="accordion-body">
                                <ul class="list-unstyled mb-0 d-grid gap-2">
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="bi bi-telephone-fill"></i>
                                        <a class="footer-link" href="tel:+390302284900">+39 030 22 84 900</a>
                                    </li>
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="bi bi-envelope-fill"></i>
                                        <a class="footer-link"
                                            href="mailto:info@ateliereuropeo.eu">info@ateliereuropeo.eu</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="footerSocialHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#footerSocialCollapse" aria-expanded="false"
                                aria-controls="footerSocialCollapse">
                                {{ __('master.footer.social_links') }}
                            </button>
                        </h2>
                        <div id="footerSocialCollapse" class="accordion-collapse collapse"
                            aria-labelledby="footerSocialHeading" data-bs-parent="#footerAccordion">
                            <div class="accordion-body">
                                <ul class="list-unstyled mb-0 d-grid gap-2">
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="bi bi-facebook"></i>
                                        <a class="footer-link" href="https://www.facebook.com/AtelierEuropeo/"
                                            target="_blank" rel="noopener noreferrer">Facebook</a>
                                    </li>
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="bi bi-instagram"></i>
                                        <a class="footer-link" href="https://www.instagram.com/ateliereuropeo/"
                                            target="_blank" rel="noopener noreferrer">Instagram</a>
                                    </li>
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="bi bi-linkedin"></i>
                                        <a class="footer-link" href="https://www.linkedin.com/company/atelier-europeo/"
                                            target="_blank" rel="noopener noreferrer">LinkedIn</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="footer-divider">

            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 text-center text-md-start">
                <small class="footer-bottom-text">
                    {{ __('master.footer.copyright', ['year' => date('Y')]) }}
                </small>
                <div class="d-flex gap-3">
                    <a href="#" class="footer-link"><u>{{ __('master.footer.privacy_policy') }}</u></a>
                    <a href="#" class="footer-link"><u>{{ __('master.footer.cookie_policy') }}</u></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navbar = document.querySelector('.main-navbar');
            if (!navbar) return;

            const syncNavbarState = () => {
                navbar.classList.toggle('navbar-compact', window.scrollY > 24);
            };

            syncNavbarState();
            window.addEventListener('scroll', syncNavbarState, {
                passive: true
            });
        });
    </script>

    <x-category-info-modals />

    @yield('scripts')

</body>

</html>