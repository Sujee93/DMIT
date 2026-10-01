@props(['name', 'label', 'checked' => false, 'hint' => null])
<div class="field {{ $attributes->get('wrapper-class') }}">
    <input type="hidden" name="{{ $name }}" value="0">
    <label class="checkbox">
        <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $checked)) {{ $attributes->except('wrapper-class') }}>
        <span>{{ $label }}</span>
    </label>
    @if ($hint)<span class="field__hint">{{ $hint }}</span>@endif
    @error($name)<span class="field__error">{{ $message }}</span>@enderror
</div>
