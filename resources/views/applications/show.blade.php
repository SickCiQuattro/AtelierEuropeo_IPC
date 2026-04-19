@extends('layouts.master')

@section('title', 'AE - Dettagli Candidatura')

@section('breadcrumb')
    <div class="bg-light py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('applications.index') }}" class="text-decoration-none">Le Mie Candidature</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Dettagli Candidatura</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('body')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('applications.index') }}" class="text-decoration-none">
                                <i class="bi bi-file-earmark-text me-1"></i>Le mie candidature
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Candidatura #{{ $application->id }}
                        </li>
                    </ol>
                </nav>

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h1 class="h2 fw-bold text-dark mb-2">
                            <i class="bi bi-person-check me-2"></i>Candidatura per: {{ $application->project->title }}
                        </h1>
                        <p class="text-muted">
                            <i class="bi bi-calendar3 me-1"></i>Inviata il
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
                            <i class="bi bi-clock me-1"></i>In attesa di valutazione
                        @elseif($application->status === 'approved')
                            <i class="bi bi-check-circle me-1"></i>Candidatura approvata
                        @elseif($application->status === 'rejected')
                            <i class="bi bi-x-circle me-1"></i>Candidatura rifiutata
                        @endif
                    </span>
                </div>

                <div class="row">
                    <!-- Colonna principale -->
                    <div class="col-lg-8">
                        <!-- Dettagli del progetto -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold mb-3">
                                    <i class="bi bi-info-circle me-2"></i>Dettagli del progetto
                                </h5>

                                <div class="mb-3">
                                    <h6 class="fw-medium text-dark">Titolo</h6>
                                    <p class="text-muted">{{ $application->project->title }}</p>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-medium text-dark">Descrizione</h6>
                                    <p class="text-muted">{{ $application->project->description }}</p>
                                </div>

                                @if($application->project->category)
                                    <div class="mb-3">
                                        <h6 class="fw-medium text-dark">
                                            <i class="bi bi-tag me-1"></i>Categoria
                                        </h6>
                                        <span class="badge bg-primary">
                                            {{ $application->project->category->name }}
                                        </span>
                                    </div>
                                @endif

                                <div class="pt-3">
                                    <a href="{{ route('project.show', $application->project->id) }}"
                                        class="btn btn-ae btn-ae-primary">
                                        <i class="bi bi-eye me-1"></i>Visualizza progetto completo
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Stato candidatura -->
                        @if($application->status === 'pending')
                            <div class="alert alert-warning">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="bi bi-clock-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Candidatura in fase di valutazione</h6>
                                        <p class="mb-0 small">
                                            La tua candidatura è stata ricevuta e verrà valutata dal nostro team. Ti
                                            contatteremo presto per comunicarti l'esito.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($application->status === 'approved')
                            <div class="alert alert-success">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Congratulazioni! Candidatura approvata</h6>
                                        <p class="mb-0 small">
                                            La tua candidatura è stata approvata. Sarai contattato presto dal nostro team per i
                                            prossimi passi.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($application->status === 'rejected')
                            <div class="alert alert-danger">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Candidatura non selezionata</h6>
                                        <p class="mb-0 small">
                                            Purtroppo la tua candidatura non è stata selezionata per questo progetto. Ti
                                            incoraggiamo a candidarti per altri progetti disponibili.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Informazioni candidato -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-title fw-semibold mb-3">
                                    <i class="bi bi-person me-2"></i>Le tue informazioni
                                </h6>

                                <div class="mb-3">
                                    <span class="fw-medium text-dark small">
                                        <i class="bi bi-person me-1"></i>Nome:
                                    </span>
                                    <p class="mb-0">{{ $application->user->name }}</p>
                                </div>

                                <div class="mb-3">
                                    <span class="fw-medium text-dark small">
                                        <i class="bi bi-envelope me-1"></i>Email:
                                    </span>
                                    <p class="mb-0">{{ $application->user->email }}</p>
                                </div>

                                <div class="mb-0">
                                    <span class="fw-medium text-dark small">
                                        <i class="bi bi-telephone me-1"></i>Telefono:
                                    </span>
                                    <p class="mb-0">{{ $application->phone }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Documento allegato -->
                        @if($application->document_path)
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title fw-semibold mb-3">
                                        <i class="bi bi-file-earmark-pdf me-2"></i>Documento allegato
                                    </h6>

                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <div class="me-3">
                                            <i class="bi bi-file-earmark-pdf text-danger fs-3"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 fw-medium">{{ $application->document_name }}</p>
                                            <p class="mb-0 small text-muted">Documento PDF</p>
                                        </div>
                                        <div>
                                            <a href="{{ asset('storage/' . $application->document_path) }}" target="_blank"
                                                class="btn btn-ae btn-ae-outline-primary btn-sm">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection