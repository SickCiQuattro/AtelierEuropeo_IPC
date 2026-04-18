@extends('layouts.master')

@section('title', 'AE - Pagina Non Trovata')

@section('breadcrumb')
<div class="bg-light py-2">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active" aria-current="page">Errore 404</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('body')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="text-center mb-4">
                <div class="display-1 text-warning mb-3">404</div>
                <h1 class="h2 text-dark mb-3">Pagina Non Trovata</h1>
                <p class="lead text-muted mb-4">
                    Ops! La pagina che stai cercando non esiste o è stata spostata.
                </p>
                <p class="text-muted mb-4">
                    L'URL potrebbe essere errato o la pagina potrebbe essere stata rimossa dal nostro sito.
                </p>
                
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="{{ route('home') }}" class="btn btn-ae btn-ae-warning btn-lg">
                        Torna alla Home
                    </a>
                    <a href="{{ route('project.index') }}" class="btn btn-ae btn-ae-outline-primary btn-lg">
                        Esplora i Progetti
                    </a>
                </div>
            </div>
            
            <!-- Suggerimenti utili -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Cosa puoi fare:</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Verifica che l'URL sia corretto
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Usa il menu di navigazione per trovare quello che cerchi
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Esplora i nostri progetti di mobilità europea
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <a href="{{ route('contact') }}" class="text-decoration-none">Contattaci</a> se hai bisogno di aiuto
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
