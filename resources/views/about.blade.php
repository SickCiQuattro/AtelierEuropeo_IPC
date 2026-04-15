@extends('layouts.master')

@section('title', 'AE - Chi Siamo')

@section('active_chi-siamo', 'active')

@section('body')
    <div class="about-page about-fade-in">
        <section class="py-5 bg-white">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-12 text-center">
                        <h1 class="display-4 fw-bold text-dark mb-3">Chi Siamo</h1>
                        <p class="lead text-body-secondary col-md-8 mx-auto">
                            Connettiamo il territorio bresciano alle opportunità europee dal 2013, promuovendo cittadinanza
                            attiva e partecipazione.
                        </p>
                    </div>
                </div>

                <div class="row align-items-center mb-0">
                    <div class="col-lg-6 mb-4 mb-lg-0 pe-lg-5">
                        <h3 class="fw-bold mb-4">La nostra storia</h3>
                        <p class="text-body-secondary mb-3">
                            Atelier Europeo nasce il <strong>9 maggio 2013</strong>, in occasione della Festa d'Europa, da
                            un'idea condivisa e visionaria di cinque enti di secondo livello del territorio bresciano.
                        </p>
                        <p class="text-body-secondary mb-3">
                            I nostri soci fondatori rappresentano una rete capillare di circa <strong>3.000 realtà
                                bresciane</strong> e almeno <strong>300.000 cittadini</strong>, includendo eccellenze come
                            il
                            Forum Provinciale del Terzo Settore di Brescia, il CSV Brescia e il Patronato San Vincenzo.
                        </p>
                        <p class="text-body-secondary mb-0">
                            Questa data simbolica sottolinea fin dalla nascita il nostro profondo legame con i valori
                            europei
                            e l'impegno nella costruzione di un futuro comune.
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <img src="{{ asset('img/hero-background.jpg') }}" alt="Foto di gruppo Atelier Europeo"
                            class="img-fluid rounded-4" style="object-fit: cover; height: 100%; min-height: 400px;">
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 bg-light">
            <div class="container">
                <div class="row mb-0">
                    <div class="col-12 mb-4">
                        <h3 class="fw-bold">Missione e Valori</h3>
                    </div>
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="card h-100 p-2">
                            <div class="card-body">
                                <div class="mb-4">
                                    <i class="bi bi-heart text-danger fs-1"></i>
                                </div>
                                <h5 class="fw-bold mb-3">Finalità Sociali</h5>
                                <p class="text-body-secondary mb-0">
                                    Siamo un'associazione senza scopo di lucro e apartitica che persegue esclusivamente
                                    finalità di carattere sociale per il bene della comunità.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="card h-100 p-2">
                            <div class="card-body">
                                <div class="mb-4">
                                    <i class="bi bi-people text-success fs-1"></i>
                                </div>
                                <h5 class="fw-bold mb-3">Partecipazione Attiva</h5>
                                <p class="text-body-secondary mb-0">
                                    Promuoviamo la partecipazione delle associazioni bresciane e lombarde alle opportunità e
                                    ai bandi offerti dall'Unione Europea.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 p-2">
                            <div class="card-body">
                                <div class="mb-4">
                                    <i class="bi bi-globe-europe-africa text-primary fs-1"></i>
                                </div>
                                <h5 class="fw-bold mb-3">Cittadinanza Europea</h5>
                                <p class="text-body-secondary mb-0">
                                    Diffondiamo e incentiviamo la partecipazione ai programmi UE per favorire la creazione
                                    di
                                    una cittadinanza europea consapevole.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 bg-white">
            <div class="container">
                <div class="row mb-0">
                    <div class="col-12 mb-4">
                        <h3 class="fw-bold">Il nostro Consiglio Direttivo</h3>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card text-center h-100 board-member-card py-4">
                            <div class="card-body">
                                <div class="board-member-avatar-wrap mx-auto mb-3">
                                    <img src="{{ asset('img/team/giovanni_vezzoni.svg') }}"
                                        alt="Foto profilo di Giovanni Vezzoni" class="board-member-avatar" loading="lazy">
                                </div>
                                <h5 class="fw-bold mb-1">Giovanni Vezzoni</h5>
                                <span class="text-primary small text-uppercase fw-semibold tracking-wide">Presidente</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card text-center h-100 board-member-card py-4">
                            <div class="card-body">
                                <div class="board-member-avatar-wrap mx-auto mb-3">
                                    <img src="{{ asset('img/team/dante_mantovani.svg') }}"
                                        alt="Foto profilo di Dante Mantovani" class="board-member-avatar" loading="lazy">
                                </div>
                                <h5 class="fw-bold mb-1">Dante Mantovani</h5>
                                <span
                                    class="text-body-secondary small text-uppercase fw-semibold tracking-wide">Consigliere</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card text-center h-100 board-member-card py-4">
                            <div class="card-body">
                                <div class="board-member-avatar-wrap mx-auto mb-3">
                                    <img src="{{ asset('img/team/marco_perrucchini.svg') }}"
                                        alt="Foto profilo di Don Marco Perrucchini" class="board-member-avatar"
                                        loading="lazy">
                                </div>
                                <h5 class="fw-bold mb-1">Don Marco Perrucchini</h5>
                                <span
                                    class="text-body-secondary small text-uppercase fw-semibold tracking-wide">Consigliere</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card text-center h-100 board-member-card py-4">
                            <div class="card-body">
                                <div class="board-member-avatar-wrap mx-auto mb-3">
                                    <img src="{{ asset('img/team/renzo_fracassi.svg') }}"
                                        alt="Foto profilo di Renzo Fracassi" class="board-member-avatar" loading="lazy">
                                </div>
                                <h5 class="fw-bold mb-1">Renzo Fracassi</h5>
                                <span
                                    class="text-body-secondary small text-uppercase fw-semibold tracking-wide">Consigliere</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card text-center h-100 board-member-card py-4">
                            <div class="card-body">
                                <div class="board-member-avatar-wrap mx-auto mb-3">
                                    <img src="{{ asset('img/team/francesco_piovani.svg') }}"
                                        alt="Foto profilo di Francesco Piovani" class="board-member-avatar" loading="lazy">
                                </div>
                                <h5 class="fw-bold mb-1">Francesco Piovani</h5>
                                <span class="text-body-secondary small text-uppercase fw-semibold tracking-wide">Revisore
                                    Unico</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card text-center h-100 board-member-card py-4">
                            <div class="card-body">
                                <div class="board-member-avatar-wrap mx-auto mb-3">
                                    <img src="{{ asset('img/team/francesca_fiini.svg') }}"
                                        alt="Foto profilo di Francesca Fiini" class="board-member-avatar" loading="lazy">
                                </div>
                                <h5 class="fw-bold mb-1">Francesca Fiini</h5>
                                <span
                                    class="text-body-secondary small text-uppercase fw-semibold tracking-wide">Segretaria</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 bg-white">
            <div class="container">
                <div class="row align-items-stretch mb-0">
                    <div class="col-lg-6 mb-5 mb-lg-0 pe-lg-4 d-flex flex-column">
                        <h3 class="fw-bold mb-4">Documenti di Trasparenza</h3>
                        <p class="text-body-secondary mb-4">
                            Garantiamo piena visibilità sulle nostre attività mettendo a disposizione di tutti i cittadini i
                            nostri documenti ufficiali.
                        </p>
                        <div class="list-group list-group-flush mt-auto border-top">
                            <a href="{{ asset('documents/atto-costitutivo-statuto.txt') }}" download
                                class="list-group-item list-group-item-action d-flex align-items-center p-3 transparency-link border-0 border-bottom">
                                <i class="bi bi-file-earmark-pdf text-body-tertiary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1 fw-semibold text-dark">Atto Costitutivo e Statuto</h6>
                                    <small class="text-body-secondary">Fondazione dell'associazione</small>
                                </div>
                                <i class="bi bi-cloud-arrow-down ms-auto text-primary fs-5" aria-hidden="true"></i>
                            </a>
                            <a href="{{ asset('documents/bilancio-missione-2025.txt') }}" download
                                class="list-group-item list-group-item-action d-flex align-items-center p-3 transparency-link border-0 border-bottom">
                                <i class="bi bi-graph-up text-body-tertiary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1 fw-semibold text-dark">Bilancio di Missione 2025</h6>
                                    <small class="text-body-secondary">Archivio rendiconti delle attività</small>
                                </div>
                                <i class="bi bi-cloud-arrow-down ms-auto text-primary fs-5" aria-hidden="true"></i>
                            </a>
                            <a href="{{ asset('documents/privacy-cookie-policy.txt') }}" download
                                class="list-group-item list-group-item-action d-flex align-items-center p-3 transparency-link border-0 border-bottom">
                                <i class="bi bi-shield-check text-body-tertiary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1 fw-semibold text-dark">Privacy & Cookie Policy</h6>
                                    <small class="text-body-secondary">Protezione dei dati personali</small>
                                </div>
                                <i class="bi bi-cloud-arrow-down ms-auto text-primary fs-5" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div
                            class="bg-europe p-4 p-md-5 rounded-4 h-100 d-flex flex-column justify-content-center text-center shadow-sm">
                            <h3 class="fw-bold text-white mb-3">Costruiamo insieme il futuro europeo</h3>
                            <p class="text-white-50 mb-4">
                                L'adesione ad Atelier Europeo è aperta a tutti gli <strong>enti pubblici e privati</strong>.
                                Scopri i nostri progetti o contattaci per diventare parte attiva della nostra rete.
                            </p>
                            <div class="d-flex gap-3 justify-content-center flex-column flex-sm-row mt-4">
                                <a href="{{ route('project.index') }}"
                                    class="btn px-4 py-2 btn-ae btn-ae-pill btn-ae-light">
                                    Scopri i Progetti
                                </a>
                                <a href="{{ route('contact') }}"
                                    class="btn px-4 py-2 btn-ae btn-ae-pill btn-ae-outline-light">
                                    Contattaci
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection