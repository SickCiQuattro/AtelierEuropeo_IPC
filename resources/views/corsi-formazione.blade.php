@extends('layouts.master')

@section('title', 'AE - Corsi di Formazione')

@section('breadcrumb')
<div class="bg-light py-2">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
                </li>
                <li class="breadcrumb-item active">Viaggiare all'Estero</li>
                <li class="breadcrumb-item active" aria-current="page">Corsi di Formazione</li>
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
            <h1 class="section-title fw-bold text-center">Corsi di Formazione</h1>
            <h1 class="section-subtitle text-center">Opportunità di sviluppo professionale per Youth Workers e operatori giovanili, 
                con focus su metodologie innovative e competenze specialistiche nel settore giovanile.</h1>
        </div>
    </div>

    <!-- Sezione Cosa sono -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Cosa sono i Corsi di Formazione</h3>
            <p class="lead mb-3">
                I <strong>Training Courses</strong> sono percorsi formativi specializzati co-finanziati 
                dal programma <strong>Erasmus+</strong>, progettati specificamente per <strong>Youth Workers</strong> 
                e operatori del settore giovanile. Questi corsi intensivi combinano formazione teorica 
                e pratica per sviluppare competenze avanzate nella gestione di progetti giovanili, 
                metodologie di educazione non formale e leadership nel terzo settore.
            </p>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0 text-dark">
                        <i class="bi bi-info-circle-fill me-2 text-dark"></i>Caratteristiche principali
                    </h5>
                </div>
                <div class="card-body py-3">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6 text-center">
                            <i class="bi bi-calendar-week text-warning" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">Durata</h6>
                            <p class="text-muted mb-0 small">3-10 giorni</p>
                        </div>
                        <div class="col-lg-3 col-md-6 text-center">
                            <i class="bi bi-person-workspace text-warning" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">Target</h6>
                            <p class="text-muted mb-0 small">Youth Workers</p>
                        </div>
                        <div class="col-lg-3 col-md-6 text-center">
                            <i class="bi bi-tools text-warning" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">Focus</h6>
                            <p class="text-muted mb-0 small">Competenze pratiche</p>
                        </div>
                        <div class="col-lg-3 col-md-6 text-center">
                            <i class="bi bi-award text-warning" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">Certificazione</h6>
                            <p class="text-muted mb-0 small">YouthPass</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sezione A chi si rivolgono -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <h3 class="fw-bold mb-3 fs-2 fs-lg-1">A chi si rivolgono</h3>
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="card border-start border-5 border-warning h-100">
                        <div class="card-body py-2">
                            <h6 class="fw-bold text-warning mb-1">
                                <i class="bi bi-person-badge me-2"></i>Youth Workers
                            </h6>
                            <p class="text-muted mb-0 small">
                                Operatori che lavorano direttamente con i giovani in contesti educativi, 
                                associativi o del volontariato
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-start border-5 border-warning h-100">
                        <div class="card-body py-2">
                            <h6 class="fw-bold text-warning mb-1">
                                <i class="bi bi-building me-2"></i>Operatori del Terzo Settore
                            </h6>
                            <p class="text-muted mb-0 small">
                                Professionisti di ONG, associazioni, cooperative sociali 
                                e organizzazioni giovanili
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-start border-5 border-warning h-100">
                        <div class="card-body py-2">
                            <h6 class="fw-bold text-warning mb-1">
                                <i class="bi bi-person-raised-hand me-2"></i>Volontari Esperti
                            </h6>
                            <p class="text-muted mb-0 small">
                                Volontari con esperienza significativa nell'ambito 
                                dei progetti giovanili e della mobilità
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-start border-5 border-warning h-100">
                        <div class="card-body py-2">
                            <h6 class="fw-bold text-warning mb-1">
                                <i class="bi bi-mortarboard me-2"></i>Formatori ed Educatori
                            </h6>
                            <p class="text-muted mb-0 small">
                                Professionisti dell'educazione non formale 
                                e della formazione per adulti
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="bg-warning bg-opacity-10 p-3 rounded-4 h-100 d-flex align-items-center">
                <div class="text-center w-100">
                    <i class="bi bi-lightbulb-fill text-warning" style="font-size: 3rem;"></i>
                    <h5 class="text-warning mt-2 mb-2">Sviluppo Professionale</h5>
                    <p class="text-muted mb-0 small">
                        I corsi di formazione rappresentano un investimento 
                        nel tuo futuro professionale nel settore giovanile
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Aree tematiche -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Aree tematiche principali</h3>
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0 text-dark">
                                <i class="bi bi-gear-fill me-2 text-dark"></i>Metodologie e Strumenti
                            </h6>
                        </div>
                        <div class="card-body py-2">
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-1">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Educazione non formale e informale
                                </li>
                                <li class="mb-1">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Gestione di gruppi e dinamiche di team
                                </li>
                                <li class="mb-1">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Tecniche di facilitazione e mediazione
                                </li>
                                <li class="mb-0">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Strumenti digitali per l'educazione
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0 text-dark">
                                <i class="bi bi-heart-fill me-2 text-dark"></i>Inclusione e Diversity
                            </h6>
                        </div>
                        <div class="card-body py-2">
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-1">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Lavoro con giovani con minori opportunità
                                </li>
                                <li class="mb-1">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Interculturalità e gestione delle diversità
                                </li>
                                <li class="mb-1">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Prevenzione della discriminazione
                                </li>
                                <li class="mb-0">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Promozione dei diritti umani
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0 text-dark">
                                <i class="bi bi-briefcase-fill me-2 text-dark"></i>Project Management
                            </h6>
                        </div>
                        <div class="card-body py-2">
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-1">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Progettazione europea (Erasmus+, ESC)
                                </li>
                                <li class="mb-1">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Gestione amministrativa e finanziaria
                                </li>
                                <li class="mb-1">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Monitoraggio e valutazione progetti
                                </li>
                                <li class="mb-0">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Fundraising e sostenibilità
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0 text-dark">
                                <i class="bi bi-graph-up-arrow me-2 text-dark"></i>Competenze Trasversali
                            </h6>
                        </div>
                        <div class="card-body py-2">
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-1">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Leadership e gestione del cambiamento
                                </li>
                                <li class="mb-1">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Comunicazione efficace e public speaking
                                </li>
                                <li class="mb-1">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Networking e partnership strategiche
                                </li>
                                <li class="mb-0">
                                    <i class="bi bi-check-circle text-warning me-2"></i>
                                    Innovazione sociale e creatività
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sezione Costi -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Investimento e coperture</h3>
            
            <div class="alert alert-warning border-0 shadow-sm mb-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-piggy-bank-fill me-3 fs-4"></i>
                    <div>
                        <h5 class="alert-heading mb-1">
                            <strong>Costi minimali per massimo beneficio!</strong>
                        </h5>
                        <p class="mb-0 small">
                            Erasmus+ copre la maggior parte dei costi, rendendo l'investimento 
                            nella tua formazione professionale molto accessibile.
                        </p>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="fw-bold text-warning mb-2">
                        <i class="bi bi-check-circle-fill me-2"></i>Completamente coperto
                    </h6>
                    <ul class="list-unstyled">
                        <li class="mb-1 small">
                            <i class="bi bi-house-door text-warning me-2"></i>
                            Vitto e alloggio per tutta la durata
                        </li>
                        <li class="mb-1 small">
                            <i class="bi bi-book-half text-warning me-2"></i>
                            Materiali formativi e risorse didattiche
                        </li>
                        <li class="mb-1 small">
                            <i class="bi bi-shield-plus text-warning me-2"></i>
                            Assicurazione sanitaria completa
                        </li>
                        <li class="mb-1 small">
                            <i class="bi bi-award text-warning me-2"></i>
                            Certificazione YouthPass finale
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-warning mb-2">
                        <i class="bi bi-airplane-fill me-2"></i>Viaggio
                    </h6>
                    <div class="bg-warning bg-opacity-10 p-2 rounded">
                        <p class="mb-1 small">
                            <strong>Rimborso basato sulla distanza:</strong>
                        </p>
                        <ul class="list-unstyled small">
                            <li>• 100-499 km: fino a €180</li>
                            <li>• 500-1999 km: fino a €275</li>
                            <li>• 2000-2999 km: fino a €360</li>
                            <li>• 3000+ km: fino a €530</li>
                        </ul>
                        <small class="text-muted">
                            Percentuale coperta: 70-100% in base alla destinazione
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sezione Come partecipare -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Processo di candidatura</h3>
            <ol class="list-group list-group-numbered">
                <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold d-flex align-items-center">
                            <i class="bi bi-search text-warning me-2"></i>
                            Identificazione delle opportunità
                        </div>
                        <small class="text-muted">Monitora regolarmente il sito di Atelier Europeo e i social media per rimanere 
                        aggiornato sui Training Courses disponibili per Youth Workers.</small>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold d-flex align-items-center">
                            <i class="bi bi-file-text text-warning me-2"></i>
                            Preparazione della candidatura
                        </div>
                        <small class="text-muted">CV in formato Europass (inglese), lettera motivazionale specifica per il corso, 
                        portfolio delle esperienze nel settore giovanile (se disponibile).</small>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold d-flex align-items-center">
                            <i class="bi bi-send text-warning me-2"></i>
                            Invio della candidatura
                        </div>
                        <small class="text-muted">Invia la candidatura completa rispettando scadenze e requisiti specifici 
                        indicati per ogni Training Course.</small>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold d-flex align-items-center">
                            <i class="bi bi-person-video3 text-warning me-2"></i>
                            Selezione e preparazione
                        </div>
                        <small class="text-muted">Possibile colloquio di selezione, briefing pre-partenza e supporto 
                        nella preparazione logistica e contenutistica.</small>
                    </div>
                </li>
            </ol>
        </div>
    </div>

    <!-- Benefici professionali -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold mb-3 fs-2 fs-lg-1">Impatto sulla tua carriera</h3>
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body p-3">
                            <i class="bi bi-trophy-fill text-warning" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-2 mb-2">Riconoscimento Professionale</h6>
                            <p class="text-muted small mb-0">
                                Certificazioni riconosciute a livello europeo 
                                che arricchiscono il tuo profilo professionale.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body p-3">
                            <i class="bi bi-people-fill text-warning" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-2 mb-2">Network Professionale</h6>
                            <p class="text-muted small mb-0">
                                Connessioni con Youth Workers e organizzazioni 
                                da tutta Europa per collaborazioni future.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body p-3">
                            <i class="bi bi-lightbulb-fill text-warning" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-2 mb-2">Metodologie Innovative</h6>
                            <p class="text-muted small mb-0">
                                Strumenti pratici e approcci all'avanguardia 
                                da applicare immediatamente nel tuo lavoro.
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
            <div class="bg-warning text-dark p-4 rounded-4 text-center">
                <h4 class="fw-bold mb-2">Investi nel tuo futuro professionale</h4>
                <p class="mb-3">
                    I Training Courses sono l'opportunità ideale per Youth Workers che vogliono 
                    eccellere nel settore giovanile, acquisendo competenze specialistiche e 
                    costruendo una carriera di impatto sociale.
                </p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="{{ route('contact') }}" class="btn btn-ae btn-ae-dark">
                        <i class="bi bi-briefcase me-2"></i>Candidati ora
                    </a>
                    <a href="{{ route('about') }}" class="btn btn-ae btn-ae-outline-dark">
                        <i class="bi bi-info-circle me-2"></i>Scopri Atelier Europeo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
