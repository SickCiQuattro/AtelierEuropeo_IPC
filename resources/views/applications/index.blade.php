@extends('layouts.master')

@section('title', 'AE - Le mie candidature')

@section('breadcrumb')
    <div class="bg-light py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active" aria-current="page">Le Mie Candidature</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('body')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="mb-4">
                    <h1 class="h2 fw-bold text-dark mb-2">
                        <i class="bi bi-file-earmark-text me-2"></i>Le mie candidature
                    </h1>
                    <p class="text-muted">
                        Qui puoi vedere tutte le tue candidature inviate e il loro stato
                    </p>
                </div>

                @if($applications->count() > 0)
                    <div class="row g-4">
                        @foreach($applications as $application)
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <!-- Header con titolo progetto e status -->
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title fw-semibold mb-1">
                                                    <i class="bi bi-briefcase me-2"></i>{{ $application->project->title }}
                                                </h5>
                                                <p class="card-text text-muted small">
                                                    <i class="bi bi-calendar3 me-1"></i>Candidatura inviata il
                                                    {{ $application->created_at->format('d/m/Y \a\l\l\e H:i') }}
                                                </p>
                                            </div>

                                            <span class="badge fs-6
                                                        @if($application->status === 'pending') 
                                                            bg-warning
                                                        @elseif($application->status === 'approved') 
                                                            bg-success
                                                        @elseif($application->status === 'rejected') 
                                                            bg-danger
                                                        @endif">
                                                @if($application->status === 'pending')
                                                    <i class="bi bi-clock me-1"></i>In attesa
                                                @elseif($application->status === 'approved')
                                                    <i class="bi bi-check-circle me-1"></i>Approvata
                                                @elseif($application->status === 'rejected')
                                                    <i class="bi bi-x-circle me-1"></i>Rifiutata
                                                @endif
                                            </span>
                                        </div>

                                        <!-- Descrizione progetto -->
                                        <p class="card-text mb-3">
                                            {{ Str::limit($application->project->description, 200) }}
                                        </p>

                                        <!-- Informazioni candidatura -->
                                        <div class="bg-light rounded p-3 mb-3">
                                            <h6 class="fw-medium mb-2">
                                                <i class="bi bi-info-circle me-1"></i>Dettagli candidatura:
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <span class="fw-medium">
                                                        <i class="bi bi-telephone me-1"></i>Telefono:
                                                    </span>
                                                    <span class="ms-2">{{ $application->phone }}</span>
                                                </div>
                                                @if($application->document_path)
                                                    <div class="col-md-6">
                                                        <span class="fw-medium">
                                                            <i class="bi bi-file-earmark-pdf me-1"></i>Documento:
                                                        </span>
                                                        <a href="{{ asset('storage/' . $application->document_path) }}" target="_blank"
                                                            class="text-primary text-decoration-none ms-2">
                                                            {{ $application->document_name }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Azioni -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="{{ route('project.show', $application->project->id) }}"
                                                class="btn btn-ae btn-ae-outline-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i>Visualizza progetto
                                            </a>

                                            @if($application->status === 'pending')
                                                <span class="text-muted small">
                                                    <i class="bi bi-hourglass-split me-1"></i>Ti contatteremo presto per comunicarti
                                                    l'esito
                                                </span>
                                            @elseif($application->status === 'approved')
                                                <span class="text-success small fw-medium">
                                                    <i class="bi bi-trophy me-1"></i>Congratulazioni! Sarai contattato presto
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginazione -->
                    <div class="mt-4">
                        {{ $applications->links() }}
                    </div>
                @else
                    <!-- Stato vuoto -->
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                        </div>

                        <h3 class="h4 fw-medium mb-2">
                            Nessuna candidatura trovata
                        </h3>
                        <p class="text-muted mb-4">
                            Non hai ancora inviato nessuna candidatura. Esplora i progetti disponibili e candidati!
                        </p>

                        <a href="{{ route('project.index') }}" class="btn btn-ae btn-ae-primary">
                            <i class="bi bi-search me-1"></i>Esplora i progetti
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection