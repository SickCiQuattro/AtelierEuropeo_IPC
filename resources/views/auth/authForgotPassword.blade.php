@extends('layouts.master')

@section('title', 'AE - Recupero Password')

@section('body')
    <script>
        $(document).ready(function () {
            $("#forgot-password-form").submit(function (event) {
                var email = $("#email").val();
                var hasErrors = false;
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                $("#invalid-email").text("");

                if (email.trim() === '') {
                    hasErrors = true;
                    $("#invalid-email").text("L'indirizzo email non può essere vuoto.");
                } else if (!emailRegex.test(email)) {
                    hasErrors = true;
                    $("#invalid-email").text("Inserisci un indirizzo email valido.");
                }

                if (hasErrors) {
                    event.preventDefault();
                    $("#email").focus();
                }
            });

            $("#email").on('input', function () {
                $("#invalid-email").text("");
            });
        });
    </script>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h1 class="section-title mb-2" style="font-size: 3rem;">Recupero Password</h1>
                            <p class="section-subtitle mb-4">Inserisci l'indirizzo email associato al tuo account. Ti invieremo un link per creare una nuova password.</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 bg-success bg-opacity-10" role="alert">
                                <i class="bi bi-check-circle-fill me-2 text-success"></i>
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="forgot-password-form" method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" name="email" id="email"
                                    class="form-control form-control-lg border-0 shadow-sm rounded-3 @error('email') is-invalid @enderror"
                                    placeholder="Inserisci la tua email" value="{{ old('email') }}">
                                <span class="invalid-input text-danger" id="invalid-email"></span>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-ae btn-ae-primary btn-lg btn-ae-pill w-100 py-3 mb-3">
                                <i class="bi bi-envelope-paper me-2"></i>Invia link di recupero
                            </button>

                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none  small">
                                    <i class="bi bi-arrow-left me-1"></i>Torna al Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
