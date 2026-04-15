@extends('layouts.master')

@section('title', 'AE - Registrati')

@section('body')
    <script>
        $(document).ready(function () {
            // Validazione in tempo reale per la password
            $('#password').on('input', function () {
                validatePasswordRequirements($(this).val());
            });

            validatePasswordRequirements($('#password').val() || '');

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

            function validatePasswordRequirements(password) {
                // Controllo lunghezza minima
                updateRequirement('#req-length', password.length >= 8);

                // Controllo lettera maiuscola
                updateRequirement('#req-uppercase', /[A-Z]/.test(password));

                // Controllo lettera minuscola
                updateRequirement('#req-lowercase', /[a-z]/.test(password));

                // Controllo numero
                updateRequirement('#req-number', /\d/.test(password));

                // Controllo simbolo
                updateRequirement('#req-symbol', /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(password));

                updateRequirementsSummary();
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

            function updateRequirementsSummary() {
                const totalRequirements = 5;
                const satisfiedRequirements = $('.password-requirements .requirement-item.text-success').length;
                const progressPercentage = Math.round((satisfiedRequirements / totalRequirements) * 100);
                const progressBar = $('#password-requirements-progress');
                const requirementsAlert = $('.password-requirements-alert');
                const requirementsDetails = $('#password-requirements-details');
                const requirementsCompact = $('#password-requirements-compact');
                const strengthBadge = $('#password-strength-badge');
                const isComplete = satisfiedRequirements === totalRequirements;

                strengthBadge.text(satisfiedRequirements + '/' + totalRequirements + ' soddisfatti');

                progressBar
                    .css('width', progressPercentage + '%')
                    .attr('aria-valuenow', progressPercentage)
                    .removeClass('bg-danger bg-warning bg-success')
                    .addClass(progressPercentage < 40 ? 'bg-danger' : progressPercentage < 100 ? 'bg-warning' : 'bg-success');

                if (isComplete) {
                    strengthBadge.removeClass('text-bg-light border').addClass('text-bg-success');
                    requirementsAlert.addClass('password-requirements-alert-complete');

                    if (requirementsDetails.is(':visible')) {
                        requirementsDetails.stop(true, true).slideUp(180);
                    }

                    requirementsCompact.removeClass('d-none').hide().stop(true, true).fadeIn(160);
                } else {
                    strengthBadge.removeClass('text-bg-success').addClass('text-bg-light border');
                    requirementsAlert.removeClass('password-requirements-alert-complete');

                    if (!requirementsDetails.is(':visible')) {
                        requirementsDetails.stop(true, true).slideDown(180);
                    }

                    requirementsCompact.stop(true, true).hide().addClass('d-none');
                }
            }

            $("#register-form").submit(function (event) {
                var name = $("input[name='name']").val();
                var email = $("#register-form input[name='email']").val();
                var password = $("#register-form input[name='password']").val();
                var confirmPassword = $("input[name='password_confirmation']").val();

                // Nuova regex più completa per i requisiti aggiornati
                var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]).{8,}$/;
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                var hasErrors = false;

                // Reset errori precedenti
                $(".invalid-input").text("");

                // Verifica nome
                if (name.trim() === "") {
                    hasErrors = true;
                    $("#invalid-name").text("Il nome è obbligatorio.");
                }

                // Verifica email
                if (email.trim() === "") {
                    hasErrors = true;
                    $("#invalid-email").text("L'indirizzo email è obbligatorio.");
                } else if (!emailRegex.test(email)) {
                    hasErrors = true;
                    $("#invalid-email").text("Inserisci un indirizzo email valido.");
                }

                // Verifica password con i nuovi requisiti
                if (password.trim() === "") {
                    hasErrors = true;
                    $("#invalid-password").text("La password è obbligatoria.");
                } else if (!passwordRegex.test(password)) {
                    hasErrors = true;
                    $("#invalid-password").text(
                        "La password deve soddisfare tutti i requisiti di sicurezza indicati."
                    );
                }

                // Verifica conferma password
                if (confirmPassword.trim() === "") {
                    hasErrors = true;
                    $("#invalid-password-confirmation").text("La conferma password è obbligatoria.");
                } else if (password !== confirmPassword) {
                    hasErrors = true;
                    $("#invalid-password-confirmation").text("Le password non coincidono.");
                }

                // Se ci sono errori, blocca l'invio e focalizza il primo campo con errore
                if (hasErrors) {
                    event.preventDefault();
                    console.log("Validazione fallita - invio bloccato");
                    $(".invalid-input").filter(function () {
                        return $(this).text() !== "";
                    }).first().prev().focus();
                    return;
                }

                // Controllo email duplicata via AJAX
                event.preventDefault();
                $.ajax({
                    type: 'GET',
                    url: '/ajaxUser',
                    data: {
                        email: email.trim()
                    },
                    success: function (data) {
                        if (data.found) {
                            $("#invalid-email").text("Questa email è già registrata.");
                            $("#email").focus();
                        } else {
                            $("#register-form")[0].submit();
                        }
                    },
                    error: function () {
                        // Se AJAX fallisce, invia comunque il form (fallback lato server)
                        $("#register-form")[0].submit();
                    }
                });
            });
        });
    </script>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h1 class="section-title mb-2" style="font-size: 3rem;">Unisciti a noi!</h1>
                            <p class="section-subtitle mb-0">Crea il tuo account e inizia a esplorare l'Europa</p>
                        </div>

                        <form id="register-form" method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Nome completo</label>
                                <input type="text" name="name" id="name"
                                    class="form-control form-control-lg border-0 shadow-sm rounded-3 @error('name') is-invalid @enderror"
                                    placeholder="Inserisci il tuo nome completo" value="{{ old('name') }}">
                                <span class="invalid-input text-danger" id="invalid-name"></span>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" name="email" id="email"
                                    class="form-control form-control-lg border-0 shadow-sm rounded-3 @error('email') is-invalid @enderror"
                                    placeholder="Inserisci la tua email" value="{{ old('email') }}">
                                <span class="invalid-input text-danger" id="invalid-email"></span>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password</label>

                                <div class="input-group shadow-sm rounded-3 bg-white mb-2">
                                    <input type="password" name="password" id="password"
                                        class="form-control form-control-lg border-0 bg-transparent @error('password') is-invalid @enderror"
                                        placeholder="Crea una password sicura">
                                    <button class="btn border-0 bg-transparent toggle-password" type="button"
                                        aria-label="Mostra o nascondi password">
                                        <i class="bi bi-eye text-muted"></i>
                                    </button>
                                </div>
                                <span class="invalid-input text-danger" id="invalid-password"></span>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- Info requisiti password -->
                                <div class="alert alert-info small mb-3 border-0 bg-info bg-opacity-10 password-requirements-alert"
                                    role="status" aria-live="polite">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-shield-lock"></i>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-2">
                                                <strong class="d-block mb-0">Requisiti password</strong>
                                                <span class="badge text-bg-light border password-strength-badge" id="password-strength-badge">0/5 soddisfatti</span>
                                            </div>
                                            <div id="password-requirements-compact" class="small text-success d-none mb-2">
                                                <i class="bi bi-check-circle-fill me-1"></i>Password valida: tutti i requisiti soddisfatti.
                                            </div>
                                            <div id="password-requirements-details">
                                                <p class="text-muted mb-2">Usa una password robusta per proteggere il tuo account.</p>
                                                <div class="progress mb-3" style="height: 0.4rem;">
                                                    <div class="progress-bar password-requirements-progress bg-danger"
                                                        id="password-requirements-progress" role="progressbar"
                                                        style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <div class="password-requirements">
                                                    <div id="req-length" class="requirement-item text-danger">
                                                        <i class="bi bi-x-circle"></i>
                                                        <span>Almeno 8 caratteri</span>
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
                                                        <span>Almeno un simbolo (!@#$%^&*)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-bold">Conferma Password</label>
                                <div class="input-group shadow-sm rounded-3 bg-white mb-2">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control form-control-lg border-0 bg-transparent"
                                        placeholder="Reinserisci la password">
                                    <button class="btn border-0 bg-transparent toggle-password" type="button"
                                        aria-label="Mostra o nascondi conferma password">
                                        <i class="bi bi-eye text-muted"></i>
                                    </button>
                                </div>
                                <span class="invalid-input text-danger" id="invalid-password-confirmation"></span>
                            </div>

                            <button type="submit" class="btn btn-ae btn-ae-primary btn-lg btn-ae-pill w-100 py-3 mb-3">
                                <i class="bi bi-person-plus-fill me-2"></i>Registrati
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="mb-0">Hai già un account?
                                <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Accedi ora</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        .password-requirements-progress {
            transition: width 0.25s ease, background-color 0.25s ease;
        }

        .input-group:has(.form-control:focus) {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        .input-group .form-control:focus {
            box-shadow: none !important;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            border-color: #86b7fe;
        }
    </style>
@endsection