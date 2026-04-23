@php
    $toasts = [];
    $seenToastKeys = [];

    $addToast = function ($message, $variant = 'info', $title = null, $icon = null) use (&$toasts, &$seenToastKeys) {
        if (!is_string($message)) {
            return;
        }

        $message = trim($message);
        if ($message === '') {
            return;
        }

        $key = $variant . '|' . $message;
        if (isset($seenToastKeys[$key])) {
            return;
        }

        $seenToastKeys[$key] = true;

        $defaultIcons = [
            'success' => 'bi-check-circle-fill',
            'danger' => 'bi-exclamation-triangle-fill',
            'warning' => 'bi-exclamation-circle-fill',
            'info' => 'bi-info-circle-fill',
            'secondary' => 'bi-bell-fill',
        ];

        $toasts[] = [
            'variant' => $variant,
            'title' => $title,
            'message' => $message,
            'icon' => $icon ?? ($defaultIcons[$variant] ?? 'bi-bell-fill'),
        ];
    };

    $flashMappings = [
        'success' => ['variant' => 'success', 'title' => 'Operazione riuscita'],
        'error' => ['variant' => 'danger', 'title' => 'Attenzione'],
        'warning' => ['variant' => 'warning', 'title' => 'Attenzione'],
        'info' => ['variant' => 'info', 'title' => 'Informazione'],
    ];

    foreach ($flashMappings as $key => $config) {
        $value = session($key);
        if (is_string($value) && trim($value) !== '') {
            $addToast($value, $config['variant'], $config['title']);
        }
    }

    $statusValue = session('status');
    if (is_string($statusValue) && trim($statusValue) !== '') {
        $statusMap = [
            'profile-updated' => [
                'variant' => 'success',
                'title' => 'Profilo',
                'message' => 'Il profilo e stato aggiornato con successo.',
            ],
            'password-updated' => [
                'variant' => 'success',
                'title' => 'Password',
                'message' => 'La password e stata aggiornata con successo.',
            ],
            'verification-link-sent' => [
                'variant' => 'info',
                'title' => 'Email di verifica',
                'message' => 'Ti abbiamo inviato un nuovo link di verifica via email.',
            ],
        ];

        if (isset($statusMap[$statusValue])) {
            $mappedStatus = $statusMap[$statusValue];
            $addToast($mappedStatus['message'], $mappedStatus['variant'], $mappedStatus['title']);
        } else {
            $addToast($statusValue, 'info', 'Informazione');
        }
    }

    $suppressValidationErrorToasts = request()->routeIs('project.create') || request()->routeIs('project.edit');

    if ($errors->any() && !$suppressValidationErrorToasts) {
        foreach ($errors->all() as $errorMessage) {
            $addToast($errorMessage, 'danger', 'Errore di validazione');
        }
    }
@endphp

<div class="toast-container app-toast-container position-fixed end-0 pe-3">
    <div id="favoriteToast" class="toast app-toast align-items-center border-0 text-bg-success" role="status"
        aria-live="polite" aria-atomic="true" data-bs-delay="2600" data-bs-autohide="true">
        <div class="d-flex">
            <div class="toast-body" id="favoriteToastMessage">Operazione completata.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Chiudi"></button>
        </div>
    </div>

    @foreach ($toasts as $toast)
        @php
            $isLightToast = $toast['variant'] === 'warning';
        @endphp
        <div class="toast app-toast js-session-toast align-items-center border-0 text-bg-{{ $toast['variant'] }}"
            role="status" aria-live="polite" aria-atomic="true" data-bs-delay="5200" data-bs-autohide="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-start gap-2">
                    <i class="bi {{ $toast['icon'] }} mt-1"></i>
                    <div>
                        @if (!empty($toast['title']))
                            <div class="fw-semibold">{{ $toast['title'] }}</div>
                        @endif
                        <div>{{ $toast['message'] }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close {{ $isLightToast ? '' : 'btn-close-white' }} me-2 m-auto"
                    data-bs-dismiss="toast" aria-label="Chiudi"></button>
            </div>
        </div>
    @endforeach
</div>

@if (!empty($toasts))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.bootstrap || !window.bootstrap.Toast) {
                return;
            }

            document.querySelectorAll('.js-session-toast').forEach(function (toastElement) {
                bootstrap.Toast.getOrCreateInstance(toastElement).show();
            });
        });
    </script>
@endif