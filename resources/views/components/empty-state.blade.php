@props(['title', 'icon' => 'document'])
<div class="empty">
    <div class="empty__icon"><x-icon :name="$icon" /></div>
    <h3>{{ $title }}</h3>
    <p>{{ $slot }}</p>
    @isset($action)
        <div class="mt-2">{{ $action }}</div>
    @endisset
</div>
