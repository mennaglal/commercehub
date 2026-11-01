CommerceHub - Order module update

Included changes:
- Orders can now contain products from multiple stores.
- Store ownership/name are saved on each order_items row.
- Removed order-level store assumptions from the admin order listing/details.
- Added shipping country/city/area snapshots and delivery instructions.
- Added order_status_histories with order, delivery, and payment status events.
- Updated admin order create/details/list views and request validation.

Installation:
1. Copy the contents of this folder into the matching paths in your CommerceHub project.
2. Review the changed files before replacing your local files; keep a backup.
3. Run: php artisan migrate
4. Clear caches if needed: php artisan optimize:clear

Important:
- Existing orders retain their current shipping_address and shipping_total values.
- The new shipping location fields are optional snapshots; shipping_total remains the existing shipping amount field.
- The uploaded archive did not include CouponService.php, config/orders.php, language files, or the full Laravel application. The existing coupon service is still called by OrderService, but its behavior and full application integration could not be executed or verified here.
- PHP syntax validation passed for all PHP files in this package. A full Laravel feature/database test run requires the complete CommerceHub application and database configuration.
