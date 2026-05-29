# Security checklist — Golden Pangolin

What this branch ships, why it matters, and what you still have to do by hand.

## What's now enforced in code

| Control | Where | What it does |
|---|---|---|
| Security headers | `app/Http/Middleware/SecurityHeaders.php` | Adds HSTS (prod only), X-Frame-Options=SAMEORIGIN, X-Content-Type-Options=nosniff, Referrer-Policy=strict-origin-when-cross-origin, a Permissions-Policy that locks down camera/mic/FLoC, and a Content-Security-Policy that whitelists the existing CDNs (jsdelivr, cdnjs, unpkg, Google Fonts, Stripe). |
| HTTPS scheme | `app/Providers/AppServiceProvider.php` | `URL::forceScheme('https')` in production so generated links don't downgrade. |
| Trusted proxy | `app/Http/Middleware/TrustProxies.php` | `$proxies = '*'` so DirectAdmin's reverse proxy forwards the right scheme/IP — otherwise HSTS and `secure` cookies break behind a proxy. |
| Secure cookies | `.env.production` | `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`, `SESSION_ENCRYPT=true`, `SESSION_SAME_SITE=lax`, `SESSION_DOMAIN=.goldenpangolin.com`. |
| Debug off | `.env.production` | `APP_DEBUG=false`, `LOG_LEVEL=warning` so stack traces don't leak. |
| CSRF | `app/Http/Middleware/VerifyCsrfToken.php` | No exclusions — every state-changing form goes through CSRF. |
| Removed fingerprinting | `SecurityHeaders` | Strips `X-Powered-By` and `Server`. |

## What you must do by hand (cannot live in code)

1. **Generate a fresh `APP_KEY` in production.** Never reuse the local one.
   ```bash
   php artisan key:generate
   ```
2. **Rotate the demo admin password.** The seeded `samadmin@gmail.com` / `password` account is documented in the README. Either delete it before going live, or:
   ```bash
   php artisan tinker
   >>> $u = App\User::where('email','samadmin@gmail.com')->first();
   >>> $u->password = Hash::make(Str::random(32)); $u->save();
   ```
3. **Stripe keys.** `.env.production` ships placeholders. Replace with live keys only when you're ready to take real payments. Until then, keep the test keys so accidental form submissions don't trigger real charges.
4. **Postmark token + DNS.** See [EMAIL_SETUP.md](EMAIL_SETUP.md). SPF + DKIM + DMARC must be green before the first send or you'll land in spam.
5. **Backups.** DirectAdmin > weekly full backup + nightly MySQL dump. There is no "delete protection" — once the DB is gone, it's gone.
6. **2FA on DirectAdmin + the Postmark + Stripe accounts.** These are higher-value targets than the app itself.

## Known sharp edges to fix later (not blockers for launch)

- **CSP uses `'unsafe-inline'` for scripts and styles.** Required because the layouts have inline `<style>` and `<script>` blocks (theme toggle, AOS init, the new pangolin hint). A future hardening pass should move these to external files and switch CSP to a nonce or hash strategy.
- **`creativeorange/gravatar`** sends visitor emails (hashed) to Gravatar. Disclose in the privacy policy or replace with locally-generated avatars.
- **No rate limiting on the contact form.** Add `throttle:5,1` to the contact POST route before launch if spam becomes a problem.
- **`.env.production` is committed to the repo.** It only contains placeholders now (the previous version had a real-looking password — wiped). Consider git-ignoring it and keeping the template as `.env.production.example` instead.

## After deploy: verify the headers

```bash
curl -sI https://goldenpangolin.com | grep -iE 'strict-transport|x-frame|x-content|referrer|permissions|content-security'
```

You should see all six headers. Also run https://securityheaders.com/?q=goldenpangolin.com — aim for an `A` or better.
