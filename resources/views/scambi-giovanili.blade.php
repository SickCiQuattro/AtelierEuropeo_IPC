@extends('layouts.master')

@section('title', 'AE - Scambi Giovanili')

@section('breadcrumb')
<div class="bg-light py-2">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
                </li>
                <li class="breadcrumb-item active">Viaggiare all'Estero</li>
                <li class="breadcrumb-item active" aria-current="page">Scambi Giovanili</li>
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
            <h1 class="section-title fw-bold text-center">Scambi Giovanili</h1>
            <h1 class="section-subtitle text-center">Esperienze di mobilità internazionale che favoriscono l'incontro tra culture diverse 
                e lo sviluppo di competenze interculturali e linguistiche.</h1>
        </div>
    </div>

    <!-- Sezione Cosa sono -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Cosa sono gli Scambi Giovanili</h3>
            <p class="lead mb-3">
                I <strong>Youth Exchanges</strong> sono progetti di mobilità internazionale co-finanziati 
                dal programma <strong>Erasmus+</strong>, rivolti a giovani tra i 13 e i 30 anni. 
                Durante questi incontri, gruppi di giovani provenienti da diversi paesi europei 
                si riuniscono per condividere esperienze culturali, apprendere reciprocamente 
                e migliorare le proprie competenze linguistiche, principalmente in inglese.
            </p>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-lightning-fill me-2 text-white"></i>In breve
                    </h5>
                </div>
                <div class="card-body py-3">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6 text-center">
                            <i class="bi bi-calendar-event text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">Durata</h6>
                            <p class="text-muted mb-0 small">7-10 giorni</p>
                        </div>
                        <div class="col-lg-4 col-md-6 text-center">
                            <i class="bi bi-person-hearts text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">Età</h6>
                            <p class="text-muted mb-0 small">13-30 anni</p>
                        </div>
                        <div class="col-lg-4 col-md-12 text-center">
                            <i class="bi bi-globe text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">Focus</h6>
                            <p class="text-muted mb-0 small">Cultura e lingue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sezione Requisiti -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Requisiti per partecipare</h3>
            <div class="list-group">
                <div class="list-group-item d-flex align-items-center py-2">
                    <i class="bi bi-calendar-check text-primary me-3 fs-5"></i>
                    <div>
                        <h6 class="mb-1">Età</h6>
                        <small class="text-muted">Avere tra i 13 e i 30 anni (o più di 30 per il ruolo di team leader)</small>
                    </div>
                </div>
                <div class="list-group-item d-flex align-items-center py-2">
                    <i class="bi bi-geo-alt text-primary me-3 fs-5"></i>
                    <div>
                        <h6 class="mb-1">Residenza</h6>
                        <small class="text-muted">Essere residenti in Italia</small>
                    </div>
                </div>
                <div class="list-group-item d-flex align-items-center py-2">
                    <i class="bi bi-heart-fill text-primary me-3 fs-5"></i>
                    <div>
                        <h6 class="mb-1">Motivazione</h6>
                        <small class="text-muted">Forte motivazione e interesse per il confronto interculturale</small>
                    </div>
                </div>
                <div class="list-group-item d-flex align-items-center py-2">
                    <i class="bi bi-chat-dots text-primary me-3 fs-5"></i>
                    <div>
                        <h6 class="mb-1">Lingua</h6>
                        <small class="text-muted">Disponibilità a comunicare in inglese (livello base sufficiente)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="bg-primary bg-opacity-10 p-3 rounded-4 h-100 d-flex align-items-center">
                <div class="text-center w-100">
                    <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                    <h5 class="text-primary mt-2 mb-2">Inclusività</h5>
                    <p class="text-muted mb-0 small">
                        Gli scambi giovanili sono aperti a tutti i giovani, 
                        indipendentemente dal background sociale, economico 
                        o educativo. L'importante è la voglia di imparare!
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sezione Costi -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Costi</h3>
            
            <div class="alert alert-primary border-0 shadow-sm mb-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-gift-fill me-3 fs-4"></i>
                    <div>
                        <h5 class="alert-heading mb-1">
                            <strong>Partecipazione completamente gratuita!</strong>
                        </h5>
                        <p class="mb-0 small">
                            Il programma Erasmus+ copre interamente tutti i costi, permettendo 
                            a ogni giovane di vivere questa esperienza senza oneri finanziari.
                        </p>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-2">
                        <i class="bi bi-check-circle-fill me-2"></i>Cosa è coperto
                    </h6>
                    <ul class="list-unstyled">
                        <li class="mb-1 small">
                            <i class="bi bi-airplane text-primary me-2"></i>
                            Viaggio (con massimale basato sulla distanza)
                        </li>
                        <li class="mb-1 small">
                            <i class="bi bi-house text-primary me-2"></i>
                            Vitto e alloggio per tutta la durata
                        </li>
                        <li class="mb-1 small">
                            <i class="bi bi-calendar-event text-primary me-2"></i>
                            Tutte le attività previste dal programma
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-2">
                        <i class="bi bi-plus-circle-fill me-2"></i>Servizi inclusi
                    </h6>
                    <ul class="list-unstyled">
                        <li class="mb-1 small">
                            <i class="bi bi-shield-check text-primary me-2"></i>
                            Assicurazione sanitaria completa
                        </li>
                        <li class="mb-1 small">
                            <i class="bi bi-book text-primary me-2"></i>
                            Materiali didattici e risorse
                        </li>
                        <li class="mb-1 small">
                            <i class="bi bi-award text-primary me-2"></i>
                            Certificato di partecipazione YouthPass
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Sezione Come partecipare -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Come partecipare</h3>
            <ol class="list-group list-group-numbered">
                <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold d-flex align-items-center">
                            <i class="bi bi-search text-primary me-2"></i>
                            Informarsi
                        </div>
                        <small class="text-muted">Seguire i canali di comunicazione di Atelier Europeo (sito web, social media, newsletter) 
                        per rimanere aggiornati sui progetti disponibili.</small>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold d-flex align-items-center">
                            <i class="bi bi-telephone text-primary me-2"></i>
                            Contattare
                        </div>
                        <small class="text-muted">Manifestare il proprio interesse contattando Atelier Europeo con anticipo, 
                        specificando le proprie motivazioni e disponibilità.</small>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold d-flex align-items-center">
                            <i class="bi bi-file-earmark-person text-primary me-2"></i>
                            Preparare il CV
                        </div>
                        <small class="text-muted">Redigere un CV in formato Europass in lingua inglese, evidenziando 
                        esperienze, competenze e motivazioni per la partecipazione.</small>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold d-flex align-items-center">
                            <i class="bi bi-people text-primary me-2"></i>
                            Supporto
                        </div>
                        <small class="text-muted">Lo staff di Atelier Europeo assisterà i partecipanti selezionati 
                        nella preparazione e durante tutto il percorso dello scambio.</small>
                    </div>
                </li>
            </ol>
        </div>
    </div>

    <!-- Benefici dell'esperienza -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Cosa otterrai da questa esperienza</h3>
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body p-3">
                            <i class="bi bi-chat-square-text text-primary" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-2 mb-2">Competenze Linguistiche</h6>
                            <p class="text-muted small mb-0">
                                Migliora il tuo inglese e scopri altre lingue europee 
                                attraverso l'immersione culturale.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body p-3">
                            <i class="bi bi-globe-americas text-primary" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-2 mb-2">Consapevolezza Culturale</h6>
                            <p class="text-muted small mb-0">
                                Sviluppa una mentalità aperta e comprensione 
                                delle diverse culture europee.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body p-3">
                            <i class="bi bi-person-hearts text-primary" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-2 mb-2">Network Internazionale</h6>
                            <p class="text-muted small mb-0">
                                Crea amicizie durature con giovani da tutta Europa 
                                e costruisci il tuo network internazionale.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row">
        <div class="col-12">
            <div class="bg-primary text-white p-4 rounded-4 text-center">
                <h4 class="fw-bold mb-2">Pronto per un'avventura interculturale?</h4>
                <p class="mb-3">
                    Gli scambi giovanili sono un'opportunità unica di crescita personale e culturale. 
                    Scopri il mondo, fai nuove amicizie e arricchisci il tuo bagaglio di esperienze 
                    senza alcun costo!
                </p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="{{ route('contact') }}" class="btn btn-ae btn-ae-light">
                        <i class="bi bi-envelope me-2"></i>Manifestiamo il tuo interesse
                    </a>
                    <a href="{{ route('corpo-europeo') }}" class="btn btn-ae btn-ae-outline-light">
                        <i class="bi bi-arrow-right me-2"></i>Scopri altre opportunità
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
