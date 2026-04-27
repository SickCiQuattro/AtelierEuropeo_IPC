@extends('layouts.master')

@section('title', 'AE - Invia Candidatura')

@section('body')
    @php
        $categoryBadges = [
            'CES' => 'badge-prog-ces',
            'SG' => 'badge-prog-sg',
            'CF' => 'badge-prog-cf',
        ];
        $tag = $project->category->tag ?? null;
        $categoryBadgeClass = $tag ? ($categoryBadges[$tag] ?? 'badge-prog-ces') : 'badge-prog-ces';
        $categoryModalTag = ($tag && array_key_exists($tag, $categoryBadges)) ? $tag : 'CES';
        $categoryName = $project->category->name ?? 'programma selezionato';
        $programTooltip = 'Info sul programma ' . $categoryName;
    @endphp

    <div class="container-fluid px-3 px-md-4 py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">

                {{-- ── NAVIGAZIONE ──────── --}}
                <div class="d-md-none mb-3">
                    <a href="{{ route('project.show', $project->id) }}"
                        class="btn btn-ae btn-ae-light border shadow-sm rounded-pill px-3 py-2 text-secondary fw-semibold transition-hover text-decoration-none">
                        <i class="bi bi-arrow-left me-2"></i>Indietro
                    </a>
                </div>

                <div class="d-none d-md-block mb-4">
                    <x-breadcrumb>
                        <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Progetti Digitali</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('project.show', $project->id) }}">{{ Str::limit($project->title, 35) }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Nuova Candidatura</li>
                    </x-breadcrumb>
                </div>

                {{-- ── INTESTAZIONE ──────────────────────────────────── --}}
                <div class="mb-4 text-center">
                    <h1 class="display-6 fw-bold text-dark mb-2">Invia la tua candidatura</h1>
                    <p class="text-muted lead mb-0 fs-6">
                        Compila il modulo per candidarti al progetto <strong
                            class="text-primary">{{ $project->title }}</strong>.
                    </p>
                </div>

                {{-- ── RIEPILOGO PROGETTO ──────────────────────────── --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                    <div class="card-body p-4 d-flex gap-3 align-items-start">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:48px; height:48px;">
                            <i class="bi bi-folder2-open fs-4"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h5 class="fw-bold mb-2">{{ $project->title }}</h5>
                            <p class="text-muted small mb-3" style="line-height: 1.6;">
                                {{ Str::limit($project->sum_description, 180) }}
                            </p>

                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                @if($tag)
                                    <span class="d-inline-block position-relative z-3" tabindex="0" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="{{ $programTooltip }}">
                                        <button type="button" class="{{ $categoryBadgeClass }} border-0 shadow-sm px-3 py-1"
                                            data-bs-toggle="modal" data-bs-target="#infoModal-{{ $categoryModalTag }}"
                                            style="font-size: 0.9rem;">
                                            {{ $tag }} <i class="bi bi-info-circle ms-1"></i>
                                        </button>
                                    </span>
                                @endif
                                @if($project->expire_date)
                                    <span class="badge rounded-pill bg-light border px-3 py-2 shadow-sm text-muted"
                                        style="font-size:.82rem;">
                                        <i class="bi bi-calendar-event me-1"></i>Scadenza:
                                        {{ $project->expire_date->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── FORM ─────────────────────────────────────────── --}}
                <form action="{{ route('applications.store', $project->id) }}" method="POST" enctype="multipart/form-data"
                    x-data="applicationForm()" @submit="onSubmit($event)" novalidate>
                    @csrf

                    <p class="text-muted small mb-3">
                        I campi contrassegnati con <span class="text-danger fw-bold">*</span> sono obbligatori.
                    </p>

                    {{-- Sezione 1: I tuoi dati --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-light border-bottom py-3 px-4 px-md-5">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-person-circle me-2 text-primary"></i>Le tue informazioni
                            </h5>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label
                                        class="form-label fw-semibold text-muted small text-uppercase tracking-wide mb-1">
                                        Nome completo
                                    </label>
                                    <div class="text-dark fw-medium fs-6">{{ Auth::user()->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label
                                        class="form-label fw-semibold text-muted small text-uppercase tracking-wide mb-1">
                                        Email
                                    </label>
                                    <div class="text-dark fw-medium fs-6">{{ Auth::user()->email }}</div>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label for="phone" class="form-label fw-semibold text-dark">
                                    Numero di telefono <span class="text-danger" aria-hidden="true">*</span>
                                </label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                    placeholder="es: +39 333 123 4567"
                                    class="form-control bg-light py-2 px-3 border-0 @error('phone') is-invalid @enderror"
                                    style="border-radius: 0.75rem;" autocomplete="tel" required>
                                <div class="form-text small">Ti contatteremo a questo numero in caso di esito positivo.
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Sezione 2: Curriculum --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-light border-bottom py-3 px-4 px-md-5">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-file-earmark-person me-2 text-primary"></i>Curriculum Vitae <span
                                    class="text-danger" aria-hidden="true">*</span>
                            </h5>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <p class="text-muted small mb-4" style="line-height: 1.6;">
                                Allega il tuo CV, portfolio o lettera di motivazione in formato <strong>PDF</strong>
                                (dimensione massima 5&nbsp;MB).
                            </p>

                            <div class="upload-zone rounded-4 text-center p-4 p-md-5 mb-2 @error('document') upload-zone--error @enderror"
                                :class="{ 'upload-zone--has-file': fileName, 'upload-zone--error': hasError }"
                                @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                                @drop.prevent="onDrop($event)" :style="dragging ? 'background: rgba(0,29,61,.06)' : ''">
                                <label for="document" class="d-block w-100 h-100 mb-0" style="cursor:pointer">
                                    <template x-if="!fileName">
                                        <div>
                                            <div class="bg-white rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center mb-3"
                                                style="width: 64px; height: 64px;">
                                                <i class="bi bi-cloud-upload fs-3 text-primary"></i>
                                            </div>
                                            <p class="fw-bold mb-1 text-dark">Trascina qui il tuo file</p>
                                            <p class="text-muted small mb-0">oppure <span
                                                    class="text-primary text-decoration-underline">sfoglia</span> per
                                                caricarlo</p>
                                        </div>
                                    </template>
                                    <template x-if="fileName">
                                        <div class="d-flex align-items-center justify-content-center gap-3">
                                            <i class="bi bi-file-earmark-pdf-fill text-danger"
                                                style="font-size: 2.5rem;"></i>
                                            <div class="text-start">
                                                <p class="fw-bold mb-0 text-dark" x-text="fileName"></p>
                                                <p class="text-muted small mb-0" x-text="fileSize"></p>
                                            </div>
                                            <button type="button"
                                                class="btn btn-light rounded-circle ms-3 shadow-sm d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width: 40px; height: 40px;"
                                                @click.prevent="clearFile()" aria-label="Rimuovi file">
                                                <i class="bi bi-trash-fill text-danger"></i>
                                            </button>
                                        </div>
                                    </template>
                                </label>
                                <input type="file" id="document" name="document" accept=".pdf"
                                    class="d-none @error('document') is-invalid @enderror" @change="onFileChange($event)"
                                    required>
                            </div>

                            <div class="invalid-feedback d-block fw-medium mt-2" x-show="hasError" x-text="errorMsg" x-cloak>
                            </div>
                            @error('document')
                                <div class="invalid-feedback d-block fw-medium mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Nota informativa (Coerente, Moderna e Riallineata) --}}
                    <div class="card border-0 bg-light rounded-4 mb-4">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-info-circle fs-4 text-primary"></i>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark">Prima di procedere</h5>
                            </div>

                            <ul class="list-unstyled mb-0 small text-muted d-flex flex-column gap-3"
                                style="line-height: 1.7;">
                                <li class="d-flex align-items-start gap-2 fs-6">
                                    <i class="bi bi-exclamation-circle text-primary flex-shrink-0"></i>
                                    <p class="flex-grow-1 text-muted opacity-75 mb-0">Una volta inviata, la candidatura
                                        <strong>non potrà essere modificata</strong>. Assicurati che i dati e il CV siano
                                        corretti.
                                    </p>
                                </li>
                                <li class="d-flex align-items-start gap-2 fs-6">
                                    <i class="bi bi-envelope-paper text-primary flex-shrink-0"></i>
                                    <p class="flex-grow-1 text-muted opacity-75 mb-0">Riceverai una comunicazione via email
                                        o telefono non appena l'organizzazione avrà valutato il tuo profilo.</p>
                                </li>
                                <li class="d-flex align-items-start gap-2 fs-6">
                                    <i class="bi bi-shield-lock text-primary flex-shrink-0"></i>
                                    <p class="flex-grow-1 text-muted opacity-75 mb-0">Il documento allegato verrà trattato
                                        in modo confidenziale e utilizzato esclusivamente per questo processo di selezione.
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Bottoni --}}
                    <div
                        class="d-flex flex-column flex-sm-row justify-content-center justify-content-sm-between align-items-center gap-3 pt-3">
                        <a href="{{ route('project.show', $project->id) }}"
                            class="btn btn-link text-secondary text-decoration-none fw-medium w-100 w-sm-auto order-2 order-sm-1">
                            Annulla
                        </a>
                        <button type="submit"
                            class="btn btn-ae btn-ae-primary rounded-pill px-5 py-2 fw-bold shadow-sm w-100 w-sm-auto order-1 order-sm-2"
                            :disabled="submitting">
                            <span x-show="!submitting"><i class="bi bi-send me-2"></i>Invia candidatura</span>
                            <span x-show="submitting" x-cloak>
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>Invio in corso…
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .upload-zone {
            border: 2px dashed #cbd5e1;
            background: #f8fafc;
            transition: all .2s ease;
            min-height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .upload-zone:hover,
        .upload-zone:focus-within {
            border-color: var(--bs-primary);
            background: #f1f5f9;
        }

        .upload-zone--has-file {
            border-color: var(--bs-success);
            border-style: solid;
            background: #f0fdf4;
        }

        .upload-zone--error {
            border-color: var(--bs-danger) !important;
            background: #fef2f2 !important;
        }

        .tracking-wide {
            letter-spacing: 0.05em;
        }

        @media (min-width: 576px) {
            .w-sm-auto {
                width: auto !important;
            }
        }
    </style>

    <script>
        function applicationForm() {
            return {
                fileName: null, fileSize: null, hasError: false, errorMsg: '', dragging: false, submitting: false,
                onSubmit(evt) {
                    this.submitting = false;

                    if (this.hasError) {
                        evt.preventDefault();
                        return;
                    }

                    this.submitting = true;
                },
                onFileChange(evt) { this.handleFile(evt.target.files[0]); },
                onDrop(evt) {
                    this.dragging = false;
                    const file = evt.dataTransfer.files[0];
                    if (file) { const dt = new DataTransfer(); dt.items.add(file); document.getElementById('document').files = dt.files; this.handleFile(file); }
                },
                handleFile(file) {
                    this.hasError = false; this.errorMsg = ''; this.fileName = null; this.fileSize = null;
                    if (!file) return;
                    if (file.type !== 'application/pdf') { this.hasError = true; this.errorMsg = 'Sono accettati solo file PDF.'; document.getElementById('document').value = ''; return; }
                    const sizeMB = file.size / 1024 / 1024;
                    if (sizeMB > 5) { this.hasError = true; this.errorMsg = 'Il file supera la dimensione massima consentita (5 MB).'; document.getElementById('document').value = ''; return; }
                    this.fileName = file.name;
                    this.fileSize = sizeMB < 1 ? `${Math.round(file.size / 1024)} KB` : `${sizeMB.toFixed(1)} MB`;
                },
                clearFile() { this.fileName = null; this.fileSize = null; this.hasError = false; this.errorMsg = ''; document.getElementById('document').value = ''; }
            };
        }
    </script>
@endsection