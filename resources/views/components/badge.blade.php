@props(['tone' => 'muted'])
<span {{ $attributes->merge(['class' => 'badge badge-'.$tone]) }}>{{ $slot }}</span>
