@extends('layouts.master')

@section('title', 'AE - Il Mio Profilo')

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
                                    <p class="text-muted mb-0 pe-4">Visualizza lo stato e gestisci le tue richieste di
                                        partecipazione.</p>
                                    <a href="{{ route('applications.index') }}" class="stretched-link"
                                        aria-label="Vai a Le Mie Candidature"></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 position-relative profile-action-card">
                                <div class="card-body p-4">
                                    <i class="bi bi-heart text-primary" style="font-size: 2rem;"></i>
                                    <h5 class="mt-3 mb-2 fw-bold text-primary">Progetti Preferiti</h5>
                                    <p class="text-muted mb-0 pe-4">Consulta i progetti che hai salvato per un secondo momento.
                                    </p>
                                    <a href="{{ route('favorites.index') }}" class="stretched-link"
                                        aria-label="Vai a Progetti Preferiti"></a>
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
                                    <div class="input-group shadow-sm rounded-3 bg-white password-input-group">
                                        <input type="password" class="form-control border-0 bg-transparent"
                                            id="update_password_current_password" name="current_password" required>
                                        <button class="btn border-0 bg-transparent toggle-password" type="button"
                                            aria-label="Mostra o nascondi password attuale">
                                            <i class="bi bi-eye text-muted"></i>
                                        </button>
                                    </div>
                                    @error('current_password', 'updatePassword')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Nuova Password</label>
                                    <div class="input-group shadow-sm rounded-3 bg-white mb-2 password-input-group">
                                        <input type="password" class="form-control border-0 bg-transparent" id="password"
                                            name="password" required>
                                        <button class="btn border-0 bg-transparent toggle-password" type="button"
                                            aria-label="Mostra o nascondi nuova password">
                                            <i class="bi bi-eye text-muted"></i>
                                        </button>
                                    </div>
                                    @error('password', 'updatePassword')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="alert alert-info small mt-2 mb-0 border-0 bg-info bg-opacity-10 password-requirements-alert"
                                        id="password-requirements-alert" role="status" aria-live="polite">
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="bi bi-shield-lock"></i>
                                            <div class="flex-grow-1">
                                                <div
                                                    class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-2">
                                                    <strong class="d-block mb-0">Requisiti password</strong>
                                                    <span class="badge text-bg-light border password-strength-badge"
                                                        id="password-strength-badge">0/5 soddisfatti</span>
                                                </div>
                                                <div id="password-requirements-compact" class="small text-success d-none mb-2">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Password valida: tutti i
                                                    requisiti soddisfatti.
                                                </div>
                                                <div id="password-requirements-details">
                                                    <p class="text-muted mb-2">Usa una password robusta per proteggere il tuo
                                                        account.</p>
                                                    <div class="progress mb-3" style="height: 0.4rem;">
                                                        <div class="progress-bar password-requirements-progress bg-danger"
                                                            id="password-requirements-progress" role="progressbar"
                                                            style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                    <div class="password-requirements">
                                                        <span id="req-length" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno 8 caratteri</span>
                                                        </span>
                                                        <span id="req-uppercase" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno una lettera maiuscola (A-Z)</span>
                                                        </span>
                                                        <span id="req-lowercase" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno una lettera minuscola (a-z)</span>
                                                        </span>
                                                        <span id="req-number" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno un numero (0-9)</span>
                                                        </span>
                                                        <span id="req-symbol" class="requirement-item text-danger mb-0">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno un simbolo (!@#$%^&*)</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="update_password_password_confirmation" class="form-label fw-semibold">Conferma
                                        Nuova Password</label>
                                    <div class="input-group shadow-sm rounded-3 bg-white password-input-group">
                                        <input type="password" class="form-control border-0 bg-transparent"
                                            id="update_password_password_confirmation" name="password_confirmation" required>
                                        <button class="btn border-0 bg-transparent toggle-password" type="button"
                                            aria-label="Mostra o nascondi conferma nuova password">
                                            <i class="bi bi-eye text-muted"></i>
                                        </button>
                                    </div>
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
                                    <div class="input-group shadow-sm rounded-3 bg-white password-input-group">
                                        <input type="password" class="form-control border-0 bg-transparent"
                                            id="update_password_current_password_admin" name="current_password" required>
                                        <button class="btn border-0 bg-transparent toggle-password" type="button"
                                            aria-label="Mostra o nascondi password attuale admin">
                                            <i class="bi bi-eye text-muted"></i>
                                        </button>
                                    </div>
                                    @error('current_password', 'updatePassword')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_admin" class="form-label fw-semibold">Nuova Password</label>
                                    <div class="input-group shadow-sm rounded-3 bg-white mb-2 password-input-group">
                                        <input type="password" class="form-control border-0 bg-transparent" id="password_admin"
                                            name="password" required>
                                        <button class="btn border-0 bg-transparent toggle-password" type="button"
                                            aria-label="Mostra o nascondi nuova password admin">
                                            <i class="bi bi-eye text-muted"></i>
                                        </button>
                                    </div>
                                    @error('password', 'updatePassword')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="alert alert-info small mt-2 mb-0 border-0 bg-info bg-opacity-10 password-requirements-alert"
                                        id="password-requirements-alert-admin" role="status" aria-live="polite">
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="bi bi-shield-lock"></i>
                                            <div class="flex-grow-1">
                                                <div
                                                    class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-2">
                                                    <strong class="d-block mb-0">Requisiti password</strong>
                                                    <span class="badge text-bg-light border password-strength-badge"
                                                        id="password-strength-badge-admin">0/5 soddisfatti</span>
                                                </div>
                                                <div id="password-requirements-compact-admin"
                                                    class="small text-success d-none mb-2">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Password valida: tutti i
                                                    requisiti soddisfatti.
                                                </div>
                                                <div id="password-requirements-details-admin">
                                                    <p class="text-muted mb-2">Usa una password robusta per proteggere il tuo
                                                        account.</p>
                                                    <div class="progress mb-3" style="height: 0.4rem;">
                                                        <div class="progress-bar password-requirements-progress bg-danger"
                                                            id="password-requirements-progress-admin" role="progressbar"
                                                            style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                    <div class="password-requirements">
                                                        <span id="req-length-admin" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno 8 caratteri</span>
                                                        </span>
                                                        <span id="req-uppercase-admin" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno una lettera maiuscola (A-Z)</span>
                                                        </span>
                                                        <span id="req-lowercase-admin" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno una lettera minuscola (a-z)</span>
                                                        </span>
                                                        <span id="req-number-admin" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno un numero (0-9)</span>
                                                        </span>
                                                        <span id="req-symbol-admin" class="requirement-item text-danger mb-0">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno un simbolo (!@#$%^&*)</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="update_password_password_confirmation_admin"
                                        class="form-label fw-semibold">Conferma Nuova Password</label>
                                    <div class="input-group shadow-sm rounded-3 bg-white password-input-group">
                                        <input type="password" class="form-control border-0 bg-transparent"
                                            id="update_password_password_confirmation_admin" name="password_confirmation"
                                            required>
                                        <button class="btn border-0 bg-transparent toggle-password" type="button"
                                            aria-label="Mostra o nascondi conferma nuova password admin">
                                            <i class="bi bi-eye text-muted"></i>
                                        </button>
                                    </div>
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

<style>
    .requirement-item {
        transition: all 0.3s ease;
        font-size: 0.875rem;
        display: flex;
        align-items: flex-start;
        gap: 0.45rem;
        line-height: 1.35;
        margin-bottom: 0.4rem;
    }

    .requirement-item i {
        margin-top: 0.08rem;
    }

    .requirement-item.text-success {
        font-weight: 500;
    }

    .password-requirements {
        line-height: 1.4;
    }

    .password-requirements-alert {
        border-left: 4px solid rgba(13, 110, 253, 0.35);
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }

    .password-requirements-alert.password-requirements-alert-complete {
        border-left-color: rgba(25, 135, 84, 0.5);
        background-color: rgba(25, 135, 84, 0.1) !important;
    }

    .password-strength-badge {
        font-weight: 600;
    }

    .password-strength-badge.password-strength-badge-complete {
        background-color: #d1e7dd !important;
        color: #0f5132 !important;
        border: 1px solid #75b798 !important;
    }

    .password-requirements-progress {
        transition: width 0.25s ease, background-color 0.25s ease;
    }

    .password-input-group:focus-within {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }

    .password-input-group .form-control:focus {
        box-shadow: none !important;
    }
</style>

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const passwordAdminInput = document.getElementById('password_admin');

            document.querySelectorAll('.toggle-password').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = this.closest('.input-group')?.querySelector('input');
                    const icon = this.querySelector('i');

                    if (!input || !icon) {
                        return;
                    }

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            });

            // Requisiti per utenti normali
            const requirements = {
                length: document.getElementById('req-length'),
                uppercase: document.getElementById('req-uppercase'),
                lowercase: document.getElementById('req-lowercase'),
                number: document.getElementById('req-number'),
                symbol: document.getElementById('req-symbol')
            };

            const requirementsUi = {
                alert: document.getElementById('password-requirements-alert'),
                details: document.getElementById('password-requirements-details'),
                compact: document.getElementById('password-requirements-compact'),
                progressBar: document.getElementById('password-requirements-progress'),
                badge: document.getElementById('password-strength-badge')
            };

            // Requisiti per admin
            const adminRequirements = {
                length: document.getElementById('req-length-admin'),
                uppercase: document.getElementById('req-uppercase-admin'),
                lowercase: document.getElementById('req-lowercase-admin'),
                number: document.getElementById('req-number-admin'),
                symbol: document.getElementById('req-symbol-admin')
            };

            const adminRequirementsUi = {
                alert: document.getElementById('password-requirements-alert-admin'),
                details: document.getElementById('password-requirements-details-admin'),
                compact: document.getElementById('password-requirements-compact-admin'),
                progressBar: document.getElementById('password-requirements-progress-admin'),
                badge: document.getElementById('password-strength-badge-admin')
            };

            function setupPasswordValidation(input, reqElements, uiElements) {
                if (input) {
                    const runValidation = function () {
                        const password = this.value;
                        let satisfiedRequirements = 0;

                        // Lunghezza minima (8 caratteri)
                        satisfiedRequirements += Number(updateRequirement(reqElements.length, password.length >= 8));

                        // Lettera maiuscola
                        satisfiedRequirements += Number(updateRequirement(reqElements.uppercase, /[A-Z]/.test(password)));

                        // Lettera minuscola
                        satisfiedRequirements += Number(updateRequirement(reqElements.lowercase, /[a-z]/.test(password)));

                        // Numero
                        satisfiedRequirements += Number(updateRequirement(reqElements.number, /\d/.test(password)));

                        // Simbolo
                        satisfiedRequirements += Number(updateRequirement(reqElements.symbol, /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(password)));

                        updateRequirementsSummary(uiElements, satisfiedRequirements, 5);
                    };

                    input.addEventListener('input', runValidation);
                    runValidation.call(input);
                }
            }

            // Setup per entrambi i campi password
            setupPasswordValidation(passwordInput, requirements, requirementsUi);
            setupPasswordValidation(passwordAdminInput, adminRequirements, adminRequirementsUi);

            function updateRequirement(element, isValid) {
                if (element) {
                    const icon = element.querySelector('i');
                    if (isValid) {
                        element.classList.add('text-success');
                        element.classList.remove('text-danger');
                        if (icon) {
                            icon.className = 'bi bi-check-circle-fill';
                        }
                    } else {
                        element.classList.add('text-danger');
                        element.classList.remove('text-success');
                        if (icon) {
                            icon.className = 'bi bi-x-circle';
                        }
                    }
                }

                return isValid;
            }

            function updateRequirementsSummary(uiElements, satisfiedRequirements, totalRequirements) {
                if (!uiElements.alert || !uiElements.details || !uiElements.compact || !uiElements.progressBar || !uiElements.badge) {
                    return;
                }

                const progressPercentage = Math.round((satisfiedRequirements / totalRequirements) * 100);
                const isComplete = satisfiedRequirements === totalRequirements;

                uiElements.badge.textContent = satisfiedRequirements + '/' + totalRequirements + ' soddisfatti';
                uiElements.progressBar.style.width = progressPercentage + '%';
                uiElements.progressBar.setAttribute('aria-valuenow', String(progressPercentage));
                uiElements.progressBar.classList.remove('bg-danger', 'bg-warning', 'bg-success');
                uiElements.progressBar.classList.add(progressPercentage < 40 ? 'bg-danger' : progressPercentage < 100 ? 'bg-warning' : 'bg-success');

                if (isComplete) {
                    uiElements.badge.classList.remove('text-bg-light', 'border');
                    uiElements.badge.classList.add('password-strength-badge-complete');
                    uiElements.alert.classList.add('password-requirements-alert-complete');
                    uiElements.details.classList.add('d-none');
                    uiElements.compact.classList.remove('d-none');
                } else {
                    uiElements.badge.classList.remove('password-strength-badge-complete');
                    uiElements.badge.classList.add('text-bg-light', 'border');
                    uiElements.alert.classList.remove('password-requirements-alert-complete');
                    uiElements.details.classList.remove('d-none');
                    uiElements.compact.classList.add('d-none');
                }
            }
        });
    </script>
@endsection