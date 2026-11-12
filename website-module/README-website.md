# The storefront (public website)

Bootstrap 5.3 (RTL build for Arabic) + jQuery 3.7 + vanilla JavaScript + one CSS file. No build step (no npm).
Blade only draws the page shell (header, footer, SEO tags, language). The data comes from the API you already have
(/api/...), called from the browser with jQuery. Arabic / English and the currency switch work on every page.

## 1) Install
1. Copy the folders into the project (config, app, routes, lang, resources, public). Same paths.
2. routes/web.php:
   - DELETE the old  Route::get('/', function () { return view('welcome'); });
   - ADD at the end:  require __DIR__ . '/website.php';
   (keep your /change-language/{locale} route: the language button uses it)
3. routes/api.php: the cart summary must work for visitors who are not logged in yet.
   Move this ONE line out of the  Route::middleware(['auth:sanctum', ...])  group, to the PUBLIC part:
       Route::post('/checkout/preview', [OrderController::class, 'preview'])->name('checkout.preview')->middleware('throttle:60,1');
4. php artisan optimize:clear
5. Open the site. Edit config/website.php (name, logo, phone, social links, payment methods ...).
   If your admin login is also /login, see the note at the top of routes/website.php.
The site needs the api-module of the previous step (catalog, orders, returns, vendor...). The notifications page and the
bell need the notifications-module; without it they hide themselves / show "not available".

## 2) What is inside
    config/website.php                    name, logo, contact, social, payment methods, bank details, page slugs
    routes/website.php                    all the pages (language middleware = your SetLocale)
    app/Http/Controllers/Web/SiteController.php   product / store / page (real 404 + SEO tags), and the numbers in the URL
    lang/en/web.php, lang/ar/web.php      ALL the texts (Blade: __('web.x.y'), JS: Shop.t('x.y'))
    resources/views/web/                  layout, partials (header, footer, mobile bar, menus), pages, account/, vendor/
    public/assets/web/css/site.css        the design (colors in :root)
    public/assets/web/js/core.js          API client, auth, currency, cart, toast, product card, pagination ...
    public/assets/web/js/pages/*.js       one file per page

## 3) The pages
| Page | URL | API used |
|---|---|---|
| Home | / | categories, products (deals, new), stores |
| Shop (search, filters, sort, pages) | /shop?q=&category_id=&min_price=... | categories, products |
| Product (options, stock, related) | /products/{slug} | products/{slug}, products |
| Stores / one store | /stores, /stores/{slug} | stores, products?store_id= |
| Cart | /cart | checkout/preview (prices from the server) |
| Checkout, order done | /checkout, /order-success/{n} | countries/cities/areas, checkout/preview, orders |
| Login / Register | /login, /register | login, register |
| Profile (data, photo, password) | /account | profile, profile/avatar, profile/password |
| My orders, one order (cancel, return) | /account/orders, /account/orders/{n} | orders, orders/{n}/cancel |
| Return request / my returns | /account/orders/{n}/return, /account/returns | returnable-items, returns |
| Notifications | /account/notifications | notifications (notifications-module) |
| Become a vendor | /become-vendor | become-vendor |
| Vendor: dashboard, products (+ variants form), orders, my store | /vendor/... | vendor/* |
| About, Privacy, Return policy, Terms | /pages/{slug} | pages/{slug} (written in the admin panel) |
| Contact us | /contact | contact |

## 4) How the main things work
- Language: the Arabic/English button calls /change-language/{ar|en} (session). <html lang dir>, the Bootstrap RTL CSS,
  the fonts and ALL the texts follow it. The JS sends  Accept-Language  to the API, so the API answers in the same language.
- Currency: the API prices are in EGP. The currency menu (GET /api/currencies) only changes how prices are SHOWN
  (price / rate_to_egp), instantly, without reloading. The order is always charged in EGP and a small note says so.
- Cart: kept in the browser (localStorage). The server never trusts it: every time the cart or the checkout opens,
  POST /checkout/preview returns the real prices, the coupon discount, the shipping of the area and the total,
  and reports a product that is not available or has no enough stock.
- Login: the Sanctum token is kept in localStorage; a 401 answer sends the visitor to /login and brings him back.
  Pages of the account and of the vendor are guarded in the browser and, more important, by the API.
- Vendor: the store starts as "pending". Until the admin approves it the vendor sees a clear message instead of the pages.
  The product form is the admin one: simple product, or variants (tick the values, Generate, price / stock per row).
- Return: the customer chooses the quantity of each item; the server checks the delivery, the return period, the
  remaining quantity and calculates the refund (after the coupon discount).

## 5) Security
- Every text that comes from the API is escaped before it is put in the page (Shop.esc / .text()).
- The HTML of the admin pages (About, Privacy ...) is cleaned in the browser first (allowed tags only, no scripts, no
  event attributes, only http/https/mailto/tel links).
- The redirect after login accepts only a path of this website (no "open redirect").
- No price, shipping, user id or status is ever sent as a trusted value: the API calculates them.
- The token in localStorage can be stolen by an XSS bug: that is why nothing is written with innerHTML without escaping.
  For production add a Content-Security-Policy header (script-src 'self' + the CDNs used in layouts/app.blade.php).
- Bootstrap, jQuery, icons and fonts come from CDNs (pinned versions). To work offline or to use SRI, download them into
  public/ and change the links in resources/views/web/layouts/app.blade.php.

## 6) The design and how to change it
Modern marketplace style (like the big shopping sites): a thin top bar, a header with a big search box and account / cart
buttons, a strip of categories with a big "All categories" menu, a slider with two promo tiles, a bar with the 4 promises,
product cards with a discount badge and a full width button, a product page with a price box, tabs and a sticky buy bar
on phones, a bottom navigation bar on phones, and a dark footer.

Colors (top of public/assets/web/css/site.css, in :root):
    --light-primary-color: #ffd176;     light gold: hover, highlights
    --primary-color: #d3a54b;           gold: the "buy" buttons, search button, badges, lines
    --secondary-color: #1D3557FF;       navy: texts, headings, the dark buttons, top bar, footer
The shades (--primary-dark, --primary-soft, --secondary-dark, --secondary-soft) are made from them, and the red of the discount
badge is --sale. Text on the gold uses the navy (good contrast). Change the three variables and the whole site follows.
The fonts are Poppins (English) and Tajawal (Arabic). After editing the CSS/JS change 'version' in config/website.php.

## 7) Not included (next steps)
- Online payment: choose "online" in config/website.php only after the gateway (Fatura, Tabby ...) creates the invoice;
  until then the order is saved with payment pending and no invoice link.
- Browser push notifications (Firebase for the web), wishlist, product reviews, sitemap.xml.
- The product and category lists are drawn by JavaScript. The product and store pages already have their title, description
  and picture in the HTML (for Google and the share previews). Server-side rendering of the lists can be added later if SEO needs it.

## 8) Quick test
1. Open / : categories, deals and stores appear. Switch AR/EN and the currency.
2. Add a product, open /cart (prices from the server), add a coupon, go to /checkout (it asks for login).
3. Choose country > city > area: the shipping appears. Place a cash-on-delivery order.
4. /account/orders: open the order, cancel it (before shipping). In the admin panel mark one order delivered, then
   request a return from /account/orders/{n}/return.
5. Become a vendor, approve the store from the admin panel, add a product with variants.
