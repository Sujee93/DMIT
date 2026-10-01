@props(['label', 'value', 'icon' => 'chart', 'tone' => 'blue', 'hint' => null, 'href' => null])
<{{ $href ? 'a' : 'div' }} @if ($href) href="{{ $href }}" @endif class="card stat">
    <div class="stat__icon tone-{{ $tone }}"><x-icon :name="$icon" /></div>
    <div>
        <div class="stat__label">{{ $label }}</div>
        <div class="stat__value">{{ $value }}</div>
        @if ($hint)<div class="stat__hint">{{ $hint }}</div>@endif
    </div>
</{{ $href ? 'a' : 'div' }}>
