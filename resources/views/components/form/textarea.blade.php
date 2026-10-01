@props(['name', 'label' => null, 'value' => null, 'required' => false, 'hint' => null])
@php $id = $attributes->get('id', 'f_'.$name); @endphp
<div class="field {{ $attributes->get('wrapper-class') }}">
    @if ($label)
        <label for="{{ $id }}">{{ $label }} @if ($required)<span class="req">*</span>@endif</label>
    @endif
    <textarea id="{{ $id }}" name="{{ $name }}" @if ($required) required @endif
        {{ $attributes->except(['id', 'wrapper-class'])->class(['textarea', 'is-invalid' => $errors->has($name)]) }}>{{ old($name, $value) }}</textarea>
    @if ($hint)<span class="field__hint">{{ $hint }}</span>@endif
    @error($name)<span class="field__error">{{ $message }}</span>@enderror
</div>
