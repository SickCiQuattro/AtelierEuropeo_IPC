@props([
    'homeUrl' => null,
])
@php
    $resolvedHomeUrl = $homeUrl ?: route('home');
@endphp

<nav aria-label="breadcrumb" class="ae-breadcrumb-nav">
    <ol class="breadcrumb ae-breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ $resolvedHomeUrl }}" aria-label="Home">
                <i class="bi bi-house-door-fill home-icon"></i>
            </a>
        </li>
        {{ $slot }}
    </ol>
</nav>