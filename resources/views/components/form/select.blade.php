@props(['name', 'label' => null, 'options' => [], 'value' => null, 'required' => false, 'placeholder' => null, 'hint' => null])
@php
    $id = $attributes->get('id', 'f_'.$name);
    $selected = (string) old($name, $value instanceof \BackedEnum ? $value->value : $value);
@endphp
<div class="field {{ $attributes->get('wrapper-class') }}">
    @if ($label)
        <label for="{{ $id }}">{{ $label }} @if ($required)<span class="req">*</span>@endif</label>
    @endif
    <select id="{{ $id }}" name="{{ $name }}" @if ($required) required @endif
        {{ $attributes->except(['id', 'wrapper-class'])->class(['select', 'is-invalid' => $errors->has($name)]) }}>
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($hint)<span class="field__hint">{{ $hint }}</span>@endif
    @error($name)<span class="field__error">{{ $message }}</span>@enderror
</div>
