@extends('layouts.master')

@section('title', 'AE - Il Mio Profilo')

@section('body')
<div class="container px-2 px-md-4 pb-5">
    <!-- Header Profilo Semplice -->
    <div class="row align-items-center mb-4 py-4 border-bottom">
        <div class="col-auto">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                <i class="bi bi-person-fill fs-2"></i>
            </div>
        </div>
        <div class="col">
            <h1 class="section-title text-dark mb-2 fw-bold">{{ $user->name }}</h1>
            <p class="main-text mb-0">
                <i class="bi bi-envelope me-2"></i>{{ $user->email }}
            </p>
        </div>
    </div>

    <!-- Layout principale -->
    @if($user->role !== 'admin')
        <!-- Layout per utenti normali -->
        <div class="row g-4">
            <div class="col-12">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 position-relative profile-action-card">
                            <div class="card-body p-4">
                                <i class="bi bi-file-earmark-text text-primary" style="font-size: 2rem;"></i>
                                <h5 class="mt-3 mb-2 fw-bold text-primary">Le Mie Candidature</h5>
                                <p class="text-muted mb-0 pe-4">Visualizza lo stato e gestisci le tue richieste di partecipazione.</p>
                                <a href="{{ route('applications.index') }}" class="stretched-link" aria-label="Vai a Le Mie Candidature"></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 position-relative profile-action-card">
                            <div class="card-body p-4">
                                <i class="bi bi-heart text-primary" style="font-size: 2rem;"></i>
                                <h5 class="mt-3 mb-2 fw-bold text-primary">Progetti Preferiti</h5>
                                <p class="text-muted mb-0 pe-4">Consulta i progetti che hai salvato per un secondo momento.</p>
                                <a href="{{ route('favorites.index') }}" class="stretched-link" aria-label="Vai a Progetti Preferiti"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Seconda riga: Gestione profilo divisa in due colonne equilibrate -->
            <div class="col-md-6">
                <!-- Aggiorna Informazioni Personali -->
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="mb-0 fw-bold text-primary">
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

                            <button type="submit" class="btn btn-ae-primary w-100">
                                <i class="bi bi-check-circle me-2"></i>Aggiorna Profilo
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Password Management -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-shield-lock me-2"></i>Sicurezza Account
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Form Cambia Password -->
                        <form method="post" action="{{ route('profile.password.update') }}">
                            @csrf
                            @method('put')

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
                                <input type="password" class="form-control" id="password" 
                                       name="password" required>
                                @error('password', 'updatePassword')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text mt-2 p-3 bg-light rounded-3">
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

                            <button type="submit" class="btn btn-ae-outline-primary w-100">
                                <i class="bi bi-key me-2"></i>Aggiorna Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Layout per Admin -->
        <div class="row g-4">
            <!-- Gestione profilo Admin -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="mb-0 fw-bold text-primary">
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

                            <button type="submit" class="btn btn-ae-primary w-100">
                                <i class="bi bi-check-circle me-2"></i>Aggiorna Profilo
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Password Admin -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-shield-lock me-2"></i>Sicurezza Account
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="post" action="{{ route('profile.password.update') }}">
                            @csrf
                            @method('put')

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
                                <input type="password" class="form-control" id="password_admin" 
                                       name="password" required>
                                @error('password', 'updatePassword')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text mt-2 p-3 bg-light rounded-3">
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

                            <button type="submit" class="btn btn-ae-outline-primary w-100">
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