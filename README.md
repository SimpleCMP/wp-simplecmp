# SimpleCMP — WordPress Consent Manager

Open-source consent manager for WordPress. Auto-detects trackers, provides a consent banner with service toggles, blocks third-party embeds before consent, and maintains a DSGVO-compliant audit trail.

WordPress port of the [t3-simplecmp](https://github.com/SimpleCMP/t3-simplecmp) TYPO3 extension.

## Features

- **Consent Banner** — SimpleCMP JS bundle v0.4.1 with Bootstrap 5, Tailwind, Bulma, Pico themes
- **366 bundled service definitions** from `simplecmp/services-library`
- **4-State Detection Triage** — Curated / Recognised / Unknown / Dismissed
- **Universal Blocking** — Pre-consent HTML rewriting of third-party scripts, iframes, images, and stylesheets
- **Click-to-Enable Placeholders** — Contextual notices on blocked embeds with "Show once" / "Always show"
- **Service Registry** — Admin-curated services with vendor data, purposes, cookies, origins
- **Library Browser** — Browse, search, and bulk-adopt from 366 library services
- **Theme Designer** — Per-site banner colors, position, tone (Sie/Du), text overrides
- **Tracker Setup** — GA4, GTM, Matomo, Meta Pixel, Microsoft UET with Consent Mode v2
- **DSGVO Audit** — Append-only config snapshots, consent log, Art. 15 lookup, retention CLI
- **Multisite** — One install can serve as central triage for multiple sites

## Requirements

- WordPress 6.0+
- PHP 8.1+
- Composer

## Installation

```bash
composer require simplecmp/wp-simplecmp
```

Or manual:

1. Upload to `/wp-content/plugins/wp-simplecmp/`
2. Run `composer install --no-dev` in the plugin directory
3. Activate the plugin
4. Go to **SimpleCMP → Settings** and enable the plugin
5. Go to **SimpleCMP → Library** and adopt services for the banner

## Configuration

All settings are stored in `wp_options`:

```bash
wp option update simplecmp_enabled 1
wp option update simplecmp_storage_name 'my-site'
wp option update simplecmp_privacy_policy_url 'https://example.com/privacy'
wp option update simplecmp_imprint_url 'https://example.com/imprint'
wp option update simplecmp_universal_blocking_enabled 1
wp option update simplecmp_universal_blocking_allowlist 'cdn.example.com'
```

### Bridge Secret (Webhook Auth)

```bash
wp simplecmp generate-bridge-secret
```

Or add to `wp-config.php`:
```php
define('SIMPLECMP_BRIDGE_SECRET', 'your-64-char-hex-secret');
```

## API Endpoints

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | `/wp-json/simplecmp/v1/health` | Protocol health |
| `GET` | `/wp-json/simplecmp/v1/services` | Paginated registry |
| `POST` | `/wp-json/simplecmp/v1/lookup` | Batch classifier |
| `POST` | `/wp-json/simplecmp/webhook` | CMS bridge (HMAC) |
| `POST` | `/wp-json/simplecmp/v1/consent-log` | Visitor consent (HMAC) |
| `GET` | `/wp-json/simplecmp/v1/bridge-nonce` | Fresh HMAC nonce |

## WP-CLI Commands

```bash
wp simplecmp generate-bridge-secret
wp simplecmp snapshot-config --site=default
wp simplecmp audit-retention --target=all --keep-days=1095 --reason="DSGVO Art. 5 (1) (e)" --i-know-what-i-do
wp simplecmp export-audit --site=default --format=json
```

## Admin Pages

| Page | Purpose |
|---|---|
| **Detections** | 4-state triage: Curated / Recognised / Unknown / Dismissed |
| **Services** | Full registry index (Custom / From Library / Orphaned) |
| **Library** | Browse 366 bundled services, bulk adopt/unadopt |
| **Theme Designer** | CSS framework, colors, position, tone, text overrides |
| **Tracker Setup** | GA4, GTM, Matomo, Meta Pixel, Microsoft UET |
| **Audit Trail** | Append-only snapshots with line-diff |
| **DSGVO Auskunft** | Art. 15 visitor data lookup |
| **Settings** | Banner content, bridge secret, blocking config |

## How It Works

1. Admin adopts services from the bundled library (or via detection triage)
2. Frontend: SimpleCMP JS bundle renders consent banner with service toggles
3. Universal Blocker rewrites third-party `<script src>`, `<iframe>`, `<img>` to `data-*` + `about:blank` before page output
4. Blocked embeds show click-to-enable placeholder cards with service description
5. On consent, the engine restores original sources
6. CMS bridge webhook records detected cookies/origins for triage
7. Config changes are snapshot-audited (sha256-deduplicated, append-only)

## Test Shortcodes

```html
[simplecmp_test type="youtube|vimeo|googlemaps|spotify"]
[simplecmp_tracker type="ga4|facebook|hotjar"]
```

## Security

See the security audit summary in the repository. Key protections:

- HMAC-SHA256 with `hash_equals()` for webhook auth
- Bridge secret auto-generated on activation
- `wp_json_encode()` on tracker inline scripts (XSS prevention)
- SQL whitelist for dynamic ORDER BY clauses
- CSP header: **opt-in only** (disabled by default)
- Rate limiting on webhook, consent-log, and bridge-nonce endpoints

## License

BSD-3-Clause
