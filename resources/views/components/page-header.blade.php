@props(['title', 'subtitle' => null])
<div class="page-header">
    <div>
        @isset($breadcrumb)
            <div class="breadcrumb">{{ $breadcrumb }}</div>
        @endisset
        <h1>{{ $title }}</h1>
        @if ($subtitle)
            <p>{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="page-header__actions">{{ $actions }}</div>
    @endisset
</div>
