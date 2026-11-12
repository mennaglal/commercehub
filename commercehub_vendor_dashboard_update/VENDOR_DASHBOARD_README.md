# CommerceHub — Vendor Dashboard

This update adds a web-based vendor panel that reuses the existing admin dashboard assets/layout foundation and the existing vendor product/order services.

## Routes

- `GET /vendor/login` — vendor login
- `POST /vendor/login` — authenticate vendor
- `GET /vendor` — overview dashboard
- `GET /vendor/products` — vendor products
- `GET /vendor/products/create` — create product
- `POST /vendor/products` — save product
- `GET /vendor/products/{product}/edit` — edit own product
- `PUT /vendor/products/{product}` — update own product
- `PATCH /vendor/products/{product}/status` — toggle own product visibility
- `DELETE /vendor/products/{product}` — delete own product when not referenced by protected records
- `GET /vendor/orders` — orders containing the vendor's items
- `GET /vendor/orders/{orderNumber}` — vendor-only invoice/details
- `GET /vendor/returns` — returns containing the vendor's items
- `GET /vendor/returns/{return}` — vendor-only return details
- `PATCH /vendor/returns/{return}/items/{item}/status` — update only this vendor's return-item status
- `GET /vendor/profile` — profile/store details
- `PUT /vendor/profile` — update own profile/store
- `POST /vendor/logout` — logout

## Installation into the full Laravel project

1. Merge the files in this archive into the same paths in the full CommerceHub project.
2. Run:

   ```bash
   php artisan migrate
   php artisan storage:link
   php artisan optimize:clear
   ```

3. Make sure the vendor has the Spatie `vendor` role and an associated store.
4. The store must be approved and active before the vendor can manage products, view orders, or view/update returns. The dashboard and profile remain accessible while the store is pending or suspended so the vendor can see their account state and edit profile details.
5. Sign in at `/vendor/login`.

## Security and business rules

- Vendor product queries are always scoped through the authenticated vendor's store. Product creation derives `store_id` from the authenticated vendor; it is never accepted from the form.
- Product edit, status changes and deletion first resolve the product through the authenticated vendor's store. Other vendors' product IDs return 404.
- Vendor order lists and details use `VendorOrderService`, which loads only `order_items.store_id` for the authenticated store. The invoice totals are calculated from those items only; order-level shipping, platform totals and other vendors' products are not exposed as the vendor's subtotal.
- The vendor has no order update/delete routes.
- Vendor return lists/details query only return items whose related order item belongs to the authenticated store. No return delete route is provided.
- A new migration adds `vendor_status`, `vendor_note`, and `vendor_processed_at` to `order_return_items`. These are per-item vendor workflow fields; they do not alter the platform's overall `order_returns.status`, refund processing, restocking, or other vendors' items.
- Vendor item status transitions are limited to `requested -> approved/rejected` and `approved -> received/rejected`.
- Profile updates operate only on the authenticated user's account and associated store.

## Important scope note

The uploaded `commercehub(4).zip` was a source subset: it did not contain the Laravel root files (`artisan`, `composer.json`, `bootstrap/`), installed dependencies, or the public dashboard asset directory. Therefore this archive is a complete source update against the supplied subset, but it is not a standalone runnable Laravel installation. The existing full project must supply its normal Laravel root files, dependencies, and `public/dashboard` assets.

PHP syntax checks passed for the included PHP application files. Laravel boot, route registration, Blade compilation, database migration execution, and browser testing could not be run from the supplied subset because its Laravel runtime/root files were not included.
