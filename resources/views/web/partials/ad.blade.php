@php
    $t = fn ($v) => \App\Support\HomeContent::t($v);
@endphp
{{-- One advertising banner: $ad = ['image', 'theme', 'tag', 'title', 'text', 'cta', 'link'] (see config/storefront.php) --}}
<a class="ad theme-{{ $ad['theme'] ?? 'dark' }} {{ $class ?? '' }}" href="{{ \App\Support\HomeContent::link($ad['link'] ?? '') }}">
    <img class="bg" loading="lazy" src="{{ \App\Support\HomeContent::url($ad['image'] ?? null) }}" alt="">
    <div class="txt">
        @if(! empty($ad['tag']))<span class="tag">{{ $t($ad['tag']) }}</span>@endif
        <h5>{{ $t($ad['title'] ?? '') }}</h5>
        @if(! empty($ad['text']))<p class="mb-0 small">{{ $t($ad['text']) }}</p>@endif
        @if(! empty($ad['cta']))<span class="btn btn-accent btn-sm mt-2">{{ $t($ad['cta']) }}</span>@endif
    </div>
</a>
