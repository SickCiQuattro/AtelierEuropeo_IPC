@extends('layouts.master')

@section('title', 'AE - Accesso Negato')

@section('breadcrumb')
<div class="bg-light py-2">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Accesso Negato</li>
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
                <div class="display-1 text-danger mb-3">
                    <i class="bi bi-shield-x"></i>
                </div>
                <h1 class="h2 text-dark mb-3">Accesso Negato</h1>
                <p class="lead text-muted mb-4">
                    Non hai i permessi necessari per accedere a questa pagina.
                </p>
                
                @if(isset($message))
                    <div class="alert alert-warning" role="alert">
                        {{ $message }}
                    </div>
                @endif
                
                <p class="text-muted mb-4">
                    Se pensi che questo sia un errore, contatta l'amministratore del sito.
                </p>
                
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="{{ route('home') }}" class="btn btn-ae btn-ae-warning btn-lg">
                        Torna alla Home
                    </a>
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-ae btn-ae-outline-primary btn-lg">
                            Accedi
                        </a>
                    @endguest
                </div>
            </div>
            
            <!-- Informazioni utili -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Cosa puoi fare:</h5>
                    <ul class="list-unstyled mb-0">
                        @guest
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Effettua il login se hai un account
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Registrati se non hai ancora un account
                            </li>
                        @endguest
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Verifica di avere i permessi necessari
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <a href="{{ route('contact') }}" class="text-decoration-none">Contatta il supporto</a> se il problema persiste
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection