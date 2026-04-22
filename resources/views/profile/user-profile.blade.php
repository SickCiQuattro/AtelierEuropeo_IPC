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
                <p class="main-text mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-envelope"></i>
                    <span>{{ $user->email }}</span>
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
                            <div class="card text-center border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                                    <i class="bi bi-file-earmark-text text-primary d-block mb-2" style="font-size: 2rem;"></i>
                                    <h5 class="mb-2 fw-bold text-primary">Le Mie Candidature</h5>
                                    <p class="text-muted mb-0">Visualizza lo stato e gestisci le tue richieste di
                                        partecipazione.</p>
                                </div>
                                <hr class="m-0 text-muted" style="opacity: 0.15;">
                                <div class="card-footer bg-transparent border-0 p-3">
                                    <a href="{{ route('applications.index') }}"
                                        class="btn btn-ae btn-ae-outline-primary">
                                        Gestisci Candidature <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card text-center border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                                    <i class="bi bi-heart text-primary d-block mb-2" style="font-size: 2rem;"></i>
                                    <h5 class="mb-2 fw-bold text-primary">Progetti Preferiti</h5>
                                    <p class="text-muted mb-0">Consulta i progetti che hai salvato per un secondo momento.
                                    </p>
                                </div>
                                <hr class="m-0 text-muted" style="opacity: 0.15;">
                                <div class="card-footer bg-transparent border-0 p-3">
                                    <a href="{{ route('favorites.index') }}"
                                        class="btn btn-ae btn-ae-outline-primary">
                                        Vai ai Preferiti <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seconda riga: Gestione profilo divisa in due colonne equilibrate -->
                <div class="col-md-6">
                    <!-- Aggiorna Informazioni Personali -->
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-transparent bord er-bottom-0 pt-4 pb-0 px-4">
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
                                    <div id="password-requirements-alert"
                                        class="alert alert-info small mb-3 border-0 bg-info bg-opacity-10 password-requirements-alert"
                                        role="status" aria-live="polite">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <div
                                                    class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-2">
                                                    <strong class="d-block mb-0"><i class="bi bi-shield-lock me-2"></i>Requisiti password</strong>
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
                                                        <div id="req-length" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>Almeno 8 caratteri
                                                        </div>
                                                        <div id="req-uppercase" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno una lettera maiuscola (A-Z)</span>
                                                        </div>
                                                        <div id="req-lowercase" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno una lettera minuscola (a-z)</span>
                                                        </div>
                                                        <div id="req-number" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno un numero (0-9)</span>
                                                        </div>
                                                        <div id="req-symbol" class="requirement-item text-danger mb-0">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno un simbolo (!@#$%^&amp;*)</span>
                                                        </div>
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

                                <button type="submit" class="btn btn-ae-primary w-100">
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
                                    <div id="password-requirements-alert-admin"
                                        class="alert alert-info small mb-3 border-0 bg-info bg-opacity-10 password-requirements-alert"
                                        role="status" aria-live="polite">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <div
                                                    class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-2">
                                                    <strong class="d-block mb-0"><i class="bi bi-shield-lock me-2"></i>Requisiti password</strong>
                                                    <span class="badge text-bg-light border password-strength-badge"
                                                        id="password-strength-b adge-admin">0/5 soddisfatti</span>
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
                                                        <div id="req-length-admin" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno 8 caratteri</span>
                                                        </div>
                                                        <div id="req-uppercase-admin" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno una lettera maiuscola (A-Z)</span>
                                                        </div>
                                                        <div id="req-lowercase-admin" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno una lettera minuscola (a-z)</span>
                                                        </div>
                                                        <div id="req-number-admin" class="requirement-item text-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno un numero (0-9)</span>
                                                        </div>
                                                        <div id="req-symbol-admin" class="requirement-item text-danger mb-0">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Almeno un simbolo (!@#$%^&*)</span>
                                                        </div>
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

                                <button type="submit" class="btn btn-ae-primary w-100">
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
        align-items: center;
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
        margin-top: 0.5rem;
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
        $(document).ready(function () {
            $('.toggle-password').click(function () {
                let input = $(this).siblings('input');
                let icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            function setupPasswordWidget(inputId, suffix) {
                const input = $(inputId);

                if (input.length === 0) {
                    return;
                }

                input.on('input', function () {
                    validatePasswordRequirements($(this).val(), suffix);
                });

                validatePasswordRequirements(input.val() || '', suffix);
            }

            function validatePasswordRequirements(password, suffix) {
                updateRequirement('#req-length' + suffix, password.length >= 8);
                updateRequirement('#req-uppercase' + suffix, /[A-Z]/.test(password));
                updateRequirement('#req-lowercase' + suffix, /[a-z]/.test(password));
                updateRequirement('#req-number' + suffix, /\d/.test(password));
                updateRequirement('#req-symbol' + suffix, /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(password));

                updateRequirementsSummary(suffix);
            }

            function updateRequirement(selector, isValid) {
                const element = $(selector);
                const icon = element.find('i');

                if (isValid) {
                    element.removeClass('text-danger').addClass('text-success');
                    icon.removeClass('bi-x-circle').addClass('bi-check-circle-fill');
                } else {
                    element.removeClass('text-success').addClass('text-danger');
                    icon.removeClass('bi-check-circle-fill').addClass('bi-x-circle');
                }
            }

            function updateRequirementsSummary(suffix) {
                const totalRequirements = 5;
                const parentContainer = suffix === '-admin' ? $('#password-requirements-details-admin') : $('#password-requirements-details');
                const satisfiedRequirements = parentContainer.find('.requirement-item.text-success').length;
                const progressPercentage = Math.round((satisfiedRequirements / totalRequirements) * 100);
                const progressBar = $('#password-requirements-progress' + suffix);
                const strengthBadge = $('#password-strength-badge' + suffix);
                const requirementsDetails = $('#password-requirements-details' + suffix);
                const requirementsCompact = $('#password-requirements-compact' + suffix);
                const requirementsAlert = strengthBadge.closest('.password-requirements-alert');
                const isComplete = satisfiedRequirements === totalRequirements;

                strengthBadge.text(satisfiedRequirements + '/' + totalRequirements + ' soddisfatti');

                progressBar
                    .css('width', progressPercentage + '%')
                    .attr('aria-valuenow', progressPercentage)
                    .removeClass('bg-danger bg-warning bg-success')
                    .addClass(progressPercentage < 40 ? 'bg-danger' : progressPercentage < 100 ? 'bg-warning' : 'bg-success');

                if (isComplete) {
                    strengthBadge.removeClass('text-bg-light border').addClass('password-strength-badge-complete');
                    requirementsAlert.addClass('password-requirements-alert-complete');

                    if (requirementsDetails.is(':visible')) {
                        requirementsDetails.stop(true, true).slideUp(180);
                    }

                    requirementsCompact.removeClass('d-none').hide().stop(true, true).fadeIn(160);
                } else {
                    strengthBadge.removeClass('password-strength-badge-complete').addClass('text-bg-light border');
                    requirementsAlert.removeClass('password-requirements-alert-complete');

                    if (!requirementsDetails.is(':visible')) {
                        requirementsDetails.stop(true, true).slideDown(180);
                    }

                    requirementsCompact.stop(true, true).hide().addClass('d-none');
                }
            }

            setupPasswordWidget('#password', '');
            setupPasswordWidget('#password_admin', '-admin');
        });
    </script>
@endsection