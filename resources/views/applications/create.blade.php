@extends('layouts.master')

@section('title', 'AE - Candidatura')

@section('breadcrumb')
<div class="bg-light py-2">
    <div class="container">
        <x-breadcrumb>
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('project.index') }}" class="text-decoration-none">Progetti Disponibili</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('project.show', $project->id) }}"
                   class="text-decoration-none">{{ Str::limit($project->title, 35) }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Candidatura</li>
        </x-breadcrumb>
    </div>
</div>
@endsection

@section('body')
@php
    $categoryBadges  = ['CES' => 'badge-prog-ces', 'SG' => 'badge-prog-sg', 'CF' => 'badge-prog-cf'];
    $tag             = $project->category->tag ?? null;
    $catBadgeClass   = $tag ? ($categoryBadges[$tag] ?? '') : '';
    $catModalTag     = ($tag && array_key_exists($tag, $categoryBadges)) ? $tag : 'CES';
@endphp

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- ── INTESTAZIONE ──────────────────────────────────── --}}
            <div class="mb-4 text-center">
                <h1 class="mb-1">Invia la tua candidatura</h1>
                <p class="text-muted lead mb-0">
                    Compila il modulo per candidarti al progetto
                    <strong class="text-primary">{{ $project->title }}</strong>.
                </p>
            </div>

            {{-- ── RIEPILOGO PROGETTO ──────────────────────────── --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body d-flex gap-3 align-items-start">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:rgba(0,29,61,.08)">
                        <i class="bi bi-folder2-open text-primary fs-4"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <h5 class="mb-1">{{ $project->title }}</h5>
                        <p class="text-muted small mb-2">{{ Str::limit($project->sum_description, 200) }}</p>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                           @if($tag && $catBadgeClass)
                            <span data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Info sul programma {{ $project->category->name ?? 'programma selezionato' }}">

                                <button type="button"
                                    class="{{ $catBadgeClass }} border-0 shadow-sm px-3 py-1"
                                    style="font-size: 0.9rem; cursor: default;">
                                    {{ $tag }}
                                    <i class="bi bi-info-circle ms-1"></i>
                                </button>

                            </span>
                            @endif
                            @if($project->expire_date)
                                <span class="badge rounded-pill bg-light border px-3 py-2 shadow-sm text-muted" style="font-size:.82rem;">
                                    <i class="bi bi-calendar-event me-1"></i>Scadenza: {{ $project->expire_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── FORM ─────────────────────────────────────────── --}}
            <form action="{{ route('applications.store', $project->id) }}" method="POST"
                  enctype="multipart/form-data"
                  x-data="applicationForm()"
                @submit="onSubmit($event)"
                  novalidate>
                @csrf

                <p class="text-muted small mb-4">
                    I campi contrassegnati con <span class="text-danger fw-bold">*</span> sono obbligatori.
                </p>

                {{-- Sezione 1: I tuoi dati --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-light border-bottom py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-person-circle me-2 text-primary"></i>Le tue informazioni
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted small text-uppercase" style="letter-spacing:.04em">
                                    Nome completo
                                </label>
                                <p class="form-control-plaintext fw-semibold mb-0">{{ Auth::user()->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted small text-uppercase" style="letter-spacing:.04em">
                                    Email
                                </label>
                                <p class="form-control-plaintext fw-semibold mb-0">{{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="phone" class="form-label fw-medium">
                                Numero di telefono <span class="text-danger" aria-hidden="true">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone"
                                   value="{{ old('phone') }}"
                                   placeholder="es: +39 333 123 4567"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   autocomplete="tel" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Sezione 2: Curriculum --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-light border-bottom py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-file-earmark-person me-2 text-primary"></i>
                            Curriculum Vitae <span class="text-danger" aria-hidden="true">*</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            Allega il tuo CV, portfolio o lettera di motivazione in formato <strong>PDF</strong> (max 5&nbsp;MB).
                        </p>

                        <div class="upload-zone rounded-3 text-center p-4 mb-2"
                             :class="{ 'upload-zone--has-file': fileName, 'upload-zone--error': hasError }"
                             @dragover.prevent="dragging = true"
                             @dragleave.prevent="dragging = false"
                             @drop.prevent="onDrop($event)"
                             :style="dragging ? 'background: rgba(0,29,61,.06)' : ''">
                            <label for="document" class="d-block w-100 h-100" style="cursor:pointer">
                                <template x-if="!fileName">
                                    <div>
                                        <i class="bi bi-cloud-upload display-5 text-muted mb-2 d-block"></i>
                                        <p class="fw-semibold mb-1 text-primary">Trascina qui il tuo file</p>
                                        <p class="text-muted small mb-0">oppure <span class="text-primary text-decoration-underline">sfoglia</span></p>
                                    </div>
                                </template>
                                <template x-if="fileName">
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-2"></i>
                                        <div class="text-start">
                                            <p class="fw-semibold mb-0 text-primary" x-text="fileName"></p>
                                            <p class="text-muted small mb-0" x-text="fileSize"></p>
                                        </div>
                                        <button type="button" class="btn btn-link text-danger p-0 ms-2"
                                                @click.prevent="clearFile()" aria-label="Rimuovi file">
                                            <i class="bi bi-x-circle-fill fs-5"></i>
                                        </button>
                                    </div>
                                </template>
                            </label>
                            <input type="file" id="document" name="document" accept=".pdf"
                                   class="d-none @error('document') is-invalid @enderror"
                                   @change="onFileChange($event)" required>
                        </div>

                        <p class="text-danger small mb-0" x-show="errorMsg" x-text="errorMsg" x-cloak></p>
                        @error('document')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Nota informativa --}}
                <div class="d-flex gap-3 align-items-start rounded-3 p-3 mb-4"
                     style="background:rgba(253,197,0,.12);border-left:4px solid var(--bs-warning)">
                    <i class="bi bi-info-circle-fill text-warning fs-4 flex-shrink-0 mt-1"></i>
                    <div>
                        <h6 class="fw-semibold mb-1" style="color:var(--bs-primary)">Prima di inviare</h6>
                        <ul class="mb-0 small text-muted ps-3">
                            <li>Una volta inviata, la candidatura <strong>non potrà essere modificata</strong>.</li>
                            <li>Riceverai una comunicazione via email o telefono per l'esito della valutazione.</li>
                            <li>Il documento allegato verrà utilizzato esclusivamente per valutare la tua candidatura.</li>
                        </ul>
                    </div>
                </div>

                {{-- Bottoni --}}
                <div class="d-flex justify-content-between align-items-center gap-2 pt-2">
                    <a href="{{ route('project.show', $project->id) }}" class="btn btn-ae btn-ae-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Annulla
                    </a>
                        <button type="submit" class="btn btn-ae btn-ae-primary px-4"
                            :disabled="submitting">
                        <span x-show="!submitting"><i class="bi bi-send me-1"></i>Invia candidatura</span>
                        <span x-show="submitting" x-cloak>
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>Invio in corso…
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<x-category-info-modals />

<style>
    [x-cloak] { display: none !important; }
    .upload-zone {
        border: 2px dashed #cbd5e1; background: #f8fafc;
        transition: border-color .2s, background .2s;
        min-height: 120px; display: flex; align-items: center; justify-content: center;
    }
    .upload-zone:hover, .upload-zone:focus-within { border-color: var(--bs-primary); background: rgba(0,29,61,.04); }
    .upload-zone--has-file { border-color: var(--bs-success); background: rgba(16,185,129,.05); }
    .upload-zone--error { border-color: var(--bs-danger); background: rgba(239,68,68,.04); }
</style>

<script>
function applicationForm() {
    return {
        fileName: null, fileSize: null, hasError: false, errorMsg: '', dragging: false, submitting: false,
        onSubmit(evt) {
            this.submitting = false;

            const documentInput = document.getElementById('document');
            const selectedFile = documentInput && documentInput.files ? documentInput.files[0] : null;

            if (!selectedFile) {
                evt.preventDefault();
                this.hasError = true;
                this.errorMsg = 'Allega un file PDF prima di inviare la candidatura.';
                if (documentInput) {
                    documentInput.focus();
                }
                return;
            }

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