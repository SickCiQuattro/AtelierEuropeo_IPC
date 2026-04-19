@extends('layouts.master')

@section('title', 'AE - Corpo Europeo di Solidarietà')

@section('breadcrumb')
    <div class="bg-light py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Viaggiare all'Estero</li>
                    <li class="breadcrumb-item active" aria-current="page">Corpo Europeo di Solidarietà</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('active_viaggiare', 'active')

@section('body')
    <div class="container py-4">
        <!-- Header della pagina -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="section-title fw-bold text-center">Corpo Europeo di Solidarietà</h1>
                <h1 class="section-subtitle text-center">Un'opportunità unica per i giovani di contribuire alla solidarietà
                    europea
                    attraverso progetti di volontariato, tirocinio e lavoro in tutta Europa.</h1>
            </div>
        </div>

        <!-- Sezione Cos'è -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Cos'è il Corpo Europeo di Solidarietà</h3>
                <p class="lead mb-3">
                    Il Corpo Europeo di Solidarietà (ESC) è un programma che offre a giovani di età compresa
                    tra i 18 e i 30 anni l'opportunità di partecipare a progetti di <strong>volontariato,
                        tirocinio e lavoro</strong> in Europa, per periodi che vanno da <strong>2 settimane
                        a 1 anno</strong>. Atelier Europeo è un'organizzazione di invio accreditata che
                    supporta i giovani in questo percorso di crescita personale e professionale.
                </p>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success">
                        <h5 class="mb-0 text-white">
                            <i class="bi bi-gear-fill me-2 text-white"></i>Settori di intervento
                        </h5>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-2">
                            <div class="col-lg-4 col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-people-fill me-2 fs-5 text-success"></i>
                                    <span class="small">Assistenza a richiedenti asilo</span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-heart-fill text-success me-2 fs-5"></i>
                                    <span class="small">Settore sociale</span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-palette-fill text-success me-2 fs-5"></i>
                                    <span class="small">Arte e cultura</span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-tree-fill text-success me-2 fs-5"></i>
                                    <span class="small">Ambiente</span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-book-fill text-success me-2 fs-5"></i>
                                    <span class="small">Educazione</span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-heart-pulse-fill text-success me-2 fs-5"></i>
                                    <span class="small">Salute</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sezione Chi può partecipare -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Chi può partecipare</h3>
                <div class="list-group">
                    <div class="list-group-item d-flex align-items-center py-2">
                        <i class="bi bi-calendar-event text-success me-3 fs-5"></i>
                        <div>
                            <h6 class="mb-1">Età</h6>
                            <small class="text-muted">Iscrizione a partire dai 17 anni, ma è necessario averne 18 per
                                iniziare. Età massima 30 anni.</small>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center py-2">
                        <i class="bi bi-gear text-success me-3 fs-5"></i>
                        <div>
                            <h6 class="mb-1">Competenze specifiche</h6>
                            <small class="text-muted">Non sono richieste competenze particolari, salvo eccezioni specifiche
                                del progetto (es. patente, genere specifico).</small>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center py-2">
                        <i class="bi bi-heart text-success me-3 fs-5"></i>
                        <div>
                            <h6 class="mb-1">Motivazione</h6>
                            <small class="text-muted">Forte desiderio di contribuire alla solidarietà europea e crescere
                                personalmente.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bg-success bg-opacity-10 p-3 rounded-4 h-100 d-flex align-items-center">
                    <div class="text-center w-100">
                        <i class="bi bi-heart-fill text-success" style="font-size: 3rem;"></i>
                        <h5 class="text-success mt-2 mb-2">Solidarietà</h5>
                        <p class="text-muted mb-0 small">
                            Un'esperienza che arricchisce te e le comunità che aiuti
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sezione Cosa copre il programma -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Cosa copre il programma</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="list-group">
                            <div class="list-group-item d-flex align-items-center py-2">
                                <i class="bi bi-airplane text-success me-3 fs-5"></i>
                                <div>
                                    <h6 class="mb-1">Viaggio</h6>
                                    <small class="text-muted">Spese di viaggio coperte con massimale in base alla
                                        distanza</small>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center py-2">
                                <i class="bi bi-house text-success me-3 fs-5"></i>
                                <div>
                                    <h6 class="mb-1">Vitto e alloggio</h6>
                                    <small class="text-muted">Sistemazione e pasti completamente coperti</small>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center py-2">
                                <i class="bi bi-shield-check text-success me-3 fs-5"></i>
                                <div>
                                    <h6 class="mb-1">Assicurazione</h6>
                                    <small class="text-muted">Copertura assicurativa completa per tutta la durata</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="list-group">
                            <div class="list-group-item d-flex align-items-center py-2">
                                <i class="bi bi-currency-euro text-success me-3 fs-5"></i>
                                <div>
                                    <h6 class="mb-1">Pocket Money</h6>
                                    <small class="text-muted">Supporto economico mensile variabile in base al paese di
                                        destinazione</small>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center py-2">
                                <i class="bi bi-calendar-check text-success me-3 fs-5"></i>
                                <div>
                                    <h6 class="mb-1">Attività</h6>
                                    <small class="text-muted">Tutte le attività del progetto e la formazione incluse</small>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center py-2">
                                <i class="bi bi-translate text-success me-3 fs-5"></i>
                                <div>
                                    <h6 class="mb-1">Supporto linguistico</h6>
                                    <small class="text-muted">Accesso gratuito alla piattaforma OLS (Online Linguistic
                                        Support)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sezione Come candidarsi -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Come candidarsi</h3>
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold d-flex align-items-center">
                                <i class="bi bi-info-circle text-success me-2"></i>
                                Informarsi
                            </div>
                            <small class="text-muted">Contattare Atelier Europeo per ricevere informazioni dettagliate sui
                                progetti disponibili e sui
                                requisiti specifici.</small>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold d-flex align-items-center">
                                <i class="bi bi-file-earmark-text text-success me-2"></i>
                                Preparare il CV
                            </div>
                            <small class="text-muted">Creare un CV in formato Europass in lingua inglese, evidenziando le
                                proprie competenze ed
                                esperienze.</small>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold d-flex align-items-center">
                                <i class="bi bi-person-plus text-success me-2"></i>
                                Iscriversi
                            </div>
                            <small class="text-muted">Registrarsi sul portale ufficiale del Corpo Europeo di Solidarietà per
                                creare il proprio
                                profilo.</small>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold d-flex align-items-center">
                                <i class="bi bi-send text-success me-2"></i>
                                Candidarsi
                            </div>
                            <small class="text-muted">Inviare la propria candidatura ai progetti di interesse tramite il
                                supporto di Atelier Europeo.</small>
                        </div>
                    </li>
                </ol>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row">
            <div class="col-12">
                <div class="bg-success text-white p-4 rounded-4 text-center">
                    <h4 class="fw-bold mb-2">Pronto a fare la differenza?</h4>
                    <p class="mb-3">
                        Unisciti al Corpo Europeo di Solidarietà e vivi un'esperienza che arricchirà
                        la tua vita personale e professionale, contribuendo al contempo a costruire
                        un'Europa più solidale e inclusiva.
                    </p>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="{{ route('contact') }}" class="btn btn-ae btn-ae-light">
                            <i class="bi bi-envelope me-2"></i>Contattaci per informazioni
                        </a>
                        <a href="{{ route('about') }}" class="btn btn-ae btn-ae-outline-light">
                            <i class="bi bi-info-circle me-2"></i>Scopri Atelier Europeo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection