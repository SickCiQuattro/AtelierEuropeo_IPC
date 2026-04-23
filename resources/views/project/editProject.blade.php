@extends('layouts.master')

@section('title', isset($project) ? 'AE - Modifica Progetto' : 'AE - Nuovo Progetto')

@section('active_progetti', 'active')

@section('body')
    @php
        $isEditMode = isset($project);
        $currentProjectStatus = $isEditMode ? ($project->status ?? 'draft') : 'draft';
        $statusLabels = [
            \App\Models\Project::STATUS_DRAFT => 'Bozza',
            \App\Models\Project::STATUS_PUBLISHED => 'Pubblicato',
            \App\Models\Project::STATUS_COMPLETED => 'Completato',
        ];
        $allowedStatuses = $isEditMode
            ? \App\Models\Project::allowedStatusTransitions($currentProjectStatus)
            : [\App\Models\Project::STATUS_DRAFT, \App\Models\Project::STATUS_PUBLISHED];
        $nextAllowedStatus = \App\Models\Project::nextStatusTransition($currentProjectStatus);
        $selectedStatus = strtolower((string) old('status', $currentProjectStatus));

        if (!in_array($selectedStatus, $allowedStatuses, true)) {
            $selectedStatus = in_array($currentProjectStatus, $allowedStatuses, true)
                ? $currentProjectStatus
                : ($allowedStatuses[0] ?? \App\Models\Project::STATUS_DRAFT);
        }

        $openCompletionModal = request()->boolean('openCompletionModal');
        $defaultBackUrl = route('admin.projects.index');
        
        $currentUrl = url()->current();
        $previousUrl = url()->previous();
        $previousPath = parse_url($previousUrl, PHP_URL_PATH) ?? '';
        $isShowcaseOrLegacyProjects = $previousPath === '/progetti' || $previousPath === '/project';
        $isUnsafeBackTarget = $previousUrl === $currentUrl
            || str_contains($previousPath, '/project/create')
            || (str_contains($previousPath, '/project/') && str_contains($previousPath, '/edit'))
            || $isShowcaseOrLegacyProjects;

        $backUrl = $isUnsafeBackTarget ? $defaultBackUrl : $previousUrl;
    @endphp

    <style>
        #project-form .ae-invalid-field {
            border: 2px solid var(--bs-danger) !important;
            background-color: #fff5f5 !important;
            box-shadow: none !important;
        }

        #project-form .ae-invalid-addon {
            border-top: 2px solid var(--bs-danger) !important;
            border-bottom: 2px solid var(--bs-danger) !important;
            border-left: 2px solid var(--bs-danger) !important;
            border-right: 0 !important;
            color: var(--bs-danger) !important;
            background-color: #fff5f5 !important;
        }

        #project-form .input-group .ae-invalid-field {
            border-left: 0 !important;
        }

        .project-form-actions {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        .project-form-actions-inner {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 0.65rem;
            min-width: max-content;
        }

        @media (min-width: 1200px) {
            .project-form-actions {
                overflow-x: visible;
            }
        }
    </style>

    <div class="container-fluid px-3 px-md-4 py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                
                <div class="d-md-none mb-3">
                    <button type="button" class="btn btn-ae btn-ae-light border shadow-sm rounded-pill px-3 py-2 text-secondary fw-semibold transition-hover js-cancel-process"
                        data-cancel-url="{{ $backUrl }}">
                        <i class="bi bi-arrow-left me-2"></i>Indietro
                    </button>
                </div>

                <div class="d-none d-md-block">
                    <x-breadcrumb>
                        <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Progetti</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $isEditMode ? 'Modifica Progetto' : 'Nuovo Progetto' }}</li>
                    </x-breadcrumb>
                </div>

                <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h1 class="h3 fw-bold text-primary mb-1">
                            <i class="bi bi-{{ $isEditMode ? 'pencil-fill' : 'plus-circle-fill' }} me-2"></i>
                            {{ $isEditMode ? 'Modifica Progetto' : 'Nuovo Progetto' }}
                        </h1>
                        <p class="text-secondary mb-0 small">Compila i dettagli per la pubblicazione nella vetrina.</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-5" style="border-radius: 1.25rem; background-color: var(--project-card-bg);">
                    <div class="card-body p-4 p-md-5">
                        
                        <form id="project-form" class="needs-validation" method="post" enctype="multipart/form-data" novalidate
                              action="{{ $isEditMode ? route('project.update', ['id' => $project->id]) : route('project.store') }}">
                            @if ($isEditMode) @method('PUT') @endif
                            @csrf
                            <input type="hidden" name="form_submit_mode" id="form_submit_mode" value="publish">
                            <input type="hidden" name="completion_confirmed" id="completion_confirmed" value="0">
                            <input type="hidden" name="user_id" value="{{ $isEditMode ? $project->user_id : auth()->id() }}" />
                            <input type="hidden" name="preview_existing_image_url" value="{{ $isEditMode ? $project->image_url : asset('img/projects/default.png') }}" />

                            <div id="project-validation-summary"
                                class="alert alert-danger d-flex align-items-start gap-2 mb-4 {{ $errors->any() ? '' : 'd-none' }}"
                                role="alert" aria-live="assertive" aria-hidden="{{ $errors->any() ? 'false' : 'true' }}">
                                <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                                <div>Attenzione: alcuni campi obbligatori non sono stati compilati. Controlla i campi evidenziati in rosso.</div>
                            </div>

                            <div class="mb-5">
                                <h5 class="fw-bold text-primary mb-4 pb-2 border-bottom"><i class="bi bi-info-circle-fill me-2"></i>Informazioni Base</h5>
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Titolo del Progetto <span class="text-danger">*</span></label>
                                        <input class="form-control bg-light border-0 py-2" style="border-radius: 0.75rem;" type="text" name="title" value="{{ old('title', $project->title ?? '') }}" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Categoria <span class="text-danger">*</span></label>
                                        <select class="form-select bg-light border-0 py-2" style="border-radius: 0.75rem;" name="category_id" required>
                                            <option value="">Seleziona categoria...</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @if(old('category_id', $project->category_id ?? '') == $category->id) selected @endif>{{ $category->tag }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Associazione <span class="text-danger">*</span></label>
                                        <select class="form-select bg-light border-0 py-2" style="border-radius: 0.75rem;" name="association_id" required>
                                            <option value="">Seleziona associazione...</option>
                                            @foreach ($associations as $association)
                                                <option value="{{ $association->id }}" @if(old('association_id', $project->association_id ?? '') == $association->id) selected @endif>{{ $association->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Immagine Copertina 
                                            @if(!$isEditMode)<span class="text-danger">*</span>@endif
                                        </label>
                                        <input class="form-control bg-light border-0 py-2" style="border-radius: 0.75rem;" type="file" name="image_path" accept="image/*" @if(!$isEditMode) required @endif />
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <h5 class="fw-bold text-primary mb-4 pb-2 border-bottom"><i class="bi bi-calendar2-week-fill me-2"></i>Date e Organizzazione</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Luogo <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 text-warning" style="border-top-left-radius: 0.75rem; border-bottom-left-radius: 0.75rem;"><i class="bi bi-geo-alt-fill"></i></span>
                                            <input class="form-control bg-light border-0 py-2" style="border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.75rem;" type="text" name="location" value="{{ old('location', $project->location ?? '') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Posti Disponibili <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 text-primary" style="border-top-left-radius: 0.75rem; border-bottom-left-radius: 0.75rem;"><i class="bi bi-people-fill"></i></span>
                                            <input class="form-control bg-light border-0 py-2" style="border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.75rem;" type="number" name="requested_people" value="{{ old('requested_people', $project->requested_people ?? '') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Data Inizio <span class="text-danger">*</span></label>
                                        <input class="form-control bg-light border-0 py-2" style="border-radius: 0.75rem;" type="date" name="start_date" value="{{ old('start_date', ($isEditMode && $project->start_date) ? $project->start_date->format('Y-m-d') : '') }}" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Data Fine <span class="text-danger">*</span></label>
                                        <input class="form-control bg-light border-0 py-2" style="border-radius: 0.75rem;" type="date" name="end_date" value="{{ old('end_date', ($isEditMode && $project->end_date) ? $project->end_date->format('Y-m-d') : '') }}" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Scadenza Iscrizioni <span class="text-danger">*</span></label>
                                        <input class="form-control bg-light border-0 py-2" style="border-radius: 0.75rem;" type="date" name="expire_date" value="{{ old('expire_date', ($isEditMode && $project->expire_date) ? $project->expire_date->format('Y-m-d') : '') }}" required />
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <h5 class="fw-bold text-primary mb-4 pb-2 border-bottom"><i class="bi bi-file-earmark-text-fill me-2"></i>Contenuti Testuali</h5>
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Descrizione Breve (Per le Card) <span class="text-danger">*</span></label>
                                        <textarea class="form-control bg-light border-0" style="border-radius: 0.75rem;" name="sum_description" rows="2" required>{{ old('sum_description', $project->sum_description ?? '') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Il Viaggio in Pillole (Completa) <span class="text-danger">*</span></label>
                                        <textarea class="form-control bg-light border-0" style="border-radius: 0.75rem;" name="full_description" rows="5" required>{{ old('full_description', $project->full_description ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Requisiti <span class="text-danger">*</span></label>
                                        <textarea class="form-control bg-light border-0" style="border-radius: 0.75rem;" name="requirements" rows="4" required>{{ old('requirements', $project->requirements ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Condizioni Economiche <span class="text-danger">*</span></label>
                                        <textarea class="form-control bg-light border-0" style="border-radius: 0.75rem;" name="travel_conditions" rows="4" required>{{ old('travel_conditions', $project->travel_conditions ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="border-top pt-4 mt-5 d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-4">
                                
                                <div class="d-flex align-items-center gap-3">
                                    <label class="form-label text-secondary fw-medium mb-0 text-nowrap">Stato del progetto:</label>
                                    <select class="form-select bg-light border-0 fw-semibold text-primary py-2 px-3 shadow-none" style="border-radius: 0.75rem; width: auto; min-width: 180px; cursor: pointer;" name="status" id="status">
                                        @foreach ($allowedStatuses as $statusOption)
                                            <option value="{{ $statusOption }}" @selected($selectedStatus === $statusOption)>
                                                {{ $statusLabels[$statusOption] ?? ucfirst($statusOption) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="project-form-actions d-flex justify-content-start justify-content-xl-end">
                                    <div class="project-form-actions-inner">
                                    
                                        <button type="button" class="btn btn-link text-secondary text-decoration-none fw-medium px-2 py-2 text-nowrap transition-hover js-cancel-process" data-cancel-url="{{ $backUrl }}">
                                            Annulla
                                        </button>

                                        <button type="button" formnovalidate class="btn btn-ae btn-ae-outline-secondary rounded-pill px-4 py-2 text-nowrap fw-semibold" id="preview-project-btn">
                                            <i class="bi bi-eye me-2"></i>Anteprima
                                        </button>

                                        <button type="submit" class="btn btn-ae btn-ae-primary rounded-pill px-4 py-2 shadow-sm text-nowrap fw-bold" id="submit-btn">
                                            <i class="bi bi-check-circle-fill me-2" id="submit-btn-icon"></i><span id="submit-btn-label">{{ $isEditMode ? 'Aggiorna Progetto' : 'Pubblica Progetto' }}</span>
                                        </button>

                                    </div>

                                </div>
                            </div>
                            
                        </form>

                        <div class="modal fade" id="cancelProjectProcessModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                                    <div class="modal-body p-4 p-md-5">
                                        <div class="text-center mb-4">
                                            <i class="bi bi-exclamation-triangle-fill text-warning display-5 d-block mb-3"></i>
                                            <h4 class="fw-bold mb-2">Vuoi abbandonare la pagina?</h4>
                                            <p class="text-secondary mb-0">Le modifiche inserite non sono state salvate. Se esci ora, i dati andranno persi.</p>
                                        </div>

                                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                                            <button type="button" class="btn btn-ae btn-ae-outline-secondary px-4 fw-semibold" data-bs-dismiss="modal">Resta qui</button>
                                            <a href="{{ $backUrl }}" id="confirm-cancel-process-link" class="btn btn-ae btn-ae-danger px-4 fw-semibold">Sì, esci senza salvare</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="confirmCompletionModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                                    <div class="modal-body p-4 p-md-5">
                                        <div class="text-center mb-4">
                                            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 80px; height: 80px;">
                                                <i class="bi bi-archive-fill text-dark display-5"></i>
                                            </div>
                                            <h4 class="fw-bold mb-2">Vuoi archiviare questo progetto?</h4>
                                            <p class="text-secondary mb-0">Modificando lo stato in <strong>Completato</strong>, il progetto verrà chiuso e <strong>non potrà più essere modificato</strong> in futuro. Le candidature rimarranno intatte.</p>
                                        </div>

                                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                                            <button type="button" class="btn btn-ae btn-ae-outline-secondary px-4 fw-semibold" data-bs-dismiss="modal">Annulla</button>
                                            <button type="button" id="confirm-completion-submit" class="btn btn-ae btn-ae-dark px-4 fw-semibold">
                                                <i class="bi bi-archive-fill me-2"></i>Archivia definitivamente
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="projectPreviewModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                                    <div class="modal-header border-0 pb-0 px-4 px-md-5 pt-4">
                                        <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-eye-fill me-2"></i>Anteprima progetto</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                                    </div>
                                    <div class="modal-body px-4 px-md-5 pb-4">
                                        <p class="text-secondary small mb-4">Così è come apparirà la card e il riepilogo nella vetrina pubblica.</p>

                                        <div class="row g-4">
                                            <div class="col-12 col-lg-5">
                                                <article class="card border-0 shadow-sm h-100" style="border-radius: 1.25rem; overflow: hidden; border: 1px solid var(--project-card-border);">
                                                    <img id="preview-image" src="{{ $isEditMode ? $project->image_url : asset('img/projects/default.png') }}"
                                                        alt="Anteprima" class="w-100 object-fit-cover" style="height: 220px;">
                                                    <div class="card-body p-4">
                                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                                            <span id="preview-category" class="badge bg-light text-dark border px-2 py-1">Categoria</span>
                                                            <span id="preview-status" class="badge bg-primary px-2 py-1">Stato</span>
                                                        </div>
                                                        <h5 id="preview-title" class="fw-bold mb-2">Titolo progetto</h5>
                                                        <p id="preview-summary" class="text-secondary mb-0 small">Descrizione breve del progetto...</p>
                                                    </div>
                                                </article>
                                            </div>

                                            <div class="col-12 col-lg-7">
                                                <div class="bg-light border p-4 mb-3" style="border-radius: 1rem;">
                                                    <h6 class="fw-bold text-primary mb-3">Informazioni principali</h6>
                                                    <div class="row g-3">
                                                        <div class="col-12 col-md-6">
                                                            <small class="text-muted d-block">Associazione</small>
                                                            <span id="preview-association" class="fw-semibold">N/D</span>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <small class="text-muted d-block">Luogo</small>
                                                            <span id="preview-location" class="fw-semibold">N/D</span>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <small class="text-muted d-block">Posti disponibili</small>
                                                            <span id="preview-people" class="fw-semibold">N/D</span>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <small class="text-muted d-block">Scadenza iscrizioni</small>
                                                            <span id="preview-expire-date" class="fw-semibold">N/D</span>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <small class="text-muted d-block">Data inizio</small>
                                                            <span id="preview-start-date" class="fw-semibold">N/D</span>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <small class="text-muted d-block">Data fine</small>
                                                            <span id="preview-end-date" class="fw-semibold">N/D</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="bg-white border p-4" style="border-radius: 1rem;">
                                                    <h6 class="fw-bold text-primary mb-2">Descrizione completa (estratto)</h6>
                                                    <p id="preview-description" class="text-secondary mb-0 small" style="white-space: pre-line; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">Contenuto descrizione...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 px-4 px-md-5 pb-4 pt-0">
                                        <button type="button" class="btn btn-ae btn-ae-outline-secondary fw-semibold" data-bs-dismiss="modal">Chiudi anteprima</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('project-form');
            const submitModeInput = document.getElementById('form_submit_mode');
            const completionConfirmedInput = document.getElementById('completion_confirmed');
            const statusSelect = document.getElementById('status');
            const submitButton = document.getElementById('submit-btn');
            const submitButtonLabel = document.getElementById('submit-btn-label');
            const submitButtonIcon = document.getElementById('submit-btn-icon');
            const cancelModalEl = document.getElementById('cancelProjectProcessModal');
            const confirmCancelLink = document.getElementById('confirm-cancel-process-link');
            const completionModalEl = document.getElementById('confirmCompletionModal');
            const confirmCompletionSubmitButton = document.getElementById('confirm-completion-submit');
            const previewButton = document.getElementById('preview-project-btn');
            const validationSummary = document.getElementById('project-validation-summary');
            const serverErrorFields = @json($errors->keys());
            const previewEndpoint = @json(route('project.preview'));
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

            let cancelModalInstance = null;
            if (cancelModalEl && window.bootstrap && bootstrap.Modal) {
                cancelModalInstance = bootstrap.Modal.getOrCreateInstance(cancelModalEl);
            }

            let completionModalInstance = null;
            if (completionModalEl && window.bootstrap && bootstrap.Modal) {
                completionModalInstance = bootstrap.Modal.getOrCreateInstance(completionModalEl);
            }

            const isEditMode = @json($isEditMode);
            const currentProjectStatus = @json($currentProjectStatus);
            const nextAllowedStatus = @json($nextAllowedStatus);
            const shouldOpenCompletionModalOnLoad = @json($openCompletionModal);

            function getPrimaryActionConfig(status) {
                switch (status) {
                    case 'draft':
                        return {
                            label: 'Salva come Bozza',
                            icon: 'bi-floppy',
                            submitMode: 'draft',
                            buttonClass: 'btn-ae-secondary',
                        };
                    case 'completed':
                        return {
                            label: 'Segna come Completato',
                            icon: 'bi-archive-fill',
                            submitMode: 'publish',
                            buttonClass: 'btn-ae-dark',
                        };
                    case 'published':
                    default:
                        const isDraftToPublished = isEditMode && currentProjectStatus === 'draft' && status === 'published';

                        return {
                            label: !isEditMode || isDraftToPublished ? 'Pubblica Progetto' : 'Aggiorna Progetto',
                            icon: 'bi-check-circle-fill',
                            submitMode: 'publish',
                            buttonClass: 'btn-ae-primary',
                        };
                }
            }

            function updatePrimaryActionFromStatus() {
                if (!statusSelect || !submitButton) {
                    return;
                }

                const selectedStatus = statusSelect.value || 'draft';
                const actionConfig = getPrimaryActionConfig(selectedStatus);

                if (submitButtonLabel) {
                    submitButtonLabel.textContent = actionConfig.label;
                }

                if (submitButtonIcon) {
                    submitButtonIcon.className = `bi ${actionConfig.icon} me-2`;
                }

                submitButton.classList.remove('btn-ae-primary', 'btn-ae-secondary', 'btn-ae-danger', 'btn-ae-warning', 'btn-ae-dark');
                submitButton.classList.add(actionConfig.buttonClass);

                if (submitModeInput) {
                    submitModeInput.value = actionConfig.submitMode;
                }
            }

            function hasNonEmptyValue(field) {
                if (!field) {
                    return true;
                }

                if (field.type === 'file') {
                    return field.files && field.files.length > 0;
                }

                const value = field.value;
                if (typeof value === 'string') {
                    return value.trim() !== '';
                }

                return value !== null && value !== '';
            }

            function setValidationSummaryVisible(visible) {
                if (!validationSummary) {
                    return;
                }

                validationSummary.classList.toggle('d-none', !visible);
                validationSummary.setAttribute('aria-hidden', visible ? 'false' : 'true');
            }

            function setFieldInvalidState(field, invalid) {
                if (!field) {
                    return;
                }

                field.classList.toggle('ae-invalid-field', invalid);

                if (invalid) {
                    field.setAttribute('aria-invalid', 'true');
                } else {
                    field.removeAttribute('aria-invalid');
                }

                const inputGroup = field.closest('.input-group');
                if (!inputGroup) {
                    return;
                }

                const addon = inputGroup.querySelector('.input-group-text');
                if (addon) {
                    addon.classList.toggle('ae-invalid-addon', invalid);
                }
            }

            function getFieldByName(name) {
                if (!form || !name) {
                    return null;
                }

                const safeName = String(name).replace(/"/g, '\\"');
                return form.querySelector(`[name="${safeName}"]`);
            }

            function applyServerValidationState() {
                if (!Array.isArray(serverErrorFields) || serverErrorFields.length === 0) {
                    setValidationSummaryVisible(false);
                    return;
                }

                serverErrorFields.forEach(function(name) {
                    const field = getFieldByName(name);
                    setFieldInvalidState(field, true);
                });

                setValidationSummaryVisible(true);
            }

            function validateRequiredFields() {
                if (!form) {
                    return true;
                }

                const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
                let isValid = true;

                requiredFields.forEach(function(field) {
                    const fieldHasValue = hasNonEmptyValue(field);
                    setFieldInvalidState(field, !fieldHasValue);

                    if (!fieldHasValue) {
                        isValid = false;
                    }
                });

                setValidationSummaryVisible(!isValid);
                return isValid;
            }

            function bindInlineValidationReset() {
                if (!form) {
                    return;
                }

                const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
                requiredFields.forEach(function(field) {
                    const eventName = field.tagName === 'SELECT' || field.type === 'file' ? 'change' : 'input';

                    field.addEventListener(eventName, function() {
                        if (hasNonEmptyValue(field)) {
                            setFieldInvalidState(field, false);
                        }

                        const hasAnyInvalid = form.querySelector('.ae-invalid-field') !== null;
                        if (!hasAnyInvalid) {
                            setValidationSummaryVisible(false);
                        }
                    });
                });
            }

            function clearValidationState() {
                if (!form) {
                    return;
                }

                form.querySelectorAll('.ae-invalid-field').forEach(function(field) {
                    setFieldInvalidState(field, false);
                });

                setValidationSummaryVisible(false);
            }

            applyServerValidationState();
            bindInlineValidationReset();

            async function openProjectPreviewInNewTab(event) {
                event.preventDefault();
                event.stopPropagation();

                if (!form || !previewEndpoint) {
                    return;
                }

                clearValidationState();

                const previousSubmitMode = submitModeInput ? submitModeInput.value : 'publish';
                if (submitModeInput) {
                    submitModeInput.value = 'preview';
                }

                if (!csrfToken) {
                    window.alert('Token CSRF non disponibile. Ricarica la pagina e riprova.');
                    if (submitModeInput) {
                        submitModeInput.value = previousSubmitMode;
                    }
                    return;
                }

                const previewTab = window.open('', '_blank');
                if (!previewTab) {
                    window.alert('Impossibile aprire la scheda di anteprima. Controlla il blocco popup del browser.');
                    if (submitModeInput) {
                        submitModeInput.value = previousSubmitMode;
                    }
                    return;
                }

                previewTab.document.open();
                previewTab.document.write('<!doctype html><html lang="it"><head><meta charset="utf-8"><title>Generazione anteprima...</title></head><body style="font-family: sans-serif; padding: 2rem; color: #334155;"><p>Generazione anteprima in corso...</p></body></html>');
                previewTab.document.close();

                const formData = new FormData(form);
                formData.delete('_method');
                formData.set('_token', csrfToken);

                try {
                    const response = await fetch(previewEndpoint, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error('Preview request failed');
                    }

                    const previewHtml = await response.text();
                    previewTab.document.open();
                    previewTab.document.write(previewHtml);
                    previewTab.document.close();
                } catch (error) {
                    console.error('Errore durante la generazione anteprima:', error);
                    previewTab.close();
                    window.alert('Errore durante la generazione dell\'anteprima. Riprova tra poco.');
                } finally {
                    if (submitModeInput) {
                        submitModeInput.value = previousSubmitMode;
                    }
                }
            }

            document.querySelectorAll('.js-cancel-process').forEach(function(button) {
                button.addEventListener('click', function() {
                    const targetUrl = button.getAttribute('data-cancel-url') || '{{ $backUrl }}';

                    if (confirmCancelLink) {
                        confirmCancelLink.setAttribute('href', targetUrl);
                    }

                    if (cancelModalInstance) {
                        cancelModalInstance.show();
                    } else {
                        window.location.href = targetUrl;
                    }
                });
            });

            if (submitButton) {
                submitButton.addEventListener('click', function() {
                    updatePrimaryActionFromStatus();

                    if (completionConfirmedInput) {
                        completionConfirmedInput.value = '0';
                    }
                });
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    if (completionConfirmedInput && statusSelect.value !== 'completed') {
                        completionConfirmedInput.value = '0';
                    }

                    updatePrimaryActionFromStatus();
                });
            }

            if (previewButton) {
                previewButton.addEventListener('click', openProjectPreviewInNewTab);
            }

            if (confirmCompletionSubmitButton) {
                confirmCompletionSubmitButton.addEventListener('click', function() {
                    if (completionConfirmedInput) {
                        completionConfirmedInput.value = '1';
                    }

                    if (completionModalInstance) {
                        completionModalInstance.hide();
                    }

                    if (form) {
                        form.submit();
                    }
                });
            }

            if (form) {
                form.addEventListener('submit', function(event) {
                    const submitMode = submitModeInput ? submitModeInput.value : 'publish';

                    if (submitMode === 'preview') {
                        event.preventDefault();
                        return;
                    }

                    if (submitMode !== 'draft') {
                        const isRequiredFieldsValid = validateRequiredFields();
                        if (!isRequiredFieldsValid) {
                            event.preventDefault();
                            if (validationSummary) {
                                validationSummary.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            return;
                        }
                    }

                    const selectedStatus = statusSelect ? statusSelect.value : 'draft';
                    const isCompletionTransition =
                        isEditMode
                        && currentProjectStatus !== 'completed'
                        && submitMode !== 'draft'
                        && selectedStatus === 'completed'
                        && (!completionConfirmedInput || completionConfirmedInput.value !== '1');

                    if (isCompletionTransition) {
                        event.preventDefault();

                        if (completionModalInstance) {
                            completionModalInstance.show();
                        } else {
                            const fallbackConfirmed = window.confirm('Una volta archiviato, il progetto non sarà più modificabile. Vuoi proseguire?');
                            if (fallbackConfirmed) {
                                if (completionConfirmedInput) {
                                    completionConfirmedInput.value = '1';
                                }
                                form.submit();
                            }
                        }
                    }
                });
            }

            if (
                shouldOpenCompletionModalOnLoad
                && completionModalInstance
                && statusSelect
                && isEditMode
                && currentProjectStatus !== 'completed'
                && nextAllowedStatus === 'completed'
            ) {
                statusSelect.value = 'completed';
                if (submitModeInput) {
                    submitModeInput.value = 'publish';
                }
                if (completionConfirmedInput) {
                    completionConfirmedInput.value = '0';
                }

                completionModalInstance.show();

                const current = new URL(window.location.href);
                current.searchParams.delete('openCompletionModal');
                const cleanQuery = current.searchParams.toString();
                const cleanUrl = current.pathname + (cleanQuery ? `?${cleanQuery}` : '') + current.hash;
                window.history.replaceState({}, document.title, cleanUrl);
            }

            updatePrimaryActionFromStatus();
        });
    </script>
@endsection