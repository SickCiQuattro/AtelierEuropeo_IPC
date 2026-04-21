@extends('layouts.master')

@section('title', 'AE - Le Mie Candidature')

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

            {{-- ── HEADER ──────────────────────────────────────────── --}}
            <div class="mb-4">
                <h1 class="mb-1">Le Mie Candidature</h1>
                <p class="text-muted mb-0">
                    Tieni traccia di tutte le candidature inviate e del loro stato.
                </p>
            </div>

            @if($applications->count() > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach($applications as $application)
                        @php
                            $statusMap = [
                                'pending'  => ['bg-warning text-dark', 'bi-clock',        'In Attesa'],
                                'approved' => ['bg-success',           'bi-check-circle', 'Approvata'],
                                'rejected' => ['bg-danger',            'bi-x-circle',     'Rifiutata'],
                            ];
                            [$badgeClass, $icon, $label] = $statusMap[$application->status]
                                ?? ['bg-secondary', 'bi-question-circle', ucfirst($application->status)];
                        @endphp

                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">

                                {{-- Titolo + badge stato --}}
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <h5 class="fw-semibold mb-1">
                                            {{ $application->project->title }}
                                        </h5>
                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Inviata il {{ $application->created_at->format('d/m/Y \a\l\l\e H:i') }}
                                        </p>
                                    </div>
                                    <span class="badge {{ $badgeClass }} px-3 py-2 flex-shrink-0">
                                        <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                                    </span>
                                </div>

                                {{-- Descrizione progetto --}}
                                <p class="text-muted small mb-3">
                                    {{ Str::limit($application->project->description, 180) }}
                                </p>

                                {{-- Messaggio admin (se presente) --}}
                                @if($application->admin_message)
                                    <div class="p-3 rounded-3 mb-3"
                                         style="background:#f8fafc;border-left:4px solid var(--bs-primary)">
                                        <p class="small fw-semibold mb-1 text-primary">
                                            <i class="bi bi-chat-left-text me-1"></i>Messaggio dall'organizzazione:
                                        </p>
                                        <p class="small text-muted mb-0">
                                            {{ $application->admin_message }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Azioni --}}
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 border-top">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('applications.show', $application) }}"
                                           class="btn btn-ae btn-ae-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>Dettagli candidatura
                                        </a>
                                        <a href="{{ route('project.show', $application->project->id) }}"
                                           class="btn btn-ae btn-ae-outline-secondary btn-sm">
                                            <i class="bi bi-folder2-open me-1"></i>Vedi progetto
                                        </a>
                                    </div>

                                    {{-- Ritira candidatura (solo se in attesa) — P. 61 --}}
                                    @if($application->status === 'pending')
                                        <button type="button"
                                                class="btn btn-ae btn-ae-outline-danger btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#withdrawModal{{ $application->id }}">
                                            <i class="bi bi-x-lg me-1"></i>Ritira
                                        </button>
                                    @elseif($application->status === 'approved')
                                        <span class="text-success small fw-medium">
                                            <i class="bi bi-trophy me-1"></i>Congratulazioni! Sarai contattato presto.
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Modale conferma ritiro --}}
                        @if($application->status === 'pending')
                            <div class="modal fade" id="withdrawModal{{ $application->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('applications.withdraw', $application) }}"
                                              method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header border-0 pb-0">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                                                         style="width:40px;height:40px">
                                                        <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                                    </div>
                                                    <h5 class="modal-title mb-0">Ritira Candidatura</h5>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body pt-3">
                                                <p class="mb-1">
                                                    Stai per ritirare la candidatura per
                                                    <strong>{{ $application->project->title }}</strong>.
                                                </p>
                                                <p class="text-muted small mb-0">
                                                    Questa azione è irreversibile. Potrai candidarti nuovamente
                                                    se il progetto è ancora aperto.
                                                </p>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-ae btn-ae-secondary"
                                                        data-bs-dismiss="modal">Annulla</button>
                                                <button type="submit" class="btn btn-ae btn-ae-danger">
                                                    <i class="bi bi-x-lg me-1"></i>Ritira candidatura
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Paginazione --}}
                <div class="mt-4">
                    {{ $applications->links() }}
                </div>

            @else
                {{-- Stato vuoto --}}
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text display-4 text-muted d-block mb-3"></i>
                    <h4 class="fw-medium mb-2">Nessuna candidatura inviata</h4>
                    <p class="text-muted mb-4">
                        Non hai ancora inviato nessuna candidatura.<br>
                        Esplora i progetti disponibili e candidati!
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