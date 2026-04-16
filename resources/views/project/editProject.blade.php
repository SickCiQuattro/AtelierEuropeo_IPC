@extends('layouts.master')

@section('title', isset($project) ? 'AE - Modifica Progetto' : 'AE - Nuovo Progetto')

@section('active_progetti', 'active')

@section('body')
    @php
        $isEditMode = isset($project);
        $currentProjectStatus = $isEditMode ? ($project->status ?? 'draft') : 'draft';
        $openCompletionModal = request()->boolean('openCompletionModal');
        
        $currentUrl = url()->current();
        $previousUrl = url()->previous();

        $backUrl = ($previousUrl !== $currentUrl && !str_contains($previousUrl, 'edit')) 
                    ? $previousUrl 
                    : route('project.index');
    @endphp

    <div class="container-fluid px-3 px-md-4 py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Progetti</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $isEditMode ? 'Modifica Progetto' : 'Nuovo Progetto' }}</li>
                </x-breadcrumb>

                <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h1 class="h3 fw-bold text-primary mb-1">
                            <i class="bi bi-{{ $isEditMode ? 'pencil-fill' : 'plus-circle-fill' }} me-2"></i>
                            {{ $isEditMode ? 'Modifica Progetto' : 'Nuovo Progetto' }}
                        </h1>
                        <p class="text-secondary mb-0 small">Compila i dettagli per la pubblicazione nella vetrina.</p>
                    </div>
                    <button type="button" class="btn btn-ae btn-ae-light border shadow-sm rounded-pill px-3 py-2 text-secondary fw-semibold transition-hover js-cancel-process"
                        data-cancel-url="{{ $backUrl }}">
                        <i class="bi bi-arrow-left me-2"></i>Indietro
                    </button>
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
                                    <label class="form-label text-secondary fw-medium mb-0 text-nowrap">Stato:</label>
                                    <select class="form-select bg-light border-0 fw-semibold text-primary py-2 px-3 shadow-none" style="border-radius: 0.75rem; width: auto; min-width: 180px; cursor: pointer;" name="status" id="status">
                                        <option value="draft" @if(old('status', $project->status ?? 'draft') == 'draft') selected @endif>Bozza (Invisibile)</option>
                                        <option value="published" @if(old('status', $project->status ?? '') == 'published') selected @endif>Pubblicato</option>
                                        @if($isEditMode) 
                                            <option value="completed" @if(old('status', $project->status ?? '') == 'completed') selected @endif>Completato</option> 
                                        @endif
                                    </select>
                                </div>
                                
                                <div class="d-flex flex-wrap align-items-center justify-content-start justify-content-xl-end gap-2 gap-sm-3">
                                    
                                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-medium px-2 py-2 text-nowrap transition-hover js-cancel-process" data-cancel-url="{{ $backUrl }}">
                                        Annulla
                                    </button>

                                    <button type="button" class="btn btn-ae btn-ae-outline-secondary rounded-pill px-4 py-2 text-nowrap fw-semibold" id="preview-project-btn" data-bs-toggle="modal" data-bs-target="#projectPreviewModal">
                                        <i class="bi bi-eye me-2"></i>Anteprima
                                    </button>

                                    <button type="button" class="btn btn-ae btn-ae-outline-primary rounded-pill px-4 py-2 text-nowrap fw-semibold" id="save-draft-btn">
                                        <i class="bi bi-floppy me-2"></i>Bozza
                                    </button>

                                    <button type="submit" class="btn btn-ae btn-ae-primary rounded-pill px-4 py-2 shadow-sm text-nowrap fw-bold" id="submit-btn">
                                        <i class="bi bi-check-circle-fill me-2"></i>{{ $isEditMode ? 'Aggiorna Progetto' : 'Pubblica' }}
                                    </button>

                                </div>
                            </div>
                            
                        </form>

                        <div class="modal fade" id="cancelProjectProcessModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                                    <div class="modal-body p-4 p-md-5">
                                        <div class="text-center mb-4">
                                            <i class="bi bi-exclamation-triangle-fill text-warning display-5 d-block mb-3"></i>
                                            <h4 class="fw-bold mb-2">Annullare il processo?</h4>
                                            <p class="text-secondary mb-0">Se esci ora, la compilazione del progetto verrà annullata e le modifiche non salvate andranno perse.</p>
                                        </div>

                                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">
                                            <button type="button" class="btn btn-ae btn-ae-outline-secondary" data-bs-dismiss="modal">Continua modifica</button>
                                            <a href="{{ $backUrl }}" id="confirm-cancel-process-link" class="btn btn-ae btn-ae-danger">Conferma annullamento</a>
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
                                            <i class="bi bi-check-circle-fill text-warning display-5 d-block mb-3"></i>
                                            <h4 class="fw-bold mb-2">Confermare il completamento?</h4>
                                            <p class="text-secondary mb-0">Una volta completato, il progetto non sarà più assolutamente modificabile. Vuoi proseguire?</p>
                                        </div>

                                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">
                                            <button type="button" class="btn btn-ae btn-ae-outline-secondary" data-bs-dismiss="modal">Annulla</button>
                                            <button type="button" id="confirm-completion-submit" class="btn btn-ae btn-ae-warning">Conferma completamento</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="projectPreviewModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                                    <div class="modal-header border-0 pb-0 px-4 px-md-5 pt-4">
                                        <h4 class="fw-bold mb-0">Anteprima progetto</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                                    </div>
                                    <div class="modal-body px-4 px-md-5 pb-4">
                                        <p class="text-secondary small mb-4">Questa anteprima mostra come potrebbe apparire il progetto una volta salvato e pubblicato.</p>

                                        <div class="row g-4">
                                            <div class="col-12 col-lg-5">
                                                <article class="card border-0 shadow-sm h-100" style="border-radius: 1rem; overflow: hidden;">
                                                    <img id="preview-image" src="{{ $isEditMode ? $project->image_url : asset('img/projects/default.png') }}"
                                                        alt="Anteprima immagine progetto" class="w-100 object-fit-cover" style="height: 220px;">
                                                    <div class="card-body p-4">
                                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                                            <span id="preview-category" class="badge bg-light text-dark border">Categoria</span>
                                                            <span id="preview-status" class="badge bg-primary">Stato</span>
                                                        </div>
                                                        <h5 id="preview-title" class="fw-bold mb-2">Titolo progetto</h5>
                                                        <p id="preview-summary" class="text-secondary mb-0">Descrizione breve del progetto...</p>
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
                                                    <h6 class="fw-bold text-primary mb-2">Estratto descrizione completa</h6>
                                                    <p id="preview-description" class="text-secondary mb-0" style="white-space: pre-line;">Contenuto descrizione...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 px-4 px-md-5 pb-4 pt-0">
                                        <button type="button" class="btn btn-ae btn-ae-outline-secondary" data-bs-dismiss="modal">Chiudi anteprima</button>
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
            const saveDraftButton = document.getElementById('save-draft-btn');
            const submitButton = document.getElementById('submit-btn');
            const cancelModalEl = document.getElementById('cancelProjectProcessModal');
            const confirmCancelLink = document.getElementById('confirm-cancel-process-link');
            const completionModalEl = document.getElementById('confirmCompletionModal');
            const confirmCompletionSubmitButton = document.getElementById('confirm-completion-submit');
            const previewButton = document.getElementById('preview-project-btn');
            const previewModalEl = document.getElementById('projectPreviewModal');
            const previewImage = document.getElementById('preview-image');
            const previewCategory = document.getElementById('preview-category');
            const previewStatus = document.getElementById('preview-status');
            const previewTitle = document.getElementById('preview-title');
            const previewSummary = document.getElementById('preview-summary');
            const previewAssociation = document.getElementById('preview-association');
            const previewLocation = document.getElementById('preview-location');
            const previewPeople = document.getElementById('preview-people');
            const previewExpireDate = document.getElementById('preview-expire-date');
            const previewStartDate = document.getElementById('preview-start-date');
            const previewEndDate = document.getElementById('preview-end-date');
            const previewDescription = document.getElementById('preview-description');
            const fallbackPreviewImage = @json($isEditMode ? $project->image_url : asset('img/projects/default.png'));

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
            const shouldOpenCompletionModalOnLoad = @json($openCompletionModal);

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
                    if (submitModeInput) {
                        submitModeInput.value = 'publish';
                    }

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
                });
            }

            function getInputValue(name, fallback = 'N/D') {
                if (!form) {
                    return fallback;
                }

                const field = form.querySelector(`[name="${name}"]`);
                if (!field) {
                    return fallback;
                }

                const value = (field.value || '').trim();
                return value !== '' ? value : fallback;
            }

            function getSelectedText(name, fallback = 'N/D') {
                if (!form) {
                    return fallback;
                }

                const select = form.querySelector(`[name="${name}"]`);
                if (!select) {
                    return fallback;
                }

                const selectedOption = select.options[select.selectedIndex];
                if (!selectedOption) {
                    return fallback;
                }

                const text = (selectedOption.text || '').trim();
                const invalidPlaceholder = text === '' || text.startsWith('Seleziona');

                return invalidPlaceholder ? fallback : text;
            }

            function formatDate(value) {
                if (!value || value === 'N/D') {
                    return 'N/D';
                }

                const parsedDate = new Date(`${value}T00:00:00`);
                if (Number.isNaN(parsedDate.getTime())) {
                    return 'N/D';
                }

                return new Intl.DateTimeFormat('it-IT', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                }).format(parsedDate);
            }

            function updateProjectPreviewImage() {
                if (!form || !previewImage) {
                    return;
                }

                const imageInput = form.querySelector('input[name="image_path"]');
                if (!imageInput || !imageInput.files || imageInput.files.length === 0) {
                    previewImage.setAttribute('src', fallbackPreviewImage);
                    return;
                }

                const file = imageInput.files[0];
                const reader = new FileReader();
                reader.onload = function(event) {
                    if (event.target && event.target.result) {
                        previewImage.setAttribute('src', event.target.result);
                    }
                };
                reader.readAsDataURL(file);
            }

            function updateProjectPreview() {
                if (!previewTitle) {
                    return;
                }

                const title = getInputValue('title', 'Titolo progetto');
                const summary = getInputValue('sum_description', 'Descrizione breve del progetto...');
                const description = getInputValue('full_description', 'Contenuto descrizione...');
                const location = getInputValue('location');
                const people = getInputValue('requested_people');
                const category = getSelectedText('category_id', 'Categoria');
                const association = getSelectedText('association_id');
                const status = getSelectedText('status', 'Bozza');

                previewTitle.textContent = title;
                previewSummary.textContent = summary;
                previewDescription.textContent = description;
                previewLocation.textContent = location;
                previewPeople.textContent = people;
                previewCategory.textContent = category;
                previewAssociation.textContent = association;
                previewStatus.textContent = status;

                previewStartDate.textContent = formatDate(getInputValue('start_date', 'N/D'));
                previewEndDate.textContent = formatDate(getInputValue('end_date', 'N/D'));
                previewExpireDate.textContent = formatDate(getInputValue('expire_date', 'N/D'));

                updateProjectPreviewImage();
            }

            if (previewButton) {
                previewButton.addEventListener('click', updateProjectPreview);
            }

            if (previewModalEl) {
                previewModalEl.addEventListener('show.bs.modal', updateProjectPreview);
            }

            if (saveDraftButton) {
                saveDraftButton.addEventListener('click', function() {
                    if (submitModeInput) {
                        submitModeInput.value = 'draft';
                    }

                    if (completionConfirmedInput) {
                        completionConfirmedInput.value = '0';
                    }

                    if (statusSelect) {
                        statusSelect.value = 'draft';
                    }

                    if (form) {
                        form.submit();
                    }
                });
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
                            const fallbackConfirmed = window.confirm('Una volta completato, il progetto non sarà più modificabile. Vuoi proseguire?');
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
        });
    </script>
@endsection