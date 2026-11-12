# CommerceHub

A multi-vendor e-commerce platform built with **Laravel**. CommerceHub brings together an online storefront, a customer account area, vendor tools, an administration dashboard, and a REST API in one application.

The platform is designed around three main user groups:

- **Customers** browse products and stores, manage their accounts, place orders, and request returns.
- **Vendors** manage their stores and products and follow orders related to their stores.
- **Administrators** manage the platform, users, vendors, catalog, orders, content, settings, and access permissions.

---

## Contents

- [Platform overview](#platform-overview)
- [Main features](#main-features)
- [Technology stack](#technology-stack)
- [Screenshots](#screenshots)
- [Application architecture](#application-architecture)
- [Project structure](#project-structure)
- [API overview](#api-overview)
- [Database domains](#database-domains)
- [Business workflows](#business-workflows)
- [Localization](#localization)
- [Installation](#installation)
- [Useful commands](#useful-commands)
- [Testing](#testing)
- [Notes for developers](#notes-for-developers)

---

## Platform overview

CommerceHub is a multi-vendor commerce application with separate experiences for the public website, customers, vendors, and administrators.

| Area | Responsibility |
|---|---|
| Public storefront | Product discovery, store browsing, informational pages, cart and checkout screens |
| Customer account | Profile, orders, returns, and notifications |
| Vendor area | Store profile, product management, dashboard, and vendor orders |
| Admin dashboard | Central management of the marketplace and its business data |
| REST API | Data and operations consumed by the storefront and external clients |

The website frontend is implemented with Laravel Blade views and JavaScript. The API is organized separately under `routes/api.php`, with dedicated controllers, request validation, middleware, and service classes.

## Main features

### 1. Storefront and catalog

- Home page and product shop.
- Browse product categories and stores.
- Product details using readable slugs.
- Store details using readable slugs.
- Product catalog endpoints with filtering/validation handled by dedicated API request classes.
- Arabic and English product names and descriptions.
- Product images and image URL accessors.
- Simple products and products with configurable variants.
- Product availability, stock, and sale pricing.

### 2. Customer accounts

- Customer registration and login.
- Token-based API authentication.
- View and update profile information.
- Upload a profile avatar.
- Change password.
- Manage customer country, city, and area information.
- Become a vendor through the vendor application flow.
- Logout and revoke the current API token.

### 3. Cart, checkout, and orders

- Cart and checkout pages on the storefront.
- Checkout preview endpoint to validate and calculate an order before submission.
- Create orders and view order history.
- View order details using an order number.
- Cancel an eligible order.
- Store customer and shipping details with the order.
- Keep shipping-related values as order snapshots so historical orders retain the values used at purchase time.
- Track order, payment, and delivery statuses independently.
- Export orders from the administration dashboard.

Order and payment states are represented explicitly in the `Order` model, including allowed status transitions. The application also defines supported payment methods and gateway identifiers in the order domain.

### 4. Multi-vendor functionality

- Vendor registration/application from an existing customer account.
- Vendor store profile and store information management.
- Store location information.
- Vendor approval and active-state checks.
- Vendor dashboard statistics.
- Vendor product listing and management.
- Product status activation/deactivation.
- Product variant types and values.
- Vendor order listing and order details.
- Vendor API access is scoped through vendor middleware.

**Approval flow:** a vendor can access and update their store profile while the store is awaiting approval. Vendor dashboard, product, and order operations require an approved and active vendor store.

### 5. Product variants

The catalog supports both simple products and products with variants.

- Define reusable variant types (for example, size or color).
- Define values for each variant type.
- Attach variant types to products.
- Store individual variant combinations, prices, discount prices, and quantities.
- Use the product's `has_variants` flag to distinguish simple products from variant-based products.

### 6. Administration dashboard

The admin area is served under the `/admin` prefix and includes management screens for:

- Dashboard.
- Admin users.
- Customers and vendors.
- Roles and permissions.
- Categories and subcategories.
- Products and product variants.
- Orders and order status updates.
- Returns.
- Countries, cities, and areas.
- Currencies.
- Coupons and coupon categories.
- Bloggers.
- Notifications and notification broadcasts.
- Static/content pages.
- Contact messages.
- Settings.

Access to protected admin actions is controlled through role/permission middleware where configured.

### 7. Coupons and blogger commissions

- Create and manage coupon codes.
- Support percentage and fixed-value discounts.
- Configure validity dates, usage limits, and minimum order amounts.
- Restrict coupons to selected categories (including their subcategories).
- Support blogger-linked coupons.
- Calculate blogger commission on the eligible products after the customer discount.
- Track commission status on order data.

Coupon validation and calculations are centralized in `CouponService` and are intended to run inside the order database transaction.

### 8. Returns

- Customers can list their return requests.
- Retrieve return details by return number.
- Check which order items are eligible for return.
- Submit a return request for an order.
- Administrators can review and manage return requests.

### 9. Notifications and push notifications

- Store in-app notifications in the database.
- Customer notification listing and read-related UI/API functionality.
- Admin notification broadcasts.
- Target an individual user, customers, vendors, or all customers and vendors.
- Arabic and English notification title/body content.
- Optional push delivery through Firebase Cloud Messaging (FCM).
- Register and manage device tokens.
- Queue broadcast delivery instead of sending to a large audience during the web request.
- Process recipients in batches and remove invalid/expired device tokens reported by Firebase.

Push delivery requires Firebase credentials and queue workers to be configured.

### 10. Locations and currencies

- Country → city → area hierarchy.
- Area-level shipping information.
- Customer and store location data.
- Currency management and a public currencies endpoint.

### 11. Content and contact

- Manage informational pages such as About, Privacy Policy, and Return Policy.
- Retrieve pages by slug through the API.
- Public contact form endpoint.
- Admin inbox for submitted contact messages.
- Blogger/content management screens.

---

## Technology stack

The repository confirms the following technologies and packages by usage in the application code:

| Technology | Usage |
|---|---|
| PHP / Laravel | Backend application, routing, controllers, models, validation, migrations |
| Laravel Blade | Server-rendered website and admin dashboard |
| JavaScript | Storefront interactions and API-driven pages |
| Laravel Sanctum | API token authentication |
| Spatie Laravel Permission | Roles and permissions |
| Eloquent ORM | Database models and relationships |
| Laravel Queues / Jobs | Background notification delivery |
| Firebase Cloud Messaging | Mobile/web push notification delivery |
| Pest / PHPUnit test structure | Automated feature and unit tests |

The exact framework and package versions should be taken from the project's `composer.json` and lock file.

---

## Screenshots

The following screenshots showcase the main modules of the CommerceHub administration dashboard.

### 1. Admin Dashboard

![Dashboard 1](docs/screenshots/dashboard1.png)
![Dashboard 2](docs/screenshots/dashboard2.png)
![Dashboard 3](docs/screenshots/dashboard3.png)
![Dashboard 4](docs/screenshots/dashboard4.png)
![Dashboard 5](docs/screenshots/dashboard5.png)
![Dashboard 6](docs/screenshots/dashboard6.png)
![Dashboard 7](docs/screenshots/dashboard7.png)
![Dashboard 8](docs/screenshots/dashboard8.png)
![Dashboard 11](docs/screenshots/dashboard11.png)

### 2. Product Variants Management

![Product Variant Types 1](docs/screenshots/variantype3.png)
![Product Variant Types 2](docs/screenshots/variantype4.png)

### 3. Product Management

![Products 1](docs/screenshots/product1.png)
![Products 2](docs/screenshots/product2.png)
![Products 3](docs/screenshots/product3.png)
![Products 4](docs/screenshots/product4.png)
![Products 5](docs/screenshots/product5.png)
![Products 6](docs/screenshots/product6.png)

### 4. Users & Access Management

![Users](docs/screenshots/user.png)

#### Roles & Permissions

![Roles](docs/screenshots/role.png)
![Roles Management](docs/screenshots/role2.png)
![Permissions](docs/screenshots/permission.png)
![Permissions Management](docs/screenshots/permission2.png)

### 5. Vendor Management

![Vendors](docs/screenshots/vendor.png)

### 6. Currency Management

![Currencies](docs/screenshots/currenct.png)

### 7. Coupon Management

![Coupons](docs/screenshots/coupon.png)

### 8. Order Management

![Orders 1](docs/screenshots/order1.png)
![Orders 2](docs/screenshots/order2.png)
![Orders 3](docs/screenshots/order3.png)

### 9. Returns & Refunds Management

![Returns 1](docs/screenshots/return1.png)
![Returns 2](docs/screenshots/return2.png)
![Returns 3](docs/screenshots/return3.png)
![Returns 4](docs/screenshots/return4.png)
![Returns 5](docs/screenshots/return5.png)
![Returns 6](docs/screenshots/return6.png)

### 10. Notifications Management

![Notifications](docs/screenshots/notification.png)

## Application architecture

The codebase uses a feature-oriented Laravel structure with a clear separation between HTTP handling and business operations.

### Request lifecycle

1. A request enters through a route in `routes/web.php`, `routes/admin.php`, or `routes/api.php`.
2. Middleware handles concerns such as authentication, active-account checks, vendor approval, and locale.
3. Form Request classes validate incoming data.
4. Controllers coordinate the HTTP request and response.
5. Service classes contain reusable business operations.
6. Eloquent models and relationships handle persistence.
7. API responses use a consistent response envelope documented in the API routes.

### Service layer

Business logic is grouped into dedicated services, including:

| Service | Responsibility |
|---|---|
| `AuthService` | Authentication operations |
| `CatalogService` | Catalog-related operations |
| `ProductService` | Product business logic |
| `VendorProductService` | Vendor product operations |
| `VendorService` | Vendor/store operations |
| `OrderService` | Order creation and core order processing |
| `CustomerOrderService` | Customer order operations |
| `VendorOrderService` | Vendor-facing order operations |
| `CouponService` | Coupon validation, discounts, and blogger commissions |
| `ReturnService` | Return request operations |
| `ProfileService` | Customer profile operations |
| `CurrencyService` | Currency-related operations |
| `NotificationService` | In-app notifications and broadcasts |
| `PushService` | Firebase push delivery |
| `ImageUploader` | Image upload handling |

### Middleware

The application includes middleware for:

- Setting the website locale.
- Applying the API locale from the `Accept-Language` header.
- Forcing JSON responses for API requests.
- Blocking inactive users.
- Ensuring the authenticated user is a vendor.
- Requiring an approved vendor store for protected vendor operations.

---

## Project structure

```text
app/
├── Console/Commands/       # Custom Artisan commands
├── Exports/                # Data exports
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin dashboard controllers
│   │   ├── Api/             # REST API controllers
│   │   │   └── Vendor/      # Vendor API controllers
│   │   └── Web/             # Storefront controllers
│   ├── Middleware/          # Authentication, locale, vendor checks
│   └── Requests/            # Form Request validation
├── Jobs/                    # Queued background jobs
├── Models/                  # Eloquent models
├── Notifications/           # Laravel notification classes
└── Services/                # Business logic

database/
├── factories/
├── migrations/
└── seeders/

resources/
└── views/
    ├── admin/               # Admin dashboard Blade views
    └── web/                 # Public storefront and account views

routes/
├── admin.php                # Admin dashboard routes
├── api.php                  # REST API routes
├── web.php                  # Main web routes
└── website.php              # Storefront routes (if included by web.php)

public/
├── assets/web/              # Storefront CSS, JavaScript, and images
└── dashboard/               # Admin dashboard assets

tests/
├── Feature/
└── Unit/
```

> Some folders may contain additional files not listed above. This tree highlights the main application boundaries.

---

## API overview

All API routes are defined in `routes/api.php`.

### Response format

The API route documentation specifies a shared response structure:

```json
{
  "success": true,
  "message": "Request completed successfully",
  "data": {},
  "errors": null
}
```

The actual `data` value depends on the endpoint. Validation and failure responses should be handled according to the API response implementation.

### Language selection

Send the desired language using:

```http
Accept-Language: en
```

or:

```http
Accept-Language: ar
```

### Public endpoints

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/register` | Register a customer |
| POST | `/api/login` | Login |
| GET | `/api/countries` | List countries |
| GET | `/api/countries/{country}/cities` | List cities in a country |
| GET | `/api/cities/{city}/areas` | List areas in a city |
| GET | `/api/currencies` | List currencies |
| GET | `/api/categories` | List categories |
| GET | `/api/products` | Browse products |
| GET | `/api/products/{slug}` | Product details |
| GET | `/api/stores` | Browse stores |
| GET | `/api/stores/{slug}` | Store details |
| GET | `/api/pages` | List content pages |
| GET | `/api/pages/{slug}` | Page details |
| POST | `/api/contact` | Submit a contact message |

### Authenticated customer endpoints

These endpoints require a valid Sanctum bearer token and an active user account.

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/logout` | Logout current token |
| GET | `/api/profile` | Get profile |
| PUT | `/api/profile` | Update profile |
| POST | `/api/profile/avatar` | Upload avatar |
| PUT | `/api/profile/password` | Change password |
| POST | `/api/become-vendor` | Submit vendor application |
| POST | `/api/checkout/preview` | Preview checkout calculations |
| GET | `/api/orders` | List customer orders |
| POST | `/api/orders` | Create an order |
| GET | `/api/orders/{orderNumber}` | Get order details |
| POST | `/api/orders/{orderNumber}/cancel` | Cancel an eligible order |
| GET | `/api/orders/{orderNumber}/returnable-items` | Get returnable order items |
| POST | `/api/orders/{orderNumber}/returns` | Submit a return request |
| GET | `/api/returns` | List customer returns |
| GET | `/api/returns/{returnNumber}` | Get return details |

### Vendor endpoints

Vendor endpoints require authentication and vendor authorization. Store profile endpoints are available to vendors awaiting approval; the dashboard, products, and orders require an approved and active store.

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/vendor/store` | Get vendor store |
| PUT / POST | `/api/vendor/store` | Update vendor store |
| GET | `/api/vendor/dashboard` | Vendor dashboard |
| GET | `/api/vendor/variant-types` | List available variant types |
| GET | `/api/vendor/products` | List vendor products |
| POST | `/api/vendor/products` | Create product |
| GET | `/api/vendor/products/{product}` | Get product |
| PUT / POST | `/api/vendor/products/{product}` | Update product |
| PATCH | `/api/vendor/products/{product}/status` | Toggle product status |
| DELETE | `/api/vendor/products/{product}` | Delete product |
| GET | `/api/vendor/orders` | List vendor orders |
| GET | `/api/vendor/orders/{orderNumber}` | Get vendor order details |

For authenticated requests, include the token:

```http
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
Accept-Language: en
```

The route definitions are the source of truth for endpoint availability and middleware requirements.

---

## Database domains

The main Eloquent models indicate the following data domains:

| Domain | Main models |
|---|---|
| Users and access | `User` and Spatie roles/permissions |
| Stores | `Store` |
| Catalog | `Category`, `Product` |
| Product options | `VariantType`, `VariantValue`, `ProductVariant` |
| Orders | `Order`, `OrderItem` |
| Returns | `OrderReturn`, `OrderReturnItem` |
| Promotions | `Coupon`, `Blogger` |
| Locations | `Country`, `City`, `Area` |
| Currency | `Currency` |
| Notifications | `NotificationBroadcast`, `DeviceToken` |
| Content and support | `Page`, `ContactMessage` |

Database changes are versioned in `database/migrations`.

---

## Business workflows

### Customer order flow

1. The customer browses the public catalog.
2. The customer adds products to the cart in the storefront.
3. The client sends checkout details to the checkout preview endpoint.
4. The API validates the request and returns the calculated preview.
5. The customer submits the order.
6. The backend processes the order and its items.
7. The customer can retrieve the order, follow its status, or request cancellation/return when eligible.

### Vendor onboarding flow

1. A customer submits a vendor application.
2. The vendor manages the initial store information.
3. An administrator reviews and approves the store.
4. Once approved and active, the vendor can access the dashboard, products, variants, and orders.

### Notification broadcast flow

1. An administrator creates a broadcast and selects its audience.
2. The broadcast is stored in the database.
3. A background job processes the recipients in batches.
4. In-app notifications are stored for the selected users.
5. If push delivery is enabled, Firebase Cloud Messaging sends device notifications in batches.
6. Invalid or expired device tokens are removed.

---

## Localization

The application supports Arabic and English in the website and API.

- Website language is managed through the locale/session flow.
- The API reads `Accept-Language` (`ar` or `en`).
- Product and category data include bilingual fields.
- Notification broadcasts store Arabic and English titles and message bodies.
- Localized text is selected when building API and push notification responses.

---

## Installation

> The uploaded source snapshot used to prepare this documentation did not include the root dependency manifests (`composer.json` / `composer.lock`). Use the versions and required extensions from the actual project repository when installing dependencies.

### Requirements

- PHP version compatible with the Laravel version used by the project.
- Composer.
- MySQL or another database supported by the application configuration.
- A web server or local PHP development environment.
- Node.js and npm if frontend assets need to be built.
- Firebase project credentials if push notifications are enabled.

### Setup

1. Clone the repository:

   ```bash
   git clone YOUR_REPOSITORY_URL
   cd YOUR_PROJECT_DIRECTORY
   ```

2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Create the environment file:

   ```bash
   cp .env.example .env
   ```

   On Windows Command Prompt:

   ```bat
   copy .env.example .env
   ```

4. Generate the application key:

   ```bash
   php artisan key:generate
   ```

5. Configure the database and other environment values in `.env`.

6. Run migrations:

   ```bash
   php artisan migrate
   ```

7. Create the storage link if the application uses the public storage disk:

   ```bash
   php artisan storage:link
   ```

8. Start the local server:

   ```bash
   php artisan serve
   ```

9. If the project has frontend build scripts, install and build the assets:

   ```bash
   npm install
   npm run build
   ```

### Queue and push notification setup

Queued notification delivery requires a working queue connection and a running worker:

```bash
php artisan queue:work
```

Configure the Firebase credentials required by the Firebase Laravel package before enabling push delivery. Do not commit service-account JSON files, private keys, or production credentials to Git.

---

## Useful commands

```bash
# Run database migrations
php artisan migrate

# Run the test suite
php artisan test

# Run the queue worker
php artisan queue:work

# Clear cached application configuration (when troubleshooting)
php artisan optimize:clear

# Create a new migration
php artisan make:migration create_example_table
```

A custom push test command is present in the application:

```bash
php artisan push:test
```

Check the command's required options and environment configuration before running it.

---

## Testing

The repository contains feature and unit tests, including coverage areas for:

- Authentication and registration.
- Admin product functionality.
- Database safety.
- Product price calculations.

Run the available tests with:

```bash
php artisan test
```

---

## Notes for developers

- Keep business rules in the relevant service classes rather than duplicating them across controllers.
- Use Form Request classes for validating incoming data.
- Keep customer and vendor authorization checks on the server; frontend visibility is not an authorization mechanism.
- Vendor product and order queries must remain scoped to the authenticated vendor's store.
- Preserve order snapshots for historical prices, discounts, shipping, and commission calculations.
- Run notification and push delivery through queues rather than sending to large audiences in the HTTP request.
- Keep API responses and localized messages consistent.
- Never commit `.env`, API tokens, Firebase credentials, or other secrets.

---

## Project status

This README documents the modules and routes found in the current source snapshot. For exact dependency versions, environment variables, payment-provider configuration, and deployment-specific settings, refer to the corresponding configuration and deployment files in the complete repository.
