@extends('layouts.master')

@section('title', 'AE - Candidatura')

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
                    <li class="breadcrumb-item active" aria-current="page">Candidatura</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('body')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h1 class="h2 fw-bold text-dark mb-2">
                                <i class="bi bi-person-plus me-2"></i>Candidati per: {{ $project->title }}
                            </h1>
                            <p class="text-muted">
                                Compila il form sottostante per candidarti a questo progetto
                            </p>
                        </div>

                        <!-- Informazioni del progetto -->
                        <div class="alert alert-info mb-4">
                            <h5 class="fw-semibold mb-2">
                                <i class="bi bi-info-circle me-2"></i>Dettagli del progetto:
                            </h5>
                            <p class="mb-2">{{ $project->description }}</p>
                            @if($project->category)
                                <span class="badge bg-primary">
                                    <i class="bi bi-tag me-1"></i>{{ $project->category->name }}
                                </span>
                            @endif
                        </div>

                        <!-- Informazioni utente -->
                        <div class="bg-light rounded p-4 mb-4">
                            <h5 class="fw-semibold mb-3">
                                <i class="bi bi-person-circle me-2"></i>Le tue informazioni:
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">
                                        <i class="bi bi-person me-1"></i>Nome completo
                                    </label>
                                    <p class="mb-0">{{ Auth::user()->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">
                                        <i class="bi bi-envelope me-1"></i>Email
                                    </label>
                                    <p class="mb-0">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form di candidatura -->
                        <form action="{{ route('applications.store', $project->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Numero di telefono -->
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-medium">
                                    <i class="bi bi-telephone me-1"></i>Numero di telefono <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                    placeholder="es: +39 123 456 7890"
                                    class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Upload documento PDF -->
                            <div class="mb-4">
                                <label for="document" class="form-label fw-medium">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>Documento PDF (CV, Portfolio, Lettera di
                                    motivazione) <span class="text-danger">*</span>
                                </label>
                                <input type="file" id="document" name="document" accept=".pdf"
                                    class="form-control @error('document') is-invalid @enderror">
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>Solo file PDF fino a 5MB
                                </div>
                                @error('document')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Note informative -->
                            <div class="alert alert-warning">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-2">Informazioni importanti</h6>
                                        <ul class="mb-0 small">
                                            <li>Una volta inviata, la candidatura non potrà essere modificata</li>
                                            <li>Ti contatteremo tramite email o telefono per comunicarti l'esito</li>
                                            <li>Il documento caricato verrà utilizzato solo per valutare la tua candidatura
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottoni -->
                            <div class="d-flex justify-content-between pt-3">
                                <a href="{{ route('project.show', $project->id) }}" class="btn btn-ae btn-ae-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Annulla
                                </a>

                                <button type="submit" class="btn btn-ae btn-ae-primary">
                                    <i class="bi bi-send me-1"></i>Invia candidatura
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script per migliorare l'esperienza di upload
        document.getElementById('document').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024; // Convert to MB
                if (fileSize > 5) {
                    alert('Il file è troppo grande. La dimensione massima consentita è 5MB.');
                    e.target.value = '';
                    return;
                }

                if (file.type !== 'application/pdf') {
                    alert('Solo i file PDF sono accettati.');
                    e.target.value = '';
                    return;
                }
            }
        });
    </script>
@endsection