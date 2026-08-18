@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
$maxWidth = [
    'sm' => 'modal-sm',
    'md' => '',
    'lg' => 'modal-lg',
    'xl' => 'modal-xl',
    '2xl' => 'modal-xl',
][$maxWidth];
@endphp

<div
    class="modal fade"
    id="{{ $name }}"
    tabindex="-1"
    aria-labelledby="{{ $name }}Label"
    aria-hidden="true"
    data-bs-backdrop="true"
    data-bs-keyboard="true"
>
    <div class="modal-dialog {{ $maxWidth }} modal-dialog-centered">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>

@if ($show)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalEl = document.getElementById('{{ $name }}');
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        });
    </script>
@endif
