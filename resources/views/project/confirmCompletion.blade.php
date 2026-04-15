@extends('layouts.master')

@section('title', 'AE - Conferma Completamento Progetto')

@section('active_progetti', 'active')

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
                <li class="breadcrumb-item">
                    <a href="{{ route('project.show', $project) }}" class="text-decoration-none">{{ $project->title }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Conferma Completamento</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('body')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <!-- Header -->
            <div class="text-center mb-4">
                <h1 class="h2 text-warning fw-bold mb-3">
                    <i class="bi bi-check-circle me-2"></i>
                    Conferma Completamento Progetto
                </h1>
                <p class="text-muted">
                    Stai per contrassegnare il progetto come completato. Una volta completato, il progetto non potrà più essere modificato.
                </p>
            </div>

            <!-- Card del progetto da completare -->
            <div class="card border-warning mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-folder-check me-2"></i>
                        Progetto da Completare
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        @if($project->image_path)
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                                <img src="{{ $project->image_url }}" 
                                     alt="Immagine progetto" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="max-height: 120px;">
                        </div>
                        @endif
                        <div class="col-md-{{ $project->image_path ? '8' : '12' }}">
                            <h4 class="text-warning fw-bold">{{ $project->title }}</h4>
                            <div class="mb-2">
                                <span class="badge bg-primary me-2">{{ $project->category->tag ?? 'N/A' }}</span>
                                <span class="badge bg-success">{{ $project->association->name ?? 'N/A' }}</span>
                                <span class="badge bg-secondary">{{ ucfirst($project->status) }}</span>
                            </div>
                            <p class="text-muted mb-1">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $project->location }}
                                    </p>
                            <p class="text-muted mb-1">
                                <i class="bi bi-people me-1"></i>
                                {{ $project->requested_people }} persone richieste
                                    </p>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar-event me-1"></i>
                                {{ \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') }} - 
                                        {{ \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') }}
                                    </p>
                        </div>
                    </div>
                    
                    @if($project->sum_description)
                    <div class="mt-3 pt-3 border-top">
                        <p class="mb-0">{{ $project->sum_description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Azioni -->
            <div class="row g-3">
                <!-- Completa -->
                <div class="col-md-6">
                    <div class="card border-warning h-100 d-flex">
                        <div class="card-header bg-light border-warning">
                            <h6 class="text-warning fw-bold mb-0">
                                <i class="bi bi-check-circle me-2"></i>
                                Contrassegna come Completato
                            </h6>
                        </div>
                        <div class="card-body text-center d-flex flex-column">
                            <div class="mb-3">
                                <i class="bi bi-lock text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <p class="text-muted mb-3 flex-grow-1">
                                Il progetto <strong>sarà contrassegnato come completato</strong> e non potrà più essere modificato.
                            </p>
                            <form method="post" action="{{ route('project.complete', ['id' => $project->id]) }}" class="mt-auto">
                                @csrf
                                @method('PUT')
                                <!-- Mantieni tutti i dati del form originale tranne il file -->
                                @foreach($formData as $key => $value)
                                    @if($key === 'status')
                                        <input type="hidden" name="{{ $key }}" value="completed">
                                    @elseif($key !== 'image_path')
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endif
                                @endforeach
                                
                                @if(isset($formData['image_path']))
                                    <!-- Nota per l'immagine -->
                                    <input type="hidden" name="has_new_image" value="1">
                                    <p class="text-muted small mb-3">
                                            <i class="bi bi-info-circle me-1"></i>
                                        Nota: Se hai caricato una nuova immagine, dovrai ricaricarla dopo la conferma.
                                    </p>
                                @endif
                                
                                <button type="submit" class="btn btn-ae btn-ae-warning btn-lg w-100">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Completa Progetto
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Annulla -->
                <div class="col-md-6">
                    <div class="card border-secondary h-100 d-flex">
                        <div class="card-header bg-light border-secondary">
                            <h6 class="text-secondary fw-bold mb-0">
                                <i class="bi bi-arrow-left me-2"></i>
                                Continua a Modificare
                            </h6>
                        </div>
                        <div class="card-body text-center d-flex flex-column">
                            <div class="mb-3">
                                <i class="bi bi-pencil-square text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <p class="text-muted mb-3 flex-grow-1">
                                Il progetto <strong>rimarrà modificabile</strong> e potrai continuare ad aggiornarlo.
                            </p>
                            <div class="mt-auto">
                                <a href="{{ route('project.edit', ['id' => $project->id]) }}" class="btn btn-ae btn-ae-secondary btn-lg w-100">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Torna alla Modifica
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Avviso importante -->
            <div class="alert alert-warning border-0 mt-4">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="alert-heading">Importante!</h6>
                        <ul class="mb-0">
                            <li>Una volta completato, il progetto non potrà più essere modificato</li>
                            <li>Il progetto rimarrà visibile ma in sola lettura</li>
                            <li>Assicurati che tutti i dati siano corretti prima di procedere</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
