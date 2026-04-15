@extends('layouts.master')

@section('title', 'AE - Il Mio Profilo')

@section('breadcrumb')
    <div class="bg-light py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Il Mio Profilo</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('body')
    <div class="container px-2 px-md-4 pb-5">
        <!-- Header Profilo Semplice -->
        <div class="row align-items-center mb-4 py-4 border-bottom">
            <div class="col-auto">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 70px; height: 70px;">
                    <i class="bi bi-person-fill fs-2"></i>
                </div>
            </div>
            <div class="col">
                <h1 class="mb-2 fw-bold">{{ $user->name }}</h1>
                <p class="text-muted mb-0">
                    <i class="bi bi-envelope me-2"></i>{{ $user->email }}
                </p>
            </div>
            @if($user->role !== 'admin')
                <div class="col-auto">
                    <a href="{{ route('favorites.index') }}" class="btn btn-ae btn-ae-outline-primary">
                        <i class="bi bi-heart me-2"></i>I Miei Preferiti
                    </a>
                </div>
            @endif
        </div>

        <!-- Layout principale -->
        @if($user->role !== 'admin')
            <!-- Layout per utenti normali -->
            <div class="row g-4">
                <!-- Prima riga: Le Mie Candidature -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div
                            class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top-4">
                            <h5 class="mb-0">
                                <i class="bi bi-file-earmark-text me-2"></i>Le Mie Candidature
                            </h5>
                            <a href="{{ route('applications.index') }}" class="btn btn-ae btn-ae-light btn-sm">
                                <i class="bi bi-eye me-1"></i>Vedi tutte
                            </a>
                        </div>
                        <div class="card-body p-4">
                            @php
                                $recentApplications = $user->applications()->with('project')->latest()->take(3)->get();
                            @endphp

                            @if($recentApplications->count() > 0)
                                @foreach($recentApplications as $application)
                                    <div class="border-bottom pb-3 mb-3 last:border-bottom-0 last:pb-0 last:mb-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-2 fw-bold">{{ $application->project->title }}</h6>
                                                <p class="text-muted small mb-2">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    Candidatura inviata il {{ $application->created_at->format('d/m/Y') }}
                                                </p>
                                                <p class="text-muted small mb-2">
                                                    <i class="bi bi-geo-alt me-1"></i>{{ $application->project->location }}
                                                    <span class="ms-3">
                                                        <i class="bi bi-calendar2-range me-1"></i>
                                                        {{ $application->project->start_date->format('d/m/Y') }} -
                                                        {{ $application->project->end_date->format('d/m/Y') }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="ms-3 text-end">
                                                <span
                                                    class="badge bg-{{ $application->status === 'approved' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }} mb-2">
                                                    {{ $application->status === 'approved' ? 'Approvata' : ($application->status === 'rejected' ? 'Rifiutata' : 'In Attesa') }}
                                                </span>
                                                <br>
                                                <a href="{{ route('applications.show', $application) }}"
                                                    class="btn btn-ae btn-ae-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>Dettagli
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted mt-3 mb-2">Nessuna candidatura</h5>
                                    <p class="text-muted mb-4">Non hai ancora inviato candidature per alcun progetto.</p>
                                    <a href="{{ route('project.index') }}" class="btn btn-ae btn-ae-primary">
                                        <i class="bi bi-search me-2"></i>Esplora i Progetti
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Seconda riga: Gestione profilo divisa in due colonne equilibrate -->
                <div class="col-md-6">
                    <!-- Aggiorna Informazioni Personali -->
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-info text-white rounded-top-4">
                            <h5 class="mb-0">
                                <i class="bi bi-person-lines-fill me-2"></i>Informazioni Personali
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Form Aggiorna Informazioni -->
                            <form method="post" action="{{ route('profile.update') }}">
                                @csrf
                                @method('patch')

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Nome Completo</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Indirizzo Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-ae btn-ae-primary w-100">
                                    <i class="bi bi-check-circle me-2"></i>Aggiorna Profilo
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Password Management -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-warning text-white rounded-top-4">
                            <h5 class="mb-0">
                                <i class="bi bi-shield-lock me-2"></i>Sicurezza Account
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Form Cambia Password -->
                            <form method="post" action="{{ route('profile.password.update') }}">
                                @csrf
                                @method('put')

                                <!-- Info requisiti password -->
                                <div class="alert alert-info small mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Requisiti password:</strong><br>
                                    <span id="req-length" class="d-block text-muted">
                                        <i class="bi bi-x-circle me-2"></i>Almeno 8 caratteri
                                    </span>
                                    <span id="req-uppercase" class="d-block text-muted">
                                        <i class="bi bi-x-circle me-2"></i>Almeno una lettera maiuscola
                                    </span>
                                    <span id="req-lowercase" class="d-block text-muted">
                                        <i class="bi bi-x-circle me-2"></i>Almeno una lettera minuscola
                                    </span>
                                    <span id="req-number" class="d-block text-muted">
                                        <i class="bi bi-x-circle me-2"></i>Almeno un numero
                                    </span>
                                    <span id="req-symbol" class="d-block text-muted">
                                        <i class="bi bi-x-circle me-2"></i>Almeno un carattere speciale (!@#$%^&*)
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <label for="update_password_current_password" class="form-label fw-semibold">Password
                                        Attuale</label>
                                    <input type="password" class="form-control" id="update_password_current_password"
                                        name="current_password" required>
                                    @error('current_password', 'updatePassword')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Nuova Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    @error('password', 'updatePassword')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="update_password_password_confirmation" class="form-label fw-semibold">Conferma
                                        Nuova Password</label>
                                    <input type="password" class="form-control" id="update_password_password_confirmation"
                                        name="password_confirmation" required>
                                    @error('password_confirmation', 'updatePassword')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-ae btn-ae-warning w-100">
                                    <i class="bi bi-key me-2"></i>Aggiorna Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Statistiche -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-secondary text-white rounded-top-4">
                            <h5 class="mb-0">
                                <i class="bi bi-bar-chart me-2"></i>Le Tue Statistiche
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row text-center g-3">
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-primary bg-opacity-10 rounded-3">
                                        <h4 class="text-primary mb-1">{{ $user->applications()->count() }}</h4>
                                        <small class="text-muted fw-semibold">Candidature Inviate</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-success bg-opacity-10 rounded-3">
                                        <h4 class="text-success mb-1">
                                            {{ $user->applications()->where('status', 'approved')->count() }}</h4>
                                        <small class="text-muted fw-semibold">Approvate</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-info bg-opacity-10 rounded-3">
                                        <h4 class="text-info mb-1">{{ $user->favoriteProjects()->count() }}</h4>
                                        <small class="text-muted fw-semibold">Preferiti</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded-3">
                                        <h4 class="text-warning mb-1">
                                            {{ $user->applications()->where('status', 'pending')->count() }}</h4>
                                        <small class="text-muted fw-semibold">In Attesa</small>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="bi bi-calendar-heart me-1"></i>
                                    Membro dal {{ $user->created_at->format('F Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Layout per Admin -->
            <div class="row g-4">
                <!-- Dashboard Admin -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-success text-white rounded-top-4">
                            <h5 class="mb-0">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard Amministratore
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6 col-lg-3">
                                    <div class="card bg-primary text-white border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="card-title mb-2">Progetti Attivi</h6>
                                                    <h3 class="mb-0">
                                                        {{ \App\Models\Project::where('status', 'published')->count() }}</h3>
                                                </div>
                                                <i class="bi bi-folder-open" style="font-size: 2rem; opacity: 0.8;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <div class="card bg-success text-white border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="card-title mb-2">Candidature</h6>
                                                    <h3 class="mb-0">{{ \App\Models\Application::count() }}</h3>
                                                </div>
                                                <i class="bi bi-people" style="font-size: 2rem; opacity: 0.8;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <div class="card bg-info text-white border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="card-title mb-2">Utenti</h6>
                                                    <h3 class="mb-0">
                                                        {{ \App\Models\User::where('role', 'registered_user')->count() }}</h3>
                                                </div>
                                                <i class="bi bi-person-check" style="font-size: 2rem; opacity: 0.8;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <div class="card bg-warning text-white border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="card-title mb-2">Progetti Totali</h6>
                                                    <h3 class="mb-0">{{ \App\Models\Project::count() }}</h3>
                                                </div>
                                                <i class="bi bi-journal-bookmark" style="font-size: 2rem; opacity: 0.8;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="border-top pt-3">
                                <h6 class="mb-3">Azioni Rapide</h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('project.create') }}" class="btn btn-ae btn-ae-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Nuovo Progetto
                                    </a>
                                    <a href="{{ route('project.index') }}" class="btn btn-ae btn-ae-outline-primary">
                                        <i class="bi bi-list me-2"></i>Gestisci Progetti
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gestione profilo Admin -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-info text-white rounded-top-4">
                            <h5 class="mb-0">
                                <i class="bi bi-person-lines-fill me-2"></i>Informazioni Personali
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="post" action="{{ route('profile.update') }}">
                                @csrf
                                @method('patch')

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Nome Completo</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Indirizzo Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-ae btn-ae-primary w-100">
                                    <i class="bi bi-check-circle me-2"></i>Aggiorna Profilo
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Password Admin -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-warning text-white rounded-top-4">
                            <h5 class="mb-0">
                                <i class="bi bi-shield-lock me-2"></i>Sicurezza Account
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="post" action="{{ route('profile.password.update') }}">
                                @csrf
                                @method('put')

                                <div class="alert alert-info small mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Requisiti password:</strong><br>
                                    <span id="req-length-admin" class="d-block text-muted">
                                        <i class="bi bi-x-circle me-2"></i>Almeno 8 caratteri
                                    </span>
                                    <span id="req-uppercase-admin" class="d-block text-muted">
                                        <i class="bi bi-x-circle me-2"></i>Almeno una lettera maiuscola
                                    </span>
                                    <span id="req-lowercase-admin" class="d-block text-muted">
                                        <i class="bi bi-x-circle me-2"></i>Almeno una lettera minuscola
                                    </span>
                                    <span id="req-number-admin" class="d-block text-muted">
                                        <i class="bi bi-x-circle me-2"></i>Almeno un numero
                                    </span>
                                    <span id="req-symbol-admin" class="d-block text-muted">
                                        <i class="bi bi-x-circle me-2"></i>Almeno un carattere speciale (!@#$%^&*)
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <label for="update_password_current_password_admin" class="form-label fw-semibold">Password
                                        Attuale</label>
                                    <input type="password" class="form-control" id="update_password_current_password_admin"
                                        name="current_password" required>
                                    @error('current_password', 'updatePassword')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_admin" class="form-label fw-semibold">Nuova Password</label>
                                    <input type="password" class="form-control" id="password_admin" name="password" required>
                                    @error('password', 'updatePassword')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="update_password_password_confirmation_admin"
                                        class="form-label fw-semibold">Conferma Nuova Password</label>
                                    <input type="password" class="form-control" id="update_password_password_confirmation_admin"
                                        name="password_confirmation" required>
                                    @error('password_confirmation', 'updatePassword')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-ae btn-ae-warning w-100">
                                    <i class="bi bi-key me-2"></i>Aggiorna Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const passwordAdminInput = document.getElementById('password_admin');

            // Requisiti per utenti normali
            const requirements = {
                length: document.getElementById('req-length'),
                uppercase: document.getElementById('req-uppercase'),
                lowercase: document.getElementById('req-lowercase'),
                number: document.getElementById('req-number'),
                symbol: document.getElementById('req-symbol')
            };

            // Requisiti per admin
            const adminRequirements = {
                length: document.getElementById('req-length-admin'),
                uppercase: document.getElementById('req-uppercase-admin'),
                lowercase: document.getElementById('req-lowercase-admin'),
                number: document.getElementById('req-number-admin'),
                symbol: document.getElementById('req-symbol-admin')
            };

            function setupPasswordValidation(input, reqElements) {
                if (input) {
                    input.addEventListener('input', function () {
                        const password = this.value;

                        // Lunghezza minima (8 caratteri)
                        updateRequirement(reqElements.length, password.length >= 8);

                        // Lettera maiuscola
                        updateRequirement(reqElements.uppercase, /[A-Z]/.test(password));

                        // Lettera minuscola
                        updateRequirement(reqElements.lowercase, /[a-z]/.test(password));

                        // Numero
                        updateRequirement(reqElements.number, /\d/.test(password));

                        // Simbolo
                        updateRequirement(reqElements.symbol, /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(password));
                    });
                }
            }

            // Setup per entrambi i campi password
            setupPasswordValidation(passwordInput, requirements);
            setupPasswordValidation(passwordAdminInput, adminRequirements);

            function updateRequirement(element, isValid) {
                if (element) {
                    const icon = element.querySelector('i');
                    if (isValid) {
                        element.classList.add('text-success');
                        element.classList.remove('text-danger');
                        if (icon) {
                            icon.className = 'bi bi-check-circle-fill me-2';
                        }
                    } else {
                        element.classList.add('text-danger');
                        element.classList.remove('text-success');
                        if (icon) {
                            icon.className = 'bi bi-x-circle me-2';
                        }
                    }
                }
            }
        });
    </script>
@endsection