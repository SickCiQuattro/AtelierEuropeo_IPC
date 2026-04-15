@extends('layouts.master')

@section('title', 'AE - Errore Interno del Server')

@section('breadcrumb')
<div class="bg-light py-2">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Errore 500</li>
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
                <div class="display-1 text-danger mb-3">500</div>
                <h1 class="h2 text-dark mb-3">Errore Interno del Server</h1>
                <p class="lead text-muted mb-4">
                    Si è verificato un errore interno del server. Ci scusiamo per l'inconveniente.
                </p>
                <p class="text-muted mb-4">
                    Il nostro team è stato automaticamente notificato e sta lavorando per risolvere il problema.
                </p>
                
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="{{ route('home') }}" class="btn btn-ae btn-ae-warning btn-lg">
                        Torna alla Home
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-ae btn-ae-outline-primary btn-lg">
                        Segnala il Problema
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
                            Riprova tra qualche minuto
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Torna alla homepage e riprova la navigazione
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Se il problema persiste, contattaci
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Controlla i nostri social per aggiornamenti
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection