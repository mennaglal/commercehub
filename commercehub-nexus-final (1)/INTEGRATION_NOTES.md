# CommerceHub + Nexus storefront integration

## What changed

- Replaced the public website Blade layout with a CommerceHub-branded storefront using the Nexus design system.
- Added Nexus CSS, Bootstrap, Bootstrap Icons, the local icon fonts, storefront JavaScript, and Nexus product/hero images under `public/assets/nexus/`.
- Removed the public layout's dependency on the former `assets/web/css/site.css` and legacy website JavaScript files.
- Rebuilt the public customer-facing views around the Nexus visual system: home, shop, product detail, stores, store detail, cart, checkout, login, registration, account, orders, order details, returns, contact, CMS pages, and vendor-facing storefront pages.
- Added `/categories`, `/deals`, and `/saved` routes.
- Connected the storefront to the supplied CommerceHub API routes for catalog, categories, stores, authentication, profile, cart checkout preview, orders, returns, vendor store, vendor products, vendor orders, and vendor dashboard.
- Cart lines are stored locally as product/variant IDs and quantities only. Prices, shipping, coupon discounts, stock and order totals are recalculated by CommerceHub on the server.
- Kept admin views, admin routes, API controllers, models, services, and database schema unchanged.

## Important limits in the supplied source archive

The supplied `commercehub(5).zip` is a partial source export. It does not contain the Laravel project root files (`artisan`, `composer.json`, `composer.lock`), the installed `vendor/` directory, `.env`, the original `public/` directory, or the existing `assets/web` logo and upload assets. Therefore this archive is an integrated source package, not a self-contained runnable Laravel installation. Apply these changes to the complete local CommerceHub checkout and keep its existing runtime files, `.env`, uploads, and original public assets.

The customer notifications API is not present in the supplied `routes/api.php`; the new page reports that limitation rather than fabricating notification data. The vendor product editor now supports simple products and variant combinations using the provided variant-type API. Payment methods and online gateways are read from CommerceHub configuration; only enabled methods are shown.

## Validation performed

- PHP syntax lint passed for the supplied PHP files after the changes.
- `node --check` passed for `public/assets/nexus/js/storefront.js`.
- A live Laravel/database/browser checkout test could not be run because the supplied archive does not include the Laravel runtime dependencies, `.env`, or a complete original public directory.
