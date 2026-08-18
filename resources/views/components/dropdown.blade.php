@props(['align' => 'end', 'width' => '48', 'menuClass' => ''])

@php
$alignmentClass = match ($align) {
    'left' => 'dropdown-menu-start',
    'top' => '',
    default => 'dropdown-menu-end',
};

$widthClass = match ($width) {
    '48' => 'w-auto',
    default => $width,
};
@endphp

<div class="dropdown">
    <div data-bs-toggle="dropdown" aria-expanded="false" role="button" style="cursor: pointer;">
        {{ $trigger }}
    </div>

    <div class="dropdown-menu {{ $alignmentClass }} {{ $widthClass }} border-0 shadow-lg mt-2 p-2 rounded-3 {{ $menuClass }}">
        {{ $content }}
    </div>
</div>
