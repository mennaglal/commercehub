<?php

namespace App\Support;

/**
 * The content of the home page (campaigns, banners, brands, editorial sections).
 *
 * TODAY it reads config/storefront.php (static pictures and texts).
 * LATER, when the admin panel manages them, only get() changes: read the banners from the database
 * (or from the API) and return the SAME array. The Blade views stay as they are.
 */
class HomeContent
{
    public static function get(): array
    {
        return config('storefront', []);
    }

    // A text ['ar' => '...', 'en' => '...'] in the current language (a plain string is returned as it is).
    public static function t(mixed $value): string
    {
        if (! is_array($value)) {
            return (string) $value;
        }

        return (string) ($value[app()->getLocale()] ?? $value['en'] ?? reset($value) ?? '');
    }

    // A picture path of public/ (or a full link) as a URL.
    public static function url(?string $path): string
    {
        if (! $path) {
            return asset('assets/web/img/placeholder.svg');
        }

        return str_starts_with($path, 'http') ? $path : asset($path);
    }

    // Links: a path of this website, or a full http(s) link. Anything else (for example "javascript:") becomes "#".
    public static function link(?string $link): string
    {
        $link = trim((string) $link);

        if ($link === '') {
            return '#';
        }

        if (str_starts_with($link, '/') && ! str_starts_with($link, '//')) {
            return url($link);
        }

        return preg_match('#^https?://#i', $link) ? $link : '#';
    }
}
