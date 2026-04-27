@extends('layouts.master')

@section('title', 'AE - Le Mie Candidature')

@section('body')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="mb-5 text-center">
                    <h1 class="mb-2 fw-bold text-dark">Le Mie Candidature</h1>
                    <p class="text-muted mb-0 lead" style="font-size: 1.1rem;">
                        Tieni traccia di tutte le candidature inviate e del loro stato.
                    </p>
                </div>

                @if($applications->count() > 0)
                    <div class="d-flex flex-column gap-4">
                        @foreach($applications as $application)
                            @php
                                $statusMap = [
                                    'pending' => ['label' => 'In Attesa', 'icon' => 'bi-hourglass-split', 'class' => 'admin-kpi-icon-pending'],
                                    'approved' => ['label' => 'Approvata', 'icon' => 'bi-check-circle', 'class' => 'text-success'],
                                    'rejected' => ['label' => 'Rifiutata', 'icon' => 'bi-x-circle', 'class' => 'text-danger'],
                                ];

                                $statusStr = strtolower((string) $application->status);
                                $statusConfig = $statusMap[$statusStr] ?? ['label' => ucfirst($statusStr), 'icon' => 'bi-question-circle', 'class' => 'text-secondary'];
                            @endphp

                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-body p-4 p-md-5">

                                    {{-- Titolo + badge stato --}}
                                    <div
                                        class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
                                        <div>
                                            <h4 class="fw-bold mb-1 text-primary">
                                                {{ $application->project->title }}
                                            </h4>
                                            <p class="text-muted small mb-0 fw-medium">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                Inviata il {{ $application->created_at->format('d/m/Y \a\l\l\e H:i') }}
                                            </p>
                                        </div>

                                        <span
                                            class="badge rounded-pill bg-light border px-3 py-2 {{ $statusConfig['class'] }} d-inline-flex align-items-center gap-1"
                                            style="font-size: 0.85rem;">
                                            <i class="bi {{ $statusConfig['icon'] }}"></i>
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </div>

                                    @if($application->admin_message)
                                        <div class="p-3 rounded-3 mb-4 bg-light border">
                                            <p class="small fw-bold mb-1 text-dark">
                                                <i class="bi bi-chat-left-text text-primary me-1"></i>
                                                Messaggio dall'organizzazione:
                                            </p>
                                            <p class="small text-secondary mb-0" style="line-height: 1.6;">
                                                {!! nl2br(e($application->admin_message)) !!}
                                            </p>
                                        </div>
                                    @endif

                                    {{-- Azioni --}}
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 border-top">
                                        <div class="d-flex gap-2 flex-wrap">
                                            <a href="{{ route('applications.show', $application) }}"
                                                class="btn btn-ae btn-ae-outline-secondary btn-sm btn-ae-square">
                                                <i class="bi bi-eye me-1"></i>
                                                Dettagli
                                            </a>

                                            <a href="{{ route('project.show', $application->project->id) }}"
                                                class="btn btn-ae btn-ae-outline-secondary btn-sm btn-ae-square">
                                                <i class="bi bi-folder2-open me-1"></i>
                                                Progetto
                                            </a>
                                        </div>

                                        @if($application->status === 'pending')
                                            <button type="button" class="btn btn-ae btn-ae-outline-danger btn-sm btn-ae-square"
                                                data-bs-toggle="modal" data-bs-target="#withdrawModal{{ $application->id }}">
                                                <i class="bi bi-x-lg me-1"></i>Ritira
                                            </button>
                                        @elseif($application->status === 'approved')
                                            <span class="text-success small fw-bold bg-success-subtle px-3 py-2 rounded-pill">
                                                <i class="bi bi-trophy-fill me-1"></i>
                                                Congratulazioni!
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            {{-- Modal ritiro --}}
                            @if($application->status === 'pending')
                                <div class="modal fade" id="withdrawModal{{ $application->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <form action="{{ route('applications.withdraw', $application) }}" method="POST">
                                                @csrf @method('DELETE')

                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-dark mb-0">Ritira Candidatura</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body pt-3">
                                                    <p class="mb-2 text-dark">
                                                        Stai per ritirare la tua candidatura per il progetto:<br>
                                                        <strong class="text-primary fs-6">{{ $application->project->title }}</strong>
                                                    </p>
                                                    <div class="alert alert-warning border-0 small py-2 px-3 mb-0">
                                                        <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                                                        Questa azione è irreversibile. Potrai candidarti nuovamente solo se il progetto
                                                        ha ancora i termini di iscrizione aperti.
                                                    </div>
                                                </div>

                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button"
                                                        class="btn btn-link text-secondary text-decoration-none fw-medium"
                                                        data-bs-dismiss="modal">
                                                        Annulla
                                                    </button>
                                                    <button type="submit" class="btn btn-ae btn-ae-danger btn-ae-square px-4">
                                                        Conferma Ritiro
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @endforeach
                    </div>

                    {{-- Paginazione Compatta --}}
                    <div class="mt-5 d-flex justify-content-center">
                        @if (isset($applications) && method_exists($applications, 'links'))
                            {{ $applications->onEachSide(1)->links() }}
                        @endif
                    </div>

                @else
                    <div class="bg-white border shadow-sm rounded-4 p-5 text-center my-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                            style="width: 80px; height: 80px;">
                            <i class="bi bi-file-earmark-text text-secondary display-5"></i>
                        </div>
                        <h3 class="fw-bold mb-2 text-dark">Nessuna candidatura</h3>
                        <p class="text-muted mb-4">
                            Non hai ancora inviato nessuna candidatura.<br>
                            Esplora i progetti disponibili e trova l'opportunità giusta per te!
                        </p>
                        <a href="{{ route('project.index') }}"
                            class="btn btn-ae btn-ae-primary btn-ae-square px-4 py-2 shadow-sm fw-semibold">
                            <i class="bi bi-search me-2"></i>Esplora i progetti
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection