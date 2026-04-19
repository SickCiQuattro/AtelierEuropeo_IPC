@extends('layouts.master')

@section('title', 'AE - ID Non Valido')

@section('breadcrumb')
    <div class="bg-light py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active" aria-current="page">ID Non Valido</li>
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
                    <div class="display-1 text-warning mb-3">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h1 class="h2 text-dark mb-3">ID Non Valido</h1>
                    <p class="lead text-muted mb-4">
                        L'identificativo richiesto non è valido o non esiste.
                    </p>

                    @if(isset($message))
                        <div class="alert alert-warning" role="alert">
                            {{ $message }}
                        </div>
                    @endif

                    <p class="text-muted mb-4">
                        Verifica che l'URL sia corretto o che la risorsa esista ancora.
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
                                Controlla che l'URL sia corretto
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Verifica che la risorsa esista ancora
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Usa il menu per navigare nel sito
                            </li>
                            <li class="mb-0">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <a href="{{ route('contact') }}" class="text-decoration-none">Contattaci</a> se hai bisogno
                                di aiuto
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection