@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null])
@php
    $id = $attributes->get('id', 'f_'.str_replace(['[', ']', '.'], '_', $name));
    $key = str_replace(['[', ']'], ['.', ''], $name);
@endphp
<div class="field {{ $attributes->get('wrapper-class') }}">
    @if ($label)
        <label for="{{ $id }}">{{ $label }} @if ($required)<span class="req">*</span>@endif</label>
    @endif
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password' && $type !== 'file') value="{{ old($key, $value) }}" @endif
        @if ($required) required @endif
        {{ $attributes->except(['id', 'wrapper-class'])->class(['input', 'is-invalid' => $errors->has($key)]) }}
    >
    @if ($hint)<span class="field__hint">{{ $hint }}</span>@endif
    @error($key)<span class="field__error">{{ $message }}</span>@enderror
</div>
