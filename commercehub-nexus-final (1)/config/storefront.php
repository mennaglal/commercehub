<?php

/*
|--------------------------------------------------------------------------
| The content of the HOME PAGE (campaigns, banners, brands, editorial sections ...)
|--------------------------------------------------------------------------
| For now everything is STATIC: the pictures are in public/assets/web/img/banners/ and the texts are here.
| Later the admin panel will manage them: only App\Support\HomeContent::get() has to read the database
| instead of this file. The Blade views do not change (they use the same array).
|
| Every text is  ['ar' => '...', 'en' => '...']. Every picture is a path inside public/.
| 'link' is a path of the website (or a full https:// link).
*/

$img = fn (string $name) => 'assets/web/img/banners/' . $name;

return [

    /* ===== Hero campaigns: the big slider. layout: start | end | center   theme: light | dark ===== */
    'hero' => [
        [
            'image' => $img('hero-summer.svg'), 'layout' => 'start', 'theme' => 'light',
            'badge' => ['ar' => 'حملة الصيف', 'en' => 'Summer campaign'],
            'title' => ['ar' => 'تخفيضات الصيف', 'en' => 'The Summer Sale'],
            'accent' => ['ar' => 'خصم يصل إلى 50%', 'en' => 'up to 50% off'],
            'text' => ['ar' => 'اختر من آلاف المنتجات بأسعار خاصة لفترة محدودة.', 'en' => 'Thousands of products at special prices for a limited time.'],
            'cta' => ['ar' => 'تسوّق العروض', 'en' => 'Shop the sale'], 'link' => '/shop?on_sale=1',
        ],
        [
            'image' => $img('hero-night.svg'), 'layout' => 'end', 'theme' => 'dark',
            'badge' => ['ar' => 'موسم الاحتفالات', 'en' => 'Festive season'],
            'title' => ['ar' => 'احتفل بأناقة', 'en' => 'Celebrate in style'],
            'accent' => ['ar' => 'هدايا وعروض مميزة', 'en' => 'gifts and special offers'],
            'text' => ['ar' => 'أفكار هدايا لكل الأذواق ولكل الميزانيات.', 'en' => 'Gift ideas for every taste and every budget.'],
            'cta' => ['ar' => 'اكتشف الهدايا', 'en' => 'Find a gift'], 'link' => '/shop',
        ],
        [
            'image' => $img('hero-season.svg'), 'layout' => 'start', 'theme' => 'dark',
            'badge' => ['ar' => 'وصل حديثًا', 'en' => 'Just landed'],
            'title' => ['ar' => 'الموسم الجديد وصل', 'en' => 'The new season is here'],
            'accent' => ['ar' => 'تشكيلات جديدة', 'en' => 'fresh collections'],
            'text' => ['ar' => 'كن أول من يشاهد أحدث المنتجات من متاجرنا.', 'en' => 'Be the first to see the latest products from our stores.'],
            'cta' => ['ar' => 'شاهد الجديد', 'en' => 'See what is new'], 'link' => '/shop?sort=newest',
        ],
        [
            'image' => $img('hero-mega.svg'), 'layout' => 'center', 'theme' => 'dark',
            'badge' => ['ar' => 'أكبر حدث', 'en' => 'The big event'],
            'title' => ['ar' => 'الجمعة الكبرى', 'en' => 'Mega Friday'],
            'accent' => ['ar' => 'أكبر خصومات العام', 'en' => 'the biggest discounts of the year'],
            'text' => ['ar' => 'لا تفوّت العروض قبل أن تنتهي.', 'en' => 'Do not miss the deals before they end.'],
            'cta' => ['ar' => 'ابدأ الآن', 'en' => 'Start now'], 'link' => '/shop?on_sale=1',
        ],
    ],

    /* ===== The two promo tiles beside the slider (desktop) ===== */
    'side_promos' => [
        ['image' => $img('promo-1.svg'), 'theme' => 'light', 'title' => ['ar' => 'وفّر أكتر', 'en' => 'Save more'], 'text' => ['ar' => 'اكتشف عروض اليوم', 'en' => 'Discover today\'s offers'], 'link' => '/shop?on_sale=1'],
        ['image' => $img('promo-2.svg'), 'theme' => 'dark', 'title' => ['ar' => 'متاجر موثوقة', 'en' => 'Trusted stores'], 'text' => ['ar' => 'تصفّح كل البائعين', 'en' => 'Browse all vendors'], 'link' => '/stores'],
    ],

    /* ===== Advertising banners between the sections ===== */
    'ads' => [
        // 3 banners in a row
        'strip_1' => [
            ['image' => $img('ad-gift.svg'), 'theme' => 'light', 'tag' => ['ar' => 'هدايا', 'en' => 'Gifts'], 'title' => ['ar' => 'أفكار هدايا', 'en' => 'Gift ideas'], 'text' => ['ar' => 'لكل مناسبة', 'en' => 'For every occasion'], 'link' => '/shop'],
            ['image' => $img('ad-audio.svg'), 'theme' => 'dark', 'tag' => ['ar' => 'جديد', 'en' => 'New'], 'title' => ['ar' => 'عالم الصوت', 'en' => 'Sound & audio'], 'text' => ['ar' => 'سماعات وأكثر', 'en' => 'Headphones and more'], 'link' => '/shop'],
            ['image' => $img('ad-tech.svg'), 'theme' => 'light', 'tag' => ['ar' => 'خصم', 'en' => 'Offer'], 'title' => ['ar' => 'تكنولوجيا ذكية', 'en' => 'Smart tech'], 'text' => ['ar' => 'أحدث الأجهزة', 'en' => 'The latest devices'], 'link' => '/shop?on_sale=1'],
        ],
        // one wide banner
        'wide' => [
            ['image' => $img('ad-wide.svg'), 'theme' => 'dark', 'tag' => ['ar' => 'عرض نهاية الأسبوع', 'en' => 'Weekend deal'], 'title' => ['ar' => 'عروض لا تفوّت', 'en' => 'Deals you cannot miss'], 'text' => ['ar' => 'أسعار خاصة على منتجات مختارة', 'en' => 'Special prices on selected products'], 'cta' => ['ar' => 'تسوّق الآن', 'en' => 'Shop now'], 'link' => '/shop?on_sale=1'],
        ],
        // 2 banners in a row
        'strip_2' => [
            ['image' => $img('ad-home.svg'), 'theme' => 'light', 'tag' => ['ar' => 'المنزل', 'en' => 'Home'], 'title' => ['ar' => 'جدّد بيتك', 'en' => 'Refresh your home'], 'text' => ['ar' => 'ديكور وأثاث بأسعار مناسبة', 'en' => 'Decor and furniture at good prices'], 'link' => '/shop'],
            ['image' => $img('ad-beauty.svg'), 'theme' => 'dark', 'tag' => ['ar' => 'العناية', 'en' => 'Beauty'], 'title' => ['ar' => 'جمالك أولًا', 'en' => 'Beauty first'], 'text' => ['ar' => 'منتجات عناية مختارة', 'en' => 'Selected care products'], 'link' => '/shop'],
        ],
    ],

    /* ===== Shop by brand (the link searches the name; later it can open a brand page) ===== */
    'brands' => [
        ['name' => 'NOVA', 'logo' => $img('brand-nova.svg')], ['name' => 'AURA', 'logo' => $img('brand-aura.svg')],
        ['name' => 'ZENITH', 'logo' => $img('brand-zenith.svg')], ['name' => 'LUMA', 'logo' => $img('brand-luma.svg')],
        ['name' => 'KAIRO', 'logo' => $img('brand-kairo.svg')], ['name' => 'ORBIT', 'logo' => $img('brand-orbit.svg')],
        ['name' => 'VELA', 'logo' => $img('brand-vela.svg')], ['name' => 'ATLAS', 'logo' => $img('brand-atlas.svg')],
    ],

    /* ===== Editorial sections: pictures + offers + content ===== */
    'editorial' => [
        // a mosaic: one big tile + two small tiles
        'mosaic' => [
            'kicker' => ['ar' => 'اختيارات المحرر', 'en' => 'Editor\'s picks'],
            'title' => ['ar' => 'اختر أسلوبك', 'en' => 'Style it your way'],
            'tiles' => [
                ['image' => $img('edit-fashion.svg'), 'size' => 'big', 'badge' => ['ar' => 'خصم 40%', 'en' => '40% off'], 'title' => ['ar' => 'عالم الموضة', 'en' => 'The fashion edit'], 'text' => ['ar' => 'إطلالات الموسم من متاجر مختارة', 'en' => 'This season\'s looks from selected stores'], 'link' => '/shop'],
                ['image' => $img('edit-tech.svg'), 'size' => 'small', 'badge' => ['ar' => 'جديد', 'en' => 'New'], 'title' => ['ar' => 'تقنية ذكية', 'en' => 'Smart tech'], 'text' => ['ar' => 'أجهزة تسهّل يومك', 'en' => 'Devices that make your day easier'], 'link' => '/shop'],
                ['image' => $img('edit-home.svg'), 'size' => 'small', 'badge' => ['ar' => 'حتى 30%', 'en' => 'Up to 30%'], 'title' => ['ar' => 'بيت أجمل', 'en' => 'A nicer home'], 'text' => ['ar' => 'لمسات تغيّر المكان', 'en' => 'Touches that change a place'], 'link' => '/shop'],
            ],
        ],
        // a split: picture + content (the second one is reversed)
        'splits' => [
            [
                'image' => $img('edit-gifts.svg'), 'reverse' => false,
                'kicker' => ['ar' => 'دليل الهدايا', 'en' => 'The gift guide'],
                'title' => ['ar' => 'الهدية المثالية موجودة هنا', 'en' => 'The perfect gift is here'],
                'text' => ['ar' => 'اخترنا لك أفكارًا تناسب كل شخص وكل ميزانية، من متاجر موثوقة.', 'en' => 'We picked ideas for every person and every budget, from trusted stores.'],
                'points' => [['ar' => 'أفكار لكل الميزانيات', 'en' => 'Ideas for every budget'], ['ar' => 'متاجر موثوقة', 'en' => 'Trusted stores'], ['ar' => 'استرجاع سهل', 'en' => 'Easy returns']],
                'badge' => ['ar' => 'عروض حتى 40%', 'en' => 'Offers up to 40%'],
                'cta' => ['ar' => 'اكتشف الهدايا', 'en' => 'Explore gifts'], 'link' => '/shop',
            ],
            [
                'image' => $img('edit-beauty.svg'), 'reverse' => true,
                'kicker' => ['ar' => 'الجمال والعناية', 'en' => 'Beauty & care'],
                'title' => ['ar' => 'روتينك اليومي يبدأ من هنا', 'en' => 'Your daily routine starts here'],
                'text' => ['ar' => 'منتجات عناية وجمال مختارة بعناية لتبدو وتشعر بأفضل حال.', 'en' => 'Carefully selected beauty and care products to look and feel your best.'],
                'points' => [['ar' => 'منتجات مختارة', 'en' => 'Hand-picked products'], ['ar' => 'أسعار منافسة', 'en' => 'Competitive prices'], ['ar' => 'توصيل لباب بيتك', 'en' => 'Delivered to your door']],
                'badge' => ['ar' => 'جديد هذا الأسبوع', 'en' => 'New this week'],
                'cta' => ['ar' => 'تسوّق العناية', 'en' => 'Shop beauty'], 'link' => '/shop',
            ],
        ],
    ],

    /* ===== Curated collections (tabs): what each tab asks the API for ===== */
    'collections' => [
        'new' => ['sort' => 'newest'],
        'trending' => ['sort' => 'trending'],
        'best' => ['sort' => 'best_sellers'],
    ],
];
