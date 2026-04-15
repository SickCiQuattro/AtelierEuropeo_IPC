@extends('layouts.master')

@section('title', 'AE - Progetto Completato')

@section('breadcrumb')
<div class="bg-light py-2">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('project.index') }}" class="text-decoration-none">Progetti Disponibili</a>
                </li>
                @if(isset($project))
                    <li class="breadcrumb-item">
                        <a href="{{ route('project.show', $project) }}" class="text-decoration-none">{{ $project->title }}</a>
                    </li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">Progetto Completato</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('body')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body text-center p-5">
                    <!-- Icona -->
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <!-- Titolo -->
                    <h1 class="h2 fw-bold text-dark mb-3">Progetto Completato</h1>
                    
                    <!-- Messaggio -->
                    <p class="text-muted mb-4 fs-5">
                        {{ $message ?? 'Questo progetto è stato completato e non può più essere modificato.' }}
                    </p>
                    
                    <!-- Informazioni sul progetto -->
                    @if(isset($project))
                    <div class="alert alert-info border-0 rounded-3 mb-4">
                        <h5 class="alert-heading mb-2">
                            <i class="bi bi-info-circle me-2"></i>
                                    {{ $project->title }}
                                </h5>
                                        <p class="mb-1">
                                            <strong>Stato:</strong> 
                            <span class="badge bg-success">Completato</span>
                                        </p>
                                    @if($project->end_date)
                                        <p class="mb-0">
                                            <strong>Data di completamento:</strong> 
                                                {{ \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') }}
                                        </p>
                                    @endif
                    </div>
                    @endif
                    
                    <!-- Azioni -->
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        @if(isset($project))
                        <a href="{{ route('project.show', $project->id) }}" 
                           class="btn btn-ae btn-ae-primary btn-lg rounded-pill px-4">
                            <i class="bi bi-eye me-2"></i>
                            Visualizza Progetto
                        </a>
                        @endif
                        
                        <a href="{{ route('project.index') }}" 
                           class="btn btn-ae btn-ae-outline-secondary btn-lg rounded-pill px-4">
                            <i class="bi bi-arrow-left me-2"></i>
                            Torna ai Progetti
                        </a>
                    </div>
                    
                    <!-- Nota informativa -->
                    <div class="mt-4 pt-3 border-top">
                        <small class="text-muted">
                            <i class="bi bi-lightbulb me-1"></i>
                            I progetti completati rimangono consultabili ma non possono essere modificati per garantire l'integrità dei dati storici.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
