@props(['active' => false])

@php
$classes = $active ? 'nav-link active fw-semibold' : 'nav-link text-dark fw-semibold';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
