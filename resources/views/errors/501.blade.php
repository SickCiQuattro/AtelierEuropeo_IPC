@extends('layouts.master')

@section('title', 'AE - Funzionalità Non Implementata')

@section('breadcrumb')
<div class="bg-light py-2">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Funzionalità Non Implementata</li>
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
                <div class="display-1 text-warning mb-3">501</div>
                <h1 class="h2 text-dark mb-3">Funzionalità Non Implementata</h1>
                <p class="lead text-muted mb-4">
                    Questa funzionalità non è ancora disponibile ma arriverà presto!
                </p>
                
                <div class="mb-4">
                    <img src="{{ url('/') }}/img/comingSoon.png" alt="Prossimamente" class="img-fluid rounded shadow-sm" style="max-width: 300px;">
                </div>
                
                <p class="text-muted mb-4">
                    Stiamo lavorando per rendere disponibile questa funzionalità al più presto.
                </p>
                
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="{{ url()->previous() }}" class="btn btn-ae btn-ae-warning btn-lg">
                        Torna Indietro
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-ae btn-ae-outline-primary btn-lg">
                        Vai alla Home
                    </a>
                </div>
            </div>
            
            <!-- Suggerimenti utili -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Nel frattempo puoi:</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Esplorare i nostri progetti disponibili
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Leggere le testimonianze nel portfolio
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Scoprire i programmi di mobilità europea
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <a href="{{ route('contact') }}" class="text-decoration-none">Contattarci</a> per maggiori informazioni
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection