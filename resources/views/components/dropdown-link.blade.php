@props(['active' => false])

@php
$classes = $active
    ? 'dropdown-item rounded-2 fw-semibold active'
    : 'dropdown-item rounded-2 fw-medium';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
