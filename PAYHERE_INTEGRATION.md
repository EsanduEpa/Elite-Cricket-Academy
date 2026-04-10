# PayHere Integration — Elite Cricket Academy

**Date written:** April 2026  
**Environment:** PHP MVC (custom framework), XAMPP, MySQL  
**Current mode:** Sandbox (test)

---

## 1. Overview

PayHere is a Sri Lankan payment gateway that accepts cards, bank transfers, and e-wallets. Payments are done via a server-generated HTML form that redirects the buyer to the PayHere hosted checkout page. After payment, PayHere calls back three URLs:

| Callback | Trigger | Method |
|---|---|---|
| `return_url` | User completes payment | Browser `GET` redirect |
| `cancel_url` | User clicks Cancel on PayHere | Browser `GET` redirect |
| `notify_url` | PayHere confirms/fails payment | Server-to-server `POST` |

The `notify_url` is the only one that should be trusted for order fulfilment — it is a direct server-to-server POST that the buyer cannot intercept or fake.

---

## 2. Credentials

> **Store credentials only in `app/libraries/PayHere.php`. Never repeat them in other files.**

| Key | Value |
|---|---|
| Sandbox Merchant ID | `1235084` |
| Sandbox Merchant Secret | `NDA0ODY0NzM4NjEwMzY1MzE1MDI1NzkyMDE4MTExOTc2NjI4ODk5` |
| Sandbox Gateway URL | `https://sandbox.payhere.lk/pay/checkout` |
| Live Gateway URL | `https://www.payhere.lk/pay/checkout` |

To go live: open `app/libraries/PayHere.php`, update `MERCHANT_ID`, `MERCHANT_SECRET`, and change `GATEWAY_URL` to point to `self::LIVE_URL`.

---

## 3. File Structure

```
app/
  libraries/
    PayHere.php                  ← Single source of truth: credentials + helpers
  controllers/
    Player.php                   ← payhere_checkout(), payhere_return(),
                                    payhere_cancel(), payhere_notify()
  views/
    player/
      cart.php                   ← Cart page (Checkout button triggers POST)
      payhere_gateway.php        ← Auto-submit form (spinner → PayHere)
      payhere_return.php         ← Success page (browser redirect)
      payhere_cancel.php         ← Cancel page (browser redirect)

public/
  js/player/
    cart.js                      ← submitToPayhere() builds hidden form, submits

payhere_test/                    ← Standalone sandbox test pages (not MVC)
  checkout.php                   ← Manual test checkout form
  return.php                     ← Test return page
  cancel.php                     ← Test cancel page
  notify.php                     ← Test server-to-server handler
  notify_log.txt                 ← Auto-created by notify.php (git-ignored)

payhere_notify_log.txt           ← Auto-created by Player::payhere_notify() (project root)
```

---

## 4. Shared Library — `app/libraries/PayHere.php`

This class is `require_once`'d by every file that needs PayHere. It provides:

### `PayHere::MERCHANT_ID`
The merchant ID string used in every form and hash.

### `PayHere::GATEWAY_URL`
The URL the checkout form posts to. Currently points to `SANDBOX_URL`. Switch to `LIVE_URL` for production.

### `PayHere::buildHash($orderId, $amount, $currency)`
Generates the checkout security hash required by PayHere.

**Formula:**
```
hash = strtoupper( md5( MERCHANT_ID + order_id + amount + currency + strtoupper(md5(MERCHANT_SECRET)) ) )
```

**Example:**
```php
$hash = PayHere::buildHash('ORDER-001', '500.00', 'LKR');
```

### `PayHere::verifyNotify(array $post)`
Verifies the server-to-server notification. Returns `true` only when:
1. `merchant_id` in POST matches `MERCHANT_ID`
2. The `md5sig` signature is valid (same hash formula but includes `status_code`)
3. `status_code` is `2` (payment captured/success)

**Notify hash formula:**
```
md5sig = strtoupper( md5( MERCHANT_ID + order_id + payhere_amount + payhere_currency + status_code + strtoupper(md5(MERCHANT_SECRET)) ) )
```

Uses `hash_equals()` for timing-safe comparison.

### `PayHere::log($logFile, $message)`
Appends a timestamped line to a log file. Used by both `payhere_test/notify.php` and `Player::payhere_notify()`.

---

## 5. Payment Flow (Production / Player Cart)

```
Player cart page
      │
      │  Clicks "Checkout"
      ▼
cart.js → submitToPayhere()
      │  Builds hidden HTML form
      │  POSTs cart_items (JSON) + cart_total to:
      ▼
Player::payhere_checkout()  [POST /player/payhere_checkout]
      │  Builds order_id = "ELITE-{playerID}-{timestamp}"
      │  Calls PayHere::buildHash()
      │  Stores order_id in $_SESSION['payhere_pending_order']
      │  Loads payhere_gateway.php view
      ▼
payhere_gateway.php
      │  Shows spinner
      │  Auto-submits hidden form to PayHere::GATEWAY_URL
      ▼
PayHere hosted checkout
      │
      ├─ User pays ──────────────────────────────────────────────────────┐
      │                                                                   │
      │  Browser redirect (GET)         Server POST (async)             │
      ▼                                 ▼                                │
Player::payhere_return()         Player::payhere_notify()               │
  Shows success page               Calls PayHere::verifyNotify()        │
  Clears localStorage cart         On success → log + mark order paid   │
                                   Always returns HTTP 200              │
      └─ User cancels ────────────────────────────────────────────────────┘
                │
                ▼
         Player::payhere_cancel()
           Shows cancel page with link back to cart
```

---

## 6. MVC Routes

All routes are handled by `Player.php` controller. The MVC router maps URL segments automatically:

| URL | Method | Description |
|---|---|---|
| `POST /player/payhere_checkout` | `Player::payhere_checkout()` | Generate hash, load gateway view |
| `GET /player/payhere_return` | `Player::payhere_return()` | Success page after payment |
| `GET /player/payhere_cancel` | `Player::payhere_cancel()` | Cancel page |
| `POST /player/payhere_notify` | `Player::payhere_notify()` | Server-to-server webhook |

---

## 7. Form Fields Sent to PayHere

| Field | Source | Notes |
|---|---|---|
| `sandbox` | hardcoded `1` | Remove / set to `0` for production |
| `merchant_id` | `PayHere::MERCHANT_ID` | |
| `return_url` | `URLROOT . '/player/payhere_return'` | |
| `cancel_url` | `URLROOT . '/player/payhere_cancel'` | |
| `notify_url` | `URLROOT . '/player/payhere_notify'` | Must be publicly accessible |
| `order_id` | `ELITE-{playerID}-{timestamp}` | Must be unique per payment |
| `items` | Cart item names + quantities | e.g. `Bat x1, Pads x2` |
| `currency` | `LKR` | |
| `amount` | Cart total (2 decimal places) | e.g. `1500.00` |
| `first_name` | Player name (first word) | |
| `last_name` | Player name (remaining words) | |
| `email` | Player email from session | |
| `phone` | Player phone or `0000000000` | |
| `address` | Player address or `N/A` | |
| `city` | `Colombo` | Hardcoded for now |
| `country` | `Sri Lanka` | |
| `hash` | `PayHere::buildHash(...)` | Security hash |

---

## 8. Notify Webhook (`payhere_notify`)

PayHere POSTs these fields to `notify_url`:

| Field | Description |
|---|---|
| `merchant_id` | Your merchant ID |
| `order_id` | The order ID you sent |
| `payhere_amount` | Amount PayHere processed |
| `payhere_currency` | Currency |
| `status_code` | `2`=success, `0`=pending, `-1`=cancel, `-2`=fail, `-3`=chargeback |
| `md5sig` | Signature to verify |

**Critical rules:**
- Always return HTTP 200. If you return anything else, PayHere retries.
- Never trust `payhere_amount` for order fulfilment — look up the amount from your own DB using `order_id`.
- Use `PayHere::verifyNotify($_POST)` which handles merchant ID check + hash check + status check in one call.

**Current behaviour:** Logs result to `payhere_notify_log.txt` in project root. The TODO comment marks where DB update code should go.

---

## 9. Standalone Test Pages (`payhere_test/`)

These are plain PHP files outside the MVC — accessible directly without routing. They are only for development testing.

| File | URL | Purpose |
|---|---|---|
| `checkout.php` | `http://localhost/Elite/payhere_test/checkout.php` | Manual test checkout |
| `return.php` | `http://localhost/Elite/payhere_test/return.php` | Test return page |
| `cancel.php` | `http://localhost/Elite/payhere_test/cancel.php` | Test cancel page |
| `notify.php` | `http://localhost/Elite/payhere_test/notify.php` | Test notify handler (logs to `notify_log.txt`) |

All credentials are pulled from `PayHere.php` — hardcoded test customer details (name, email, phone) are in `checkout.php`.

The `.htaccess` at project root includes a rule to bypass the MVC router for `payhere_test/`:
```apache
RewriteRule ^payhere_test/(.*)$ payhere_test/$1 [L]
```

---

## 10. Test Card (PayHere Sandbox)

| Field | Value |
|---|---|
| Card Number | `4916217501611292` |
| Expiry | Any future date (e.g. `12/28`) |
| CVV | Any 3 digits (e.g. `123`) |
| Name | Any name |

Other test cards are available in the PayHere sandbox documentation.

---

## 11. Session Variables Used

| Variable | Set by | Purpose |
|---|---|---|
| `$_SESSION['payhere_pending_order']` | `payhere_checkout()` | Stores current order_id |
| `$_SESSION['payhere_nonce']` | `cart()` and rotated by `payhere_checkout()` | Anti-CSRF token passed as hidden field in cart |

---

## 12. Going to Production Checklist

- [ ] Log in to live PayHere dashboard (`https://www.payhere.lk`) and get live `Merchant ID` and `Merchant Secret`
- [ ] Update `PayHere::MERCHANT_ID` and `PayHere::MERCHANT_SECRET` in `app/libraries/PayHere.php`
- [ ] Change `GATEWAY_URL` to `self::LIVE_URL` in `PayHere.php`
- [ ] Remove `'sandbox' => '1'` from the form fields in `payhere_gateway.php` (or set to `'0'`)
- [ ] Ensure `notify_url` is a **publicly accessible HTTPS URL** (PayHere cannot POST to localhost)
- [ ] Implement the DB update inside `Player::payhere_notify()` where the `// TODO` comment is
- [ ] Implement the DB update inside `payhere_test/notify.php` if also used in production
- [ ] Do not commit credentials to version control — move them to an `.env` file or server environment variables
- [ ] Remove or restrict access to `payhere_test/` in production

---

## 13. Adding PayHere to a New Module

To add PayHere payment to a new page (e.g. tournament entry fee, subscription payment):

1. **Build the order** in your controller and call:
   ```php
   require_once APPROOT . '/libraries/PayHere.php';
   $hash = PayHere::buildHash($orderId, $amount, 'LKR');
   ```

2. **Pass a `gateway` array** to the `player/payhere_gateway` view (same structure as the cart does — see `Player::payhere_checkout()`). Reuse that view directly.

3. **Return and cancel** pages can be reused (`player/payhere_return` and `player/payhere_cancel`), or create module-specific ones.

4. **Notify** is already handled by `Player::payhere_notify()`. Add a `switch` on `order_id` prefix (e.g. `ELITE-` vs `TRN-`) to route the fulfilment logic to the right module.
