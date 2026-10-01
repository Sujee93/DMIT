@props(['title' => null, 'subtitle' => null, 'flush' => false])
<section {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title || isset($actions))
        <header class="card__header">
            <div>
                @if ($title)<h2>{{ $title }}</h2>@endif
                @if ($subtitle)<p>{{ $subtitle }}</p>@endif
            </div>
            @isset($actions)
                <div class="actions">{{ $actions }}</div>
            @endisset
        </header>
    @endif
    <div class="{{ $flush ? 'card__body card__body--flush' : 'card__body' }}">
        {{ $slot }}
    </div>
    @isset($footer)
        <footer class="card__footer">{{ $footer }}</footer>
    @endisset
</section>
