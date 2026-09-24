# Shiprocket Checkout (Fastrr) Integration

Fastrr one-click checkout for this Laravel store. It is a **separate integration** from
Shiprocket Shipping (`ShiprocketService`), with its own credentials, host, and auth. Shipping code
is not modified. Checkout orders are created as normal Laravel orders and then handed to the
existing Shipping service.

Official sources used (nothing beyond them is assumed):

- Integration guide: https://docs.google.com/document/d/1uEcKW0uPAldhKiFCqJbAQnAmBTP6Azvlt_Rcxu5BW_0
- API documentation: https://documenter.getpostman.com/view/25617008/2sB34bL3ig

## Flow

```
Customer ─ Buy Now / Checkout ─► Laravel (POST /shiprocket-checkout/token)
    Laravel ─ signed POST /api/v1/access-token/checkout ─► Shiprocket Checkout ─► token
Browser ─ HeadlessCheckout.addToCart(event, token, {fallbackUrl}) ─► Fastrr iframe (address, UPI/COD)
Shiprocket ─ POST order webhook ─► /api/fastrr/webhook/order/{secret}
    Laravel ─ signed POST /api/v1/custom-platform-order/details ─► verified order
    Laravel order created ─► existing ShiprocketService::createShipment() ─► AWB / tracking
Browser ◄─ redirect_url?oid=…&ost=SUCCESS ─ Laravel verifies again, then shows the order success page
```

If anything fails (integration OFF, keys missing, API error, script not loaded, stale CSRF), the
browser goes to the **native checkout** instead. For Buy Now, that's a signed URL that adds the item
to the cart and opens `/checkout`.

## Files

| File | Purpose |
|---|---|
| `config/services.php` → `shiprocket_checkout` | Env overrides + official hosts/scripts per environment |
| `app/Services/ShiprocketCheckoutService.php` | Credentials, HMAC, signed requests, access token, order details, test connection, status |
| `app/Services/ShiprocketCheckoutCatalog.php` | Builds the documented catalog JSON from existing products, variants, and categories |
| `app/Services/ShiprocketCheckoutCatalogSync.php` | Sends product/collection update webhooks after the response (`defer()`) |
| `app/Services/ShiprocketCheckoutOrderProcessor.php` | Verify → create order (idempotent) → Shipping + emails |
| `app/Http/Controllers/ShiprocketCheckoutController.php` | Storefront: token, return URL, native fallback |
| `app/Http/Controllers/Api/ShiprocketCheckoutCatalogController.php` | Fetch Products / by Collection / Collections |
| `app/Http/Controllers/Api/ShiprocketCheckoutWebhookController.php` | Order webhook |
| `app/Http/Controllers/Admin/ShiprocketCheckoutController.php` | Test Connection, rotate webhook URL |
| `app/Console/Commands/ShiprocketCheckoutReconcile.php` | Failsafe for missed webhooks (scheduled every 10 min) |
| `app/Models/ShiprocketCheckoutVariant.php`, `ShiprocketCheckoutOrder.php` | Mapping + checkout sessions |
| `resources/views/admin/settings/partials/shiprocket-checkout.blade.php` | Admin tab (status, settings, endpoints, guide) |
| `resources/views/frontend/partials/shiprocket-checkout.blade.php` | Loads the official script; intercepts Buy Now / cart Checkout |
| `tests/Feature/ShiprocketCheckoutTest.php` | 16 feature tests |

Existing files changed:

- `SettingController::store()`: one encryption mechanism for secret settings (also used by the Engage password), plus validation of Checkout values.
- `AppServiceProvider`: model event hooks for catalog sync.
- `layouts/app.blade.php`: includes the launcher partial.
- Cart page and cart drawer: `data-shiprocket-checkout="cart"` on the checkout buttons.
- `routes/*.php`, `.env.example`.

## Database

Migration `2026_09_24_100000_create_shiprocket_checkout_tables`:

- **`shiprocket_checkout_variants`**: `id` **is** the `variant_id` sent to Shiprocket. The unique `variant_key` `p{product}-v{variant|0}` covers real variants and products without variants (they get one "Default Title" variant). Product and variant IDs are never changed. The mapping is created on demand and stays stable.
- **`shiprocket_checkout_orders`**: one row per checkout session.
  - `checkout_order_id` is unique; `order_id` links to the Laravel order.
  - Also stores status, payment, the verified `details`, `last_error`, and a `browser_token` (used to log a guest back in on return).

Settings live in the existing `settings` table (group `shiprocket_checkout`):

| Key | Notes |
|---|---|
| `shiprocket_checkout_enabled` | master ON/OFF |
| `shiprocket_checkout_on_buy_now`, `shiprocket_checkout_on_cart` | where to launch |
| `shiprocket_checkout_push_to_shipping` | hand orders to Shiprocket Shipping |
| `shiprocket_checkout_api_key`, `shiprocket_checkout_secret_key` | **encrypted** with `Crypt` (APP_KEY), never rendered |
| `shiprocket_checkout_environment` | `production` / `staging` |
| `shiprocket_checkout_base_url` | optional https override |
| `shiprocket_checkout_webhook_token` | secret path segment of the webhook URL |
| `shiprocket_checkout_last_*` | status panel (test, error, catalog fetch, webhook) |

`.env` (`SHIPROCKET_CHECKOUT_API_KEY`, `_SECRET_KEY`, `_ENV`, `_BASE_URL`, `_TIMEOUT`) overrides the admin values.

## Authentication / HMAC

```
X-Api-Key: <API key>
X-Api-HMAC-SHA256: base64( hmac_sha256( <exact JSON body>, <secret key> ) )
```

- The body is JSON-encoded once, signed, and sent with `withBody()`. The signed bytes are the sent bytes.
- The guide's prose shows `X-Api-Key: Bearer <key>`, but every official request example (guide curl and Postman) sends the bare key, so the bare key is used.
- Hosts:
  - Production: `https://checkout-api.shiprocket.com`
  - Staging: `https://fastrr-api-dev.pickrr.com`
- Test Connection uses the read-only **Order List** API (`POST /api/v1/custom-platform-order/details/list`).
  - HTTP 511 = invalid key/HMAC. Confirmed against the live API with dummy keys.

## Catalog endpoints (called by Shiprocket)

| Name | URL |
|---|---|
| Fetch Products | `GET {APP_URL}/api/fastrr/products?page=1&limit=100` |
| Fetch Products by Collection | `GET {APP_URL}/api/fastrr/products/collection?collection_id=ID&page=1&limit=100` |
| Fetch Collections | `GET {APP_URL}/api/fastrr/collections?page=1&limit=100` |

Response format is `{"data": {"total": N, "products"|"collections": [...]}}`, with every documented
field present (blank string when empty), integer IDs, and `limit` capped at 250.

Field mapping:

| Documented field | Source in this store |
|---|---|
| collections | categories |
| `title` | name |
| `body_html` | description |
| `vendor` | brand name, or `APP_NAME` |
| `product_type` | category name |
| `tags` | category and subcategory names |
| `status` | `is_active` → `active` / `draft` |
| `price` | sale price |
| `compare_at_price` | MRP |
| `quantity` | stock (variant stock when variants exist) |
| `grams` / `weight` (`kg`) | parsed from the weight text using the existing parser |
| images | absolute URLs |

Real-time sync: saving a product, variant (including stock changes), or category sends
`POST /wh/v1/custom/product` or `/wh/v1/custom/collection` after the response. It only runs while
the integration is active, and runs at most once per product per request.

## Access token / launch

- `POST /shiprocket-checkout/token`: CSRF-protected, throttled to 20/min.
  - Body: `{source: "buy_now", product_id, variant_id, quantity}` or `{source: "cart"}`.
  - Sends the documented `cart_data.items[{variant_id, quantity}]`, `redirect_url`, and `timestamp`.
  - Duplicate variant IDs are merged, because the docs require unique variants.
- The frontend loads the official `shopify.js` and `shopify.css` (production or staging) plus `<input id="sellerDomain">` (the APP_URL host).
- It then calls `HeadlessCheckout.addToCart(event, token, {fallbackUrl})`.
- Native coupons from the cart are not passed to Fastrr. Discounts are applied inside Shiprocket Checkout.

## Order webhook

- URL: `POST {APP_URL}/api/fastrr/webhook/order/{secret}`. The docs define no signature for this webhook, so:
  1. A wrong secret gets a 404.
  2. `order_id` is validated.
  3. The order is **re-fetched from the signed Order Details API**. The webhook payload is never trusted for amounts, items, or status.
- Only `status = SUCCESS` creates an order. `CREATED`, `INITIATED`, and `FAILED` are recorded only.
- Idempotent: a unique `checkout_order_id` plus a `lockForUpdate` transaction. Repeated webhooks, the redirect, and the reconcile job never create duplicates.
- Always responds with HTTP 200, as the docs expect.
- The order is created as follows:
  - **Customer:** matched by email, then phone; otherwise created. Without an email, a non-deliverable `@checkout.invalid` placeholder is used, because `users.email` is required.
  - **Payment:** `payment_method` is `cod` for `CASH_ON_DELIVERY`, else `shiprocket_checkout`. Status is `paid` only for a prepaid order with `payment_status = Success`.
  - **Items:** order items at current prices. Stock is deducted and never blocks, because the customer has already paid.
  - **Totals:** from the verified details.
  - **Records:** a `payments` row per `payments[]` entry, a tracking entry, and an activity log entry.
- After creation:
  - `ShiprocketService::createShipment($order)` runs, if Shipping is configured and "Send orders to Shiprocket Shipping" is on. The same pattern is used by `OrderService`.
  - The customer and admin emails are sent.

Redirect: `GET /shiprocket-checkout/return?oid=…&ost=SUCCESS` verifies the order the same way.
- The cart is cleared (cart source only).
- A guest is logged in only if the session's `browser_token` matches the one that started the checkout.
- Then the page redirects to `checkout.success`.

## Local development

```bash
php artisan migrate
php artisan serve                      # http://127.0.0.1:8000
ngrok http 8000                        # public HTTPS for Shiprocket → your machine
# .env: APP_URL=https://xxxx.ngrok-free.app
php artisan config:clear
```

- Test Connection and the popup work without a tunnel. Only the calls Shiprocket makes to you (catalog, webhook) need it.
- Without a tunnel, orders are still created on the redirect back.

Tests (the project's migrations are MySQL-only):

```bash
mysql -uroot -e "CREATE DATABASE rohidafarm_srtest"
DB_DATABASE=rohidafarm_srtest php artisan migrate
DB_CONNECTION=mysql DB_DATABASE=rohidafarm_srtest php artisan test --filter=ShiprocketCheckout
```

On the default SQLite test run, these tests are skipped with a message.

## Production

1. `php artisan migrate`.
2. `APP_URL=https://yourdomain`, then `php artisan config:cache`.
3. Scheduler cron: `* * * * * php /path/artisan schedule:run >> /dev/null 2>&1`. This runs `shiprocket-checkout:reconcile` every 10 minutes (missed webhooks, 30-minute backoff per session).
4. Enter the keys in Admin → Settings → Shiprocket Checkout, **Test Connection**, then share the Custom Endpoints with Shiprocket.
5. After turning the integration ON, clear the page cache (Admin → Clear Cache). Public pages are cached for up to 1 hour. Turning it OFF is effective at once, because the token endpoint refuses and the browser falls back.

## Security

- The secret only ever feeds `hash_hmac` on the server.
  - It is never logged: API errors log path, status, and message only.
  - It is never returned in JSON responses, never rendered in Blade, and never put in `VITE_*` variables.
- Keys are encrypted at rest. Blank form fields keep the saved value, and "Remove" clears it.
- Admin routes use the existing `auth` + `admin` + `permission:settings` middleware. Settings and test calls are CSRF-protected.
- Validation: environment must be `production` or `staging`, the base URL must be `https`, and webhook `order_id` must match `[A-Za-z0-9_-]{1,64}`. The catalog `page`/`limit` values are integers, with `limit` capped at 250.
- The webhook URL secret is compared with `hash_equals` and can be rotated from the admin tab.
- The catalog APIs are public but read-only and throttled (120/min). They expose only what the storefront already shows.

## Troubleshooting

| Symptom | Cause / fix |
|---|---|
| Test: HTTP 511 / 401 | Wrong key or secret, or the wrong environment for these keys |
| Popup never opens | Integration OFF or keys missing (check **Last Error**); clear the page cache after enabling |
| Paid order missing | Webhook URL not registered, or the cron isn't running; run `php artisan shiprocket-checkout:reconcile` |
| `unknown variant_id` in the log | Shiprocket has a stale catalog; ask them to re-sync |
| Double shipments | Shiprocket also books shipments on its side; untick "Send orders to Shiprocket Shipping" |
| Keys stop working | `APP_KEY` changed; re-enter the keys |
