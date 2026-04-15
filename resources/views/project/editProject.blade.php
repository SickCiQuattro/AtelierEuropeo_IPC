@extends('layouts.master')

@section('title')
    @if (isset($project))
        AE - Modifica Progetto
    @else
        AE - Nuovo Progetto
    @endif
@endsection

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
                @if (isset($project))
                    <li class="breadcrumb-item">
                        <a href="{{ route('project.show', $project->id) }}" class="text-decoration-none">{{ $project->title }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Modifica Progetto</li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">Nuovo Progetto</li>
                @endif
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('body')
    <div class="container-fluid px-3 px-md-4 py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <!-- Header -->
                <div class="mb-4 text-center text-md-start">
                    <h1 class="h2 mb-2 text-primary fw-bold">
                        <i class="bi bi-{{ isset($project) ? 'pencil-square' : 'plus-circle' }} me-2"></i>
                        @if (isset($project))
                            Modifica Progetto
                        @else
                            Crea Nuovo Progetto
                        @endif
                    </h1>
                    <p class="text-muted mb-0">
                        @if (isset($project))
                            Modifica i dettagli di "{{ $project->title }}"
                        @else
                            Compila le informazioni per creare una nuova opportunità di progetto
                        @endif
                    </p>
                </div>

                <!-- Alert per messaggi di validazione -->
                <div id="validation-alert" class="alert alert-danger d-none" role="alert">
                    <h6 class="alert-heading mb-2">
                        <i class="bi bi-exclamation-triangle me-2"></i>Correggi i seguenti errori:
                    </h6>
                    <ul class="mb-0" id="validation-errors"></ul>
                </div>

                <!-- Alert di successo -->
                <div id="success-alert" class="alert alert-success d-none" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <span id="success-message"></span>
                </div>

                <!-- Card principale del form -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient bg-primary text-white border-0">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-form me-2"></i>Informazioni Progetto
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if (isset($project))
                            <form id="project-form" class="needs-validation" name="project" method="post"
                                action="{{ route('project.update', ['id' => $project->id]) }}" enctype="multipart/form-data" novalidate>
                                @method('PUT')
                        @else
                            <form id="project-form" class="needs-validation" name="project" method="post" action="{{ route('project.store') }}" enctype="multipart/form-data" novalidate>
                        @endif
                        @csrf

                        <!-- Sezione 1: Informazioni di Base -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3 border-bottom border-primary border-opacity-25 pb-2">
                                    <i class="bi bi-info-circle me-2"></i>Informazioni di Base
                                </h6>
                            </div>
                            
                            <!-- Titolo -->
                            <div class="col-12 mb-3">
                                <label for="title" class="form-label fw-medium">
                                    Titolo del Progetto <span class="text-danger">*</span>
                                </label>
                                <input class="form-control @error('title') is-invalid @enderror"
                                    type="text" name="title" id="title"
                                    value="{{ old('title', $project->title ?? '') }}"
                                    placeholder="Inserisci un titolo descrittivo per il progetto" required />
                                <div class="invalid-feedback" id="title-error"></div>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Categoria e Associazione -->
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label fw-medium">
                                    Categoria <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('category_id') is-invalid @enderror"
                                    name="category_id" id="category_id" required>
                                    <option value="">Seleziona una categoria...</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            @if (old('category_id', $project->category_id ?? '') == $category->id) selected @endif>
                                            {{ $category->tag }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="category_id-error"></div>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="association_id" class="form-label fw-medium">
                                    Associazione <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('association_id') is-invalid @enderror"
                                    name="association_id" id="association_id" required>
                                    <option value="">Seleziona un'associazione...</option>
                                    @foreach ($associations as $association)
                                        <option value="{{ $association->id }}"
                                            @if (old('association_id', $project->association_id ?? '') == $association->id) selected @endif>
                                            {{ $association->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="association_id-error"></div>
                                @error('association_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Autore del progetto -->
                            @if (isset($project))
                                <input type="hidden" name="user_id" value="{{ $project->user_id }}" />
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-medium">Creatore del Progetto</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-secondary text-white">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <input class="form-control" type="text" 
                                               value="{{ $project->author->name }}" disabled />
                                    </div>
                                    <small class="text-muted">Il creatore del progetto non può essere modificato</small>
                                </div>
                            @else
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}" />
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-medium">Creatore del Progetto</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="bi bi-person-check"></i>
                                        </span>
                                        <input class="form-control" type="text" 
                                               value="{{ auth()->user()->name }}" disabled />
                                    </div>
                                    <small class="text-success">Sarai impostato come creatore del progetto</small>
                                </div>
                            @endif
                        </div>

                        <!-- Sezione 2: Dettagli del Progetto -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info fw-bold mb-3 border-bottom border-info border-opacity-25 pb-2">
                                    <i class="bi bi-gear me-2"></i>Dettagli del Progetto
                                </h6>
                            </div>
                            
                            <!-- Stato e Persone Richieste -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-medium">Stato</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" id="status">
                                    <option value="draft" @if (old('status', $project->status ?? 'draft') == 'draft') selected @endif>
                                        Bozza
                                    </option>
                                    <option value="published" @if (old('status', $project->status ?? '') == 'published') selected @endif>
                                        Pubblicato
                                    </option>
                                    @if (isset($project))
                                        <!-- L'opzione "Completato" è disponibile solo durante la modifica -->
                                        <option value="completed" @if (old('status', $project->status ?? '') == 'completed') selected @endif>
                                            Completato
                                        </option>
                                    @endif
                                </select>
                                <div class="invalid-feedback" id="status-error"></div>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="requested_people" class="form-label fw-medium">Persone Richieste <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-info text-white">
                                        <i class="bi bi-people"></i>
                                    </span>
                                    <input class="form-control @error('requested_people') is-invalid @enderror"
                                        type="number" name="requested_people" id="requested_people" min="1"
                                        value="{{ old('requested_people', $project->requested_people ?? '') }}"
                                        placeholder="Numero di persone necessarie" required />
                                </div>
                                <div class="invalid-feedback" id="requested_people-error"></div>
                                @error('requested_people')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Ubicazione -->
                            <div class="col-12 mb-3">
                                <label for="location" class="form-label fw-medium">Ubicazione <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-warning text-dark">
                                        <i class="bi bi-geo-alt"></i>
                                    </span>
                                    <input class="form-control @error('location') is-invalid @enderror"
                                        type="text" name="location" id="location"
                                        value="{{ old('location', $project->location ?? '') }}"
                                        placeholder="Ubicazione del progetto o 'Remoto'" required />
                                </div>
                                <div class="invalid-feedback" id="location-error"></div>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Upload Immagine -->
                            <div class="col-12 mb-3">
                                <label for="image_path" class="form-label fw-medium">
                                    Immagine del Progetto 
                                    @if (!isset($project))
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                
                                @if (isset($project) && $project->image_path)
                                    <!-- Mostra l'immagine corrente durante la modifica -->
                                    <div class="mb-2">
                                        <small class="text-muted">Immagine attuale:</small>
                                        <div class="mt-1">
                                            @if (str_starts_with($project->image_path, 'http') || str_starts_with($project->image_path, 'img/'))
                                                <!-- Immagine da URL o path pubblico (seed/default) -->
                                                <img src="{{ $project->image_url }}" 
                                                     alt="Immagine progetto" 
                                                     class="img-thumbnail" 
                                                     style="max-height: 100px;">
                                            @else
                                                <!-- Immagine da storage (file upload) -->
                                                <img src="{{ $project->image_url }}" 
                                                     alt="Immagine progetto" 
                                                     class="img-thumbnail" 
                                                     style="max-height: 100px;">
                                            @endif
                                        </div>
                                        <small class="text-info">Seleziona un nuovo file per sostituire l'immagine corrente</small>
                                    </div>
                                @endif
                                
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white">
                                        <i class="bi bi-image"></i>
                                    </span>
                                    <input class="form-control @error('image_path') is-invalid @enderror"
                                        type="file" name="image_path" id="image_path"
                                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                        @if (!isset($project)) required @endif />
                                </div>
                                <div class="form-text">
                                    Formati accettati: JPEG, JPG, PNG, GIF, WEBP. Dimensione massima: 2MB
                                </div>
                                <div class="invalid-feedback" id="image_path-error"></div>
                                @error('image_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Sezione 3: Date Importanti -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-warning fw-bold mb-3 border-bottom border-warning border-opacity-25 pb-2">
                                    <i class="bi bi-calendar3 me-2"></i>Date Importanti
                                </h6>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">Data di Inizio <span class="text-danger">*</span></label>
                                <input class="form-control @error('start_date') is-invalid @enderror"
                                    type="date" name="start_date" id="start_date"
                                    value="{{ old('start_date', (isset($project) && $project->start_date) ? $project->start_date->format('Y-m-d') : '') }}" required />
                                <div class="invalid-feedback" id="start_date-error"></div>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">Data di Fine <span class="text-danger">*</span></label>
                                <input class="form-control @error('end_date') is-invalid @enderror"
                                    type="date" name="end_date" id="end_date"
                                    value="{{ old('end_date', (isset($project) && $project->end_date) ? $project->end_date->format('Y-m-d') : '') }}" required />
                                <div class="invalid-feedback" id="end_date-error"></div>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="expire_date" class="form-label">Scadenza Candidature <span class="text-danger">*</span></label>
                                <input class="form-control @error('expire_date') is-invalid @enderror"
                                    type="date" name="expire_date" id="expire_date"
                                    value="{{ old('expire_date', (isset($project) && $project->expire_date) ? $project->expire_date->format('Y-m-d') : '') }}" required />
                                <div class="invalid-feedback" id="expire_date-error"></div>
                                @error('expire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Sezione 4: Descrizioni del Progetto -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-success fw-bold mb-3 border-bottom border-success border-opacity-25 pb-2">
                                    <i class="bi bi-file-text me-2"></i>Descrizione del Progetto
                                </h6>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="sum_description" class="form-label">Descrizione Riassuntiva <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('sum_description') is-invalid @enderror" name="sum_description"
                                    id="sum_description" rows="3" 
                                    placeholder="Scrivi un riassunto breve e accattivante del tuo progetto..." required>{{ old('sum_description', $project->sum_description ?? '') }}</textarea>
                                <div class="form-text">Questo sarà mostrato negli elenchi e nelle anteprime dei progetti</div>
                                <div class="invalid-feedback" id="sum_description-error"></div>
                                @error('sum_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="full_description" class="form-label">Descrizione Completa <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('full_description') is-invalid @enderror" name="full_description"
                                    id="full_description" rows="6"
                                    placeholder="Fornisci una descrizione dettagliata del progetto, dei suoi obiettivi, delle attività e dei risultati attesi..." required>{{ old('full_description', $project->full_description ?? '') }}</textarea>
                                <div class="invalid-feedback" id="full_description-error"></div>
                                @error('full_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Sezione 5: Requisiti e Condizioni -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-danger fw-bold mb-3 border-bottom border-danger border-opacity-25 pb-2">
                                    <i class="bi bi-list-check me-2"></i>Requisiti e Condizioni
                                </h6>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="requirements" class="form-label">Requisiti <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('requirements') is-invalid @enderror" name="requirements" id="requirements"
                                    rows="4"
                                    placeholder="Elenca le competenze richieste, le qualifiche, la conoscenza delle lingue o qualsiasi prerequisito..." required>{{ old('requirements', $project->requirements ?? '') }}</textarea>
                                <div class="invalid-feedback" id="requirements-error"></div>
                                @error('requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="travel_conditions" class="form-label">Condizioni di Viaggio <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('travel_conditions') is-invalid @enderror" name="travel_conditions"
                                    id="travel_conditions" rows="3"
                                    placeholder="Descrivi modalità di viaggio, dettagli dell'alloggio, copertura delle spese, requisiti per visti..." required>{{ old('travel_conditions', $project->travel_conditions ?? '') }}</textarea>
                                <div class="invalid-feedback" id="travel_conditions-error"></div>
                                @error('travel_conditions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Pulsanti di Azione -->
                        <div class="row">
                            <div class="col-12">
                                <div class="border-top border-primary border-opacity-25 pt-4">
                                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-between">
                                        <a class="btn btn-ae btn-ae-outline-secondary d-flex align-items-center justify-content-center" href="{{ route('project.index') }}">
                                            <i class="bi bi-arrow-left me-2"></i>Annulla e Torna Indietro
                                        </a>
                                        
                                        <div class="d-flex gap-2">
                                            @if (isset($project))
                                                <button type="submit" class="btn btn-ae btn-ae-primary d-flex align-items-center justify-content-center" id="submit-btn">
                                                    <i class="bi bi-check-circle me-2"></i>
                                                    <span class="btn-text">Aggiorna Progetto</span>
                                                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-ae btn-ae-success d-flex align-items-center justify-content-center" id="submit-btn">
                                                    <i class="bi bi-rocket-takeoff me-2"></i>
                                                    <span class="btn-text">Lancia il Tuo Progetto</span>
                                                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script per validazione AJAX -->
    <script>
        $(document).ready(function() {
            let isValidating = false;
            let titleCheckTimeout;
            const projectId = {{ isset($project) ? $project->id : 'null' }};
            
            // Regole di validazione
            const validationRules = {
                title: {
                    required: true,
                    minLength: 5,
                    maxLength: 255,
                    message: 'Il titolo è obbligatorio e deve essere tra 5 e 255 caratteri.'
                },
                category_id: {
                    required: true,
                    message: 'La categoria è obbligatoria.'
                },
                association_id: {
                    required: true,
                    message: 'L\'associazione è obbligatoria.'
                },
                requested_people: {
                    required: true,
                    min: 1,
                    max: 999,
                    message: 'Il numero di persone richieste è obbligatorio e deve essere tra 1 e 999.'
                },
                location: {
                    required: true,
                    minLength: 2,
                    maxLength: 255,
                    message: 'L\'ubicazione è obbligatoria e deve essere tra 2 e 255 caratteri.'
                },
                image_path: {
                    required: {{ isset($project) ? 'false' : 'true' }}, // Obbligatorio solo durante la creazione
                    fileTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'],
                    maxSize: 2048, // 2MB in KB
                    message: 'L\'immagine è obbligatoria e deve essere in formato JPEG, JPG, PNG, GIF o WEBP (max 2MB).'
                },
                start_date: {
                    required: true,
                    message: 'La data di inizio è obbligatoria.'
                },
                end_date: {
                    required: true,
                    message: 'La data di fine è obbligatoria.'
                },
                expire_date: {
                    required: true,
                    message: 'La scadenza delle candidature è obbligatoria.'
                },
                sum_description: {
                    required: true,
                    minLength: 20,
                    maxLength: 500,
                    message: 'La descrizione riassuntiva è obbligatoria e deve essere tra 20 e 500 caratteri.'
                },
                full_description: {
                    required: true,
                    minLength: 50,
                    message: 'La descrizione completa è obbligatoria e deve essere almeno di 50 caratteri.'
                },
                requirements: {
                    required: true,
                    minLength: 10,
                    message: 'I requisiti sono obbligatori e devono essere almeno di 10 caratteri.'
                },
                travel_conditions: {
                    required: true,
                    minLength: 10,
                    message: 'Le condizioni di viaggio sono obbligatorie e devono essere almeno di 10 caratteri.'
                }
            };

            // Funzione per controllare se il titolo è unico
            function checkTitleUnique(title, callback) {
                if (!title || title.trim().length < 5) {
                    callback(true); // Non controllare se il titolo è troppo corto
                    return;
                }

                $.ajax({
                    url: '{{ route("project.checkTitle") }}',
                    method: 'POST',
                    data: {
                        title: title,
                        project_id: projectId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        callback(!response.exists);
                    },
                    error: function() {
                        callback(true); // In caso di errore, considera valido
                    }
                });
            }

            // Funzione di validazione singolo campo
            function validateField(fieldName, value, fileInput = null) {
                const rule = validationRules[fieldName];
                if (!rule) return { valid: true };

                // Validazione speciale per file upload
                if (fieldName === 'image_path' && fileInput && fileInput.files && fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    
                    // Controllo tipo file
                    if (rule.fileTypes && !rule.fileTypes.includes(file.type)) {
                        return { valid: false, message: 'Il file deve essere un\'immagine in formato JPEG, JPG, PNG, GIF o WEBP.' };
                    }
                    
                    // Controllo dimensione file (in KB)
                    if (rule.maxSize && file.size > rule.maxSize * 1024) {
                        return { valid: false, message: 'L\'immagine non può superare i 2MB.' };
                    }
                    
                    return { valid: true };
                } else if (fieldName === 'image_path') {
                    // Se non c'è file selezionato, controlla se è richiesto
                    if (rule.required && (!fileInput || !fileInput.files || fileInput.files.length === 0)) {
                        return { valid: false, message: rule.message };
                    }
                    return { valid: true };
                }

                // Required
                if (rule.required && (!value || value.trim() === '')) {
                    return { valid: false, message: rule.message };
                }

                // MinLength
                if (rule.minLength && value && value.length < rule.minLength) {
                    return { valid: false, message: rule.message };
                }

                // MaxLength
                if (rule.maxLength && value && value.length > rule.maxLength) {
                    return { valid: false, message: rule.message };
                }

                // Min (numerico)
                if (rule.min && value && parseInt(value) < rule.min) {
                    return { valid: false, message: rule.message };
                }

                // Max (numerico)
                if (rule.max && value && parseInt(value) > rule.max) {
                    return { valid: false, message: rule.message };
                }

                // Pattern
                if (rule.pattern && value && !rule.pattern.test(value)) {
                    return { valid: false, message: rule.message };
                }

                return { valid: true };
            }

            // Validazione delle date
            function validateDates() {
                const startDate = new Date($('#start_date').val());
                const endDate = new Date($('#end_date').val());
                const expireDate = new Date($('#expire_date').val());
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const isUpdate = {{ isset($project) ? 'true' : 'false' }};

                let errors = [];

                // Per i nuovi progetti, controlla che le date non siano nel passato
                if (!isUpdate) {
                    if (startDate < today) {
                        errors.push({ field: 'start_date', message: 'La data di inizio non può essere nel passato.' });
                    }

                    if (expireDate < today) {
                        errors.push({ field: 'expire_date', message: 'La scadenza delle candidature non può essere nel passato.' });
                    }
                }

                // Per tutti i progetti, controlla la coerenza tra le date
                if (endDate <= startDate) {
                    errors.push({ field: 'end_date', message: 'La data di fine deve essere successiva alla data di inizio.' });
                }

                if (expireDate >= startDate) {
                    errors.push({ field: 'expire_date', message: 'La scadenza delle candidature deve essere prima della data di inizio.' });
                }

                return errors;
            }

            // Mostra errore per un campo
            function showFieldError(fieldName, message) {
                const field = $(`#${fieldName}`);
                const errorDiv = $(`#${fieldName}-error`);
                
                field.addClass('is-invalid');
                errorDiv.text(message).show();
            }

            // Rimuovi errore per un campo
            function clearFieldError(fieldName) {
                const field = $(`#${fieldName}`);
                const errorDiv = $(`#${fieldName}-error`);
                
                field.removeClass('is-invalid');
                errorDiv.text('').hide();
            }

            // Validazione in tempo reale per ogni campo
            $('input, select, textarea').on('blur change', function() {
                const fieldName = $(this).attr('name');
                const value = $(this).val();
                
                if (fieldName && validationRules[fieldName]) {
                    // Gestione speciale per file input
                    if (fieldName === 'image_path') {
                        const validation = validateField(fieldName, value, this);
                        if (validation.valid) {
                            clearFieldError(fieldName);
                        } else {
                            showFieldError(fieldName, validation.message);
                        }
                    } else {
                        const validation = validateField(fieldName, value);
                        if (validation.valid) {
                            clearFieldError(fieldName);
                        } else {
                            showFieldError(fieldName, validation.message);
                        }
                    }
                }
            });

            // Validazione specifica per file upload con preview
            $('#image_path').on('change', function() {
                const fileInput = this;
                const validation = validateField('image_path', '', fileInput);
                
                if (validation.valid) {
                    clearFieldError('image_path');
                    // Mostra preview dell'immagine se valida
                    if (fileInput.files && fileInput.files[0]) {
                        showImagePreview(fileInput.files[0]);
                    }
                } else {
                    showFieldError('image_path', validation.message);
                    removeImagePreview();
                }
            });

            // Funzione per mostrare preview dell'immagine
            function showImagePreview(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let previewContainer = $('#image-preview');
                    if (previewContainer.length === 0) {
                        previewContainer = $('<div id="image-preview" class="mt-2"></div>');
                        $('#image_path').closest('.col-12').append(previewContainer);
                    }
                    
                    previewContainer.html(`
                        <small class="text-success">Anteprima nuova immagine:</small>
                        <div class="mt-1">
                            <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-height: 100px;">
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            }

            // Funzione per rimuovere preview dell'immagine
            function removeImagePreview() {
                $('#image-preview').remove();
            }

            // Validazione speciale per il titolo con controllo duplicati
            $('#title').on('input', function() {
                const title = $(this).val();
                
                // Cancella il timeout precedente
                clearTimeout(titleCheckTimeout);
                
                // Valida prima la lunghezza
                const validation = validateField('title', title);
                if (!validation.valid) {
                    showFieldError('title', validation.message);
                    return;
                }
                
                // Se la validazione base è ok, controlla i duplicati con debounce
                titleCheckTimeout = setTimeout(function() {
                    if (title.length >= 5) {
                        // Mostra indicatore di caricamento
                        $('#title').addClass('is-loading');
                        
                        checkTitleUnique(title, function(isUnique) {
                            $('#title').removeClass('is-loading');
                            
                            if (!isUnique) {
                                showFieldError('title', 'Esiste già un progetto con questo titolo. Scegli un titolo diverso.');
                            } else {
                                clearFieldError('title');
                                // Mostra feedback positivo
                                showFieldSuccess('title', 'Titolo disponibile!');
                            }
                        });
                    }
                }, 800); // Attende 800ms dopo che l'utente smette di digitare
            });

            // Funzione per mostrare feedback positivo
            function showFieldSuccess(fieldName, message) {
                const field = $(`#${fieldName}`);
                const errorDiv = $(`#${fieldName}-error`);
                
                field.removeClass('is-invalid').addClass('is-valid');
                errorDiv.removeClass('invalid-feedback').addClass('valid-feedback')
                        .css('color', '#198754').text(message).show();
                
                // Rimuovi il feedback positivo dopo 3 secondi
                setTimeout(function() {
                    field.removeClass('is-valid');
                    errorDiv.removeClass('valid-feedback').addClass('invalid-feedback')
                            .css('color', '').text('').hide();
                }, 3000);
            }

            // Validazione delle date in tempo reale
            $('#start_date, #end_date, #expire_date').on('change', function() {
                const dateErrors = validateDates();
                
                // Pulisci tutti gli errori di data
                clearFieldError('start_date');
                clearFieldError('end_date');
                clearFieldError('expire_date');
                
                // Mostra nuovi errori
                dateErrors.forEach(error => {
                    showFieldError(error.field, error.message);
                });
            });

            // Gestione submit del form
            $('#project-form').on('submit', function(e) {
                e.preventDefault();
                
                if (isValidating) return;
                isValidating = true;
                
                // Nascondi alert precedenti
                $('#validation-alert').addClass('d-none');
                $('#success-alert').addClass('d-none');
                
                // Controlla se si sta tentando di completare il progetto
                const currentStatus = {{ isset($project) ? "'".$project->status."'" : "'draft'" }};
                const newStatus = $('#status').val();
                
                if (newStatus === 'completed' && currentStatus !== 'completed') {
                    // Chiedi conferma per il completamento
                    if (confirm('ATTENZIONE: Una volta contrassegnato come completato, il progetto non potrà più essere modificato. Vuoi procedere con la conferma?')) {
                        // Procedi con l'invio normale del form che reindirizzerà alla pagina di conferma
                        this.submit();
                        return;
                    } else {
                        // Ripristina lo stato precedente
                        $('#status').val(currentStatus);
                        isValidating = false;
                        return;
                    }
                }
                
                // Mostra loading sul pulsante
                const submitBtn = $('#submit-btn');
                const btnText = submitBtn.find('.btn-text');
                const spinner = submitBtn.find('.spinner-border');
                
                submitBtn.prop('disabled', true);
                spinner.removeClass('d-none');
                btnText.text('Validazione in corso...');

                // Validazione migliorata ma non troppo restrittiva
                let hasErrors = false;
                const errors = [];
                
                // Valida i campi principali
                const title = $('#title').val();
                if (!title || title.trim().length < 5) {
                    hasErrors = true;
                    errors.push('Il titolo è obbligatorio e deve essere di almeno 5 caratteri.');
                    showFieldError('title', 'Il titolo è obbligatorio e deve essere di almeno 5 caratteri.');
                } else {
                    clearFieldError('title');
                }
                
                const categoryId = $('#category_id').val();
                if (!categoryId) {
                    hasErrors = true;
                    errors.push('La categoria è obbligatoria.');
                    showFieldError('category_id', 'La categoria è obbligatoria.');
                } else {
                    clearFieldError('category_id');
                }
                
                const associationId = $('#association_id').val();
                if (!associationId) {
                    hasErrors = true;
                    errors.push('L\'associazione è obbligatoria.');
                    showFieldError('association_id', 'L\'associazione è obbligatoria.');
                } else {
                    clearFieldError('association_id');
                }
                
                // Valida altri campi essenziali
                const requestedPeople = $('#requested_people').val();
                if (!requestedPeople || parseInt(requestedPeople) < 1) {
                    hasErrors = true;
                    errors.push('Il numero di persone richieste deve essere almeno 1.');
                    showFieldError('requested_people', 'Il numero di persone richieste deve essere almeno 1.');
                } else {
                    clearFieldError('requested_people');
                }
                
                const location = $('#location').val();
                if (!location || location.trim().length < 2) {
                    hasErrors = true;
                    errors.push('L\'ubicazione è obbligatoria.');
                    showFieldError('location', 'L\'ubicazione è obbligatoria.');
                } else {
                    clearFieldError('location');
                }
                
                // Valida le date (versione semplificata)
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const expireDate = $('#expire_date').val();
                
                if (!startDate) {
                    hasErrors = true;
                    errors.push('La data di inizio è obbligatoria.');
                    showFieldError('start_date', 'La data di inizio è obbligatoria.');
                } else {
                    clearFieldError('start_date');
                }
                
                if (!endDate) {
                    hasErrors = true;
                    errors.push('La data di fine è obbligatoria.');
                    showFieldError('end_date', 'La data di fine è obbligatoria.');
                } else {
                    clearFieldError('end_date');
                }
                
                if (!expireDate) {
                    hasErrors = true;
                    errors.push('La scadenza delle candidature è obbligatoria.');
                    showFieldError('expire_date', 'La scadenza delle candidature è obbligatoria.');
                } else {
                    clearFieldError('expire_date');
                }
                
                // Valida le descrizioni
                const sumDescription = $('#sum_description').val();
                if (!sumDescription || sumDescription.trim().length < 20) {
                    hasErrors = true;
                    errors.push('La descrizione riassuntiva deve essere di almeno 20 caratteri.');
                    showFieldError('sum_description', 'La descrizione riassuntiva deve essere di almeno 20 caratteri.');
                } else {
                    clearFieldError('sum_description');
                }
                
                const fullDescription = $('#full_description').val();
                if (!fullDescription || fullDescription.trim().length < 50) {
                    hasErrors = true;
                    errors.push('La descrizione completa deve essere di almeno 50 caratteri.');
                    showFieldError('full_description', 'La descrizione completa deve essere di almeno 50 caratteri.');
                } else {
                    clearFieldError('full_description');
                }
                
                const requirements = $('#requirements').val();
                if (!requirements || requirements.trim().length < 10) {
                    hasErrors = true;
                    errors.push('I requisiti devono essere di almeno 10 caratteri.');
                    showFieldError('requirements', 'I requisiti devono essere di almeno 10 caratteri.');
                } else {
                    clearFieldError('requirements');
                }
                
                const travelConditions = $('#travel_conditions').val();
                if (!travelConditions || travelConditions.trim().length < 10) {
                    hasErrors = true;
                    errors.push('Le condizioni di viaggio devono essere di almeno 10 caratteri.');
                    showFieldError('travel_conditions', 'Le condizioni di viaggio devono essere di almeno 10 caratteri.');
                } else {
                    clearFieldError('travel_conditions');
                }

                if (hasErrors) {
                    const errorList = $('#validation-errors');
                    errorList.empty();
                    errors.forEach(error => {
                        errorList.append(`<li>${error}</li>`);
                    });
                    $('#validation-alert').removeClass('d-none');
                    
                    // Reset pulsante
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                    btnText.text({{ isset($project) ? "'Aggiorna Progetto'" : "'Lancia il Tuo Progetto'" }});
                    
                    // Scroll verso l'alto per mostrare gli errori
                    $('html, body').animate({ scrollTop: 0 }, 500);
                    
                    isValidating = false;
                    return;
                }

                // Se tutto è valido, invia il form
                btnText.text('Invio in corso...');
                this.submit();
            });
            
            /*
            // Funzione helper per finalizzare la validazione
            function finalizaValidation(hasErrors, errors, submitBtn, btnText, spinner) {
                if (hasErrors) {
                    const errorList = $('#validation-errors');
                    errorList.empty();
                    errors.forEach(error => {
                        errorList.append(`<li>${error}</li>`);
                    });
                    $('#validation-alert').removeClass('d-none');
                    
                    // Reset pulsante
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                    btnText.text({{ isset($project) ? "'Aggiorna Progetto'" : "'Lancia il Tuo Progetto'" }});
                    
                    // Scroll verso l'alto per mostrare gli errori
                    $('html, body').animate({ scrollTop: 0 }, 500);
                    
                    isValidating = false;
                    return;
                }

                // Se tutto è valido, invia il form
                btnText.text('Invio in corso...');
                document.getElementById('project-form').submit();
            }
            */
        });
    </script>
    
    <!-- CSS per indicatore di caricamento -->
    <style>
        .is-loading {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23007bff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 2v4l3 3'/%3E%3Ccircle cx='8' cy='8' r='6' fill='none' stroke='%23007bff' stroke-width='2'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .valid-feedback {
            display: block !important;
        }
    </style>
    </script>
@endsection
