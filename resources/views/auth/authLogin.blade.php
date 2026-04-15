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
        });
    </script>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h1 class="section-title mb-2" style="font-size: 3rem;">Bentornato!</h1>
                            <p class="section-subtitle mb-0">Accedi al tuo account per continuare</p>
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
                                <label for="password" class="form-label fw-bold">Password</label>
                                <input type="password" name="password" id="password"
                                    class="form-control form-control-lg border-0 shadow-sm rounded-3 @error('password') is-invalid @enderror"
                                    placeholder="Inserisci la tua password">
                                <span class="invalid-input text-danger" id="invalid-password"></span>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
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