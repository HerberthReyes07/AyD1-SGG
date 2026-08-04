@props(['align' => 'end', 'width' => '48'])

@php
$alignmentClass = match ($align) {
    'left' => 'dropdown-menu-start',
    'top' => '',
    default => 'dropdown-menu-end',
};
@endphp

<div class="dropdown">
    <div data-bs-toggle="dropdown" aria-expanded="false" role="button" style="cursor: pointer;">
        {{ $trigger }}
    </div>

    <div class="dropdown-menu {{ $alignmentClass }} shadow">
        {{ $content }}
    </div>
</div>
