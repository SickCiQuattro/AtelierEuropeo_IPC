@extends('layouts.master')

@section('title', 'AE - Accedi')

@section('body')
    <script>
        $(document).ready(function () {
            $("#login-form").submit(function (event) {
                // Ottenere i valori dei campi email e password
                var email = $("#email").val();
                var password = $("#password").val();
                var hasErrors = false;

                // Reset errori precedenti
                $("#invalid-email").text("");
                $("#invalid-password").text("");

                // Verifica se il campo email è vuoto
                if (email.trim() === '') {
                    hasErrors = true;
                    $("#invalid-email").text("L'indirizzo email non può essere vuoto.");
                }

                // Verifica se il campo password è vuoto
                if (password.trim() === '') {
                    hasErrors = true;
                    $("#invalid-password").text("La password non può essere vuota.");
                }

                // Se ci sono errori, impedisci l'invio e focalizza il primo campo con errore
                if (hasErrors) {
                    event.preventDefault();
                    if (email.trim() === '') {
                        $("#email").focus();
                    } else if (password.trim() === '') {
                        $("#password").focus();
                    }
                }
            });

            // Rimuovi errori quando l'utente inizia a digitare
            $("#email, #password").on('input', function () {
                var fieldName = $(this).attr('name');
                $("#invalid-" + fieldName).text("");
            });

            // Toggle visibilita password
            $("#togglePassword").on('click', function () {
                var passwordField = $("#password");
                var icon = $(this).find("i");
                var isPasswordType = passwordField.attr('type') === 'password';

                passwordField.attr('type', isPasswordType ? 'text' : 'password');
                icon.toggleClass('bi-eye bi-eye-slash');
            });
        });
    </script>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h1 class="section-title mb-2" style="font-size: 3rem;">Accedi</h1>
                            <p class="section-subtitle mb-0">Inserisci le tue credenziali per accedere al tuo profilo</p>
                        </div>

                        <form id="login-form" method="POST" action="{{ route('login') }}">
                            @csrf
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

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password" class="form-label fw-bold mb-0">Password</label>
                                    <a href="{{ route('password.request') }}" class="text-decoration-none small">Password dimenticata?</a>
                                </div>
                                <div class="input-group shadow-sm rounded-3 bg-white">
                                    <input type="password" name="password" id="password"
                                        class="form-control form-control-lg border-0 bg-transparent @error('password') is-invalid @enderror"
                                        placeholder="Inserisci la tua password">
                                    <button class="btn border-0 bg-transparent" type="button" id="togglePassword"
                                        aria-label="Mostra o nascondi password">
                                        <i class="bi bi-eye text-muted"></i>
                                    </button>
                                </div>
                                <span class="invalid-input text-danger" id="invalid-password"></span>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-ae btn-ae-primary btn-lg btn-ae-pill w-100 py-3 mb-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Accedi
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="mb-0">Non hai ancora un account?
                                <a href="{{ route('register') }}" class="text-decoration-none fw-bold">Registrati ora</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection