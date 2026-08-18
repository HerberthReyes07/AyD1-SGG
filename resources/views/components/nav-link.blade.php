@props(['active' => false])

@php
$classes = $active ? 'nav-link text-nowrap active fw-semibold' : 'nav-link text-nowrap text-dark fw-semibold';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
