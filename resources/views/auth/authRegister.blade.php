@extends('layouts.master')

@section('title', 'AE - Registrati')

@section('body')
    <script>
        $(document).ready(function () {
            // Validazione in tempo reale per la password
            $('#password').on('input', function () {
                validatePasswordRequirements($(this).val());
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

                                <!-- Info requisiti password -->
                                <div class="alert alert-info small mb-3 border-0 bg-info bg-opacity-10">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-info-circle me-2 mt-1"></i>
                                        <div>
                                            <strong class="d-block mb-2">Requisiti password:</strong>
                                            <div class="password-requirements">
                                                <div id="req-length" class="requirement-item text-danger mb-1">
                                                    <i class="bi bi-x-circle me-2"></i>Almeno 8 caratteri
                                                </div>
                                                <div id="req-uppercase" class="requirement-item text-danger mb-1">
                                                    <i class="bi bi-x-circle me-2"></i>Almeno una lettera maiuscola (A-Z)
                                                </div>
                                                <div id="req-lowercase" class="requirement-item text-danger mb-1">
                                                    <i class="bi bi-x-circle me-2"></i>Almeno una lettera minuscola (a-z)
                                                </div>
                                                <div id="req-number" class="requirement-item text-danger mb-1">
                                                    <i class="bi bi-x-circle me-2"></i>Almeno un numero (0-9)
                                                </div>
                                                <div id="req-symbol" class="requirement-item text-danger mb-0">
                                                    <i class="bi bi-x-circle me-2"></i>Almeno un simbolo (!@#$%^&*)
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="password" name="password" id="password"
                                    class="form-control form-control-lg border-0 shadow-sm rounded-3 @error('password') is-invalid @enderror"
                                    placeholder="Crea una password sicura">
                                <span class="invalid-input text-danger" id="invalid-password"></span>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-bold">Conferma Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control form-control-lg border-0 shadow-sm rounded-3"
                                    placeholder="Reinserisci la password">
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
        }

        .requirement-item.text-success {
            font-weight: 500;
        }

        .password-requirements {
            line-height: 1.4;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            border-color: #86b7fe;
        }
    </style>
@endsection