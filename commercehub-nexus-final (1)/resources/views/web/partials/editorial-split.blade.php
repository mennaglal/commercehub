@php
    $t = fn ($v) => \App\Support\HomeContent::t($v);
    $reverse = ! empty($split['reverse']);
@endphp
{{-- An editorial block: a picture and its content. $split: see config/storefront.php => editorial.splits --}}
<section class="page-section pt-0">
    <div class="container">
        <div class="edit-split">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-6 {{ $reverse ? 'order-lg-2' : '' }}">
                    <div class="pic"><img loading="lazy" src="{{ \App\Support\HomeContent::url($split['image'] ?? null) }}" alt=""></div>
                </div>
                <div class="col-lg-6 d-flex align-items-center">
                    <div class="body">
                        <span class="kicker">{{ $t($split['kicker'] ?? '') }}</span>
                        <h3>{{ $t($split['title'] ?? '') }}</h3>
                        @if(! empty($split['badge']))<div><span class="offer">{{ $t($split['badge']) }}</span></div>@endif
                        <p class="text-muted mb-0">{{ $t($split['text'] ?? '') }}</p>
                        @if(! empty($split['points']))
                            <ul>@foreach($split['points'] as $point)<li><i class="bi bi-check-circle-fill"></i>{{ $t($point) }}</li>@endforeach</ul>
                        @endif
                        <a class="btn btn-accent btn-lg" href="{{ \App\Support\HomeContent::link($split['link'] ?? '') }}">{{ $t($split['cta'] ?? '') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
