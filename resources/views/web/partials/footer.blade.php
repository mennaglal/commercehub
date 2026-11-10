@php
    $isAr = app()->getLocale() === 'ar';
    $site = config('website');
    $social = ['facebook' => 'bi-facebook', 'instagram' => 'bi-instagram', 'tiktok' => 'bi-tiktok', 'x' => 'bi-twitter-x', 'youtube' => 'bi-youtube'];
@endphp

<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="brand text-white mb-3">
                    <img src="{{ asset($site['logo']) }}" alt="" width="40" height="40"> {{ $site['name'][$isAr ? 'ar' : 'en'] }}
                </div>
                <p>{{ __('web.brand.description') }}</p>
                <div class="social">
                    @foreach($social as $key => $icon)
                        @if(! empty($site['social'][$key]))
                            <a href="{{ $site['social'][$key] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $key }}"><i class="bi {{ $icon }}"></i></a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h6>{{ __('web.footer.shop') }}</h6>
                <ul>
                    <li><a href="{{ route('site.shop') }}">{{ __('web.nav.shop') }}</a></li>
                    <li><a href="{{ route('site.stores') }}">{{ __('web.nav.stores') }}</a></li>
                    <li><a href="{{ route('site.cart') }}">{{ __('web.nav.cart') }}</a></li>
                    <li><a href="{{ route('site.become-vendor') }}">{{ __('web.nav.become_vendor') }}</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-3">
                <h6>{{ __('web.footer.help') }}</h6>
                <ul>
                    <li><a href="{{ route('site.page', $site['pages']['about']) }}">{{ __('web.footer.about') }}</a></li>
                    <li><a href="{{ route('site.page', $site['pages']['returns']) }}">{{ __('web.footer.returns') }}</a></li>
                    <li><a href="{{ route('site.page', $site['pages']['privacy']) }}">{{ __('web.footer.privacy') }}</a></li>
                    <li><a href="{{ route('site.page', $site['pages']['terms']) }}">{{ __('web.footer.terms') }}</a></li>
                    <li><a href="{{ route('site.contact') }}">{{ __('web.nav.contact') }}</a></li>
                </ul>
            </div>

            <div class="col-lg-3">
                <h6>{{ __('web.nav.contact') }}</h6>
                <ul>
                    <li><i class="bi bi-geo-alt me-2"></i>{{ $site['contact']['address'][$isAr ? 'ar' : 'en'] }}</li>
                    <li><i class="bi bi-telephone me-2"></i><a href="tel:{{ preg_replace('/\s+/', '', $site['contact']['phone']) }}" dir="ltr">{{ $site['contact']['phone'] }}</a></li>
                    <li><i class="bi bi-envelope me-2"></i><a href="mailto:{{ $site['contact']['email'] }}">{{ $site['contact']['email'] }}</a></li>
                    <li><i class="bi bi-clock me-2"></i>{{ $site['contact']['hours'][$isAr ? 'ar' : 'en'] }}</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-wrap justify-content-between gap-2">
            <span>&copy; {{ date('Y') }} {{ $site['name'][$isAr ? 'ar' : 'en'] }}. {{ __('web.footer.rights') }}</span>
            <span><i class="bi bi-shield-lock me-1"></i>{{ __('web.footer.secure') }}</span>
        </div>
    </div>
</footer>
