@extends('layouts.master')

@section('title', 'AE - Elimina Progetto')

@section('active_progetti', 'active')

@section('breadcrumb')
    <div class="bg-light py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('project.index') }}" class="text-decoration-none">Progetti Disponibili</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('project.show', $project) }}"
                            class="text-decoration-none">{{ $project->title }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Elimina Progetto</li>
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
                    <h1 class="h2 text-danger fw-bold mb-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Conferma Eliminazione
                    </h1>
                    <p class="text-muted">
                        Stai per eliminare definitivamente il progetto. Questa azione non può essere annullata.
                    </p>
                </div>

                <!-- Card del progetto da eliminare -->
                <div class="card border-danger mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-folder-x me-2"></i>
                            Progetto da Eliminare
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            @if($project->image_path)
                                <div class="col-md-4 text-center mb-3 mb-md-0">
                                    <img src="{{ $project->image_url }}" alt="Immagine progetto"
                                        class="img-fluid rounded shadow-sm" style="max-height: 120px;">
                                </div>
                            @endif
                            <div class="col-md-{{ $project->image_path ? '8' : '12' }}">
                                <h4 class="text-danger fw-bold">{{ $project->title }}</h4>
                                <div class="mb-2">
                                    <span class="badge bg-primary me-2">{{ $project->category->tag ?? 'N/A' }}</span>
                                    <span class="badge bg-success">{{ $project->association->name ?? 'N/A' }}</span>
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
                    <!-- Elimina -->
                    <div class="col-md-6">
                        <div class="card border-danger h-100 d-flex">
                            <div class="card-header bg-light border-danger">
                                <h6 class="text-danger fw-bold mb-0">
                                    <i class="bi bi-trash3 me-2"></i>
                                    Elimina Definitivamente
                                </h6>
                            </div>
                            <div class="card-body text-center d-flex flex-column">
                                <div class="mb-3">
                                    <i class="bi bi-exclamation-circle text-danger" style="font-size: 2rem;"></i>
                                </div>
                                <p class="text-muted mb-3 flex-grow-1">
                                    Il progetto <strong>sarà rimosso permanentemente</strong> dal database insieme a tutti i
                                    dati associati.
                                </p>
                                <form method="post" action="{{ route('project.destroy', ['id' => $project->id]) }}"
                                    class="mt-auto">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-ae btn-ae-danger btn-lg w-100">
                                        <i class="bi bi-trash3 me-2"></i>
                                        Elimina Progetto
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
                                    Annulla Operazione
                                </h6>
                            </div>
                            <div class="card-body text-center d-flex flex-column">
                                <div class="mb-3">
                                    <i class="bi bi-shield-check text-success" style="font-size: 2rem;"></i>
                                </div>
                                <p class="text-muted mb-3 flex-grow-1">
                                    Il progetto <strong>rimarrà invariato</strong> e non verrà eliminato dal database.
                                </p>
                                <div class="mt-auto">
                                    <a href="{{ route('project.index') }}" class="btn btn-ae btn-ae-secondary btn-lg w-100">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Torna ai Progetti
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
                            <h6 class="alert-heading">Attenzione!</h6>
                            <p class="mb-0">
                                Una volta eliminato, il progetto non potrà essere recuperato.
                                Assicurati di aver fatto un backup se necessario.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection