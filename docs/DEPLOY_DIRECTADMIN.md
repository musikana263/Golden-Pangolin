# Deploying Golden Pangolin to DirectAdmin (goldenpangolin.com)

Step-by-step. Tested for **Windows hosts** uploading to a DirectAdmin Linux server.

---

## 0. Pre-flight checklist (on your Windows laptop)

- DNS for `goldenpangolin.com` and `www.goldenpangolin.com` points at the DirectAdmin server's IP. Confirm with `nslookup goldenpangolin.com`.
- DirectAdmin login + email working.
- An SSH user enabled on the DirectAdmin box (DirectAdmin > **SSH Keys** > enable for your hosting account). Required for `composer`, `php artisan`, Let's Encrypt issuance.
- **PHP 8.2+** active for the domain (DirectAdmin > **Domain Setup** > **PHP Version Selector** > pick 8.2 or higher).
- PHP extensions enabled (tick in **Select PHP Extensions**): `pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo, curl, gd, intl, zip`.
- One Windows-side tool for uploads:
  - **Git Bash** (recommended) — gives you `ssh` + `scp` + `rsync` like Linux.
  - **WinSCP** (GUI) — drag-and-drop SFTP, easiest if you've never SSH'd.
  - **FileZilla** (GUI) — works the same way.

If you'll be running `git clone` on the server (preferred), all you need is SSH access.

---

## 1. Upload the code

**A. Git pull on the server (preferred):**

```bash
ssh your-user@goldenpangolin.com
cd ~/domains/goldenpangolin.com
git clone https://github.com/<you>/Tours-Travel.git app
```

**B. Or SFTP from Windows:**

1. Open WinSCP or FileZilla.
2. Connect: host `goldenpangolin.com`, user `<your DirectAdmin SSH user>`, port `22`.
3. Upload your local `Tours-Travel/` folder to `~/domains/goldenpangolin.com/app/`.
4. **Exclude** `node_modules/`, `vendor/`, `storage/framework/`, `storage/logs/`, `.git/`, `.env` from the upload (they get rebuilt on the server / contain secrets).

Either way, the project ends up at `~/domains/goldenpangolin.com/app/`.

---

## 2. Point the public docroot at `app/public`

Laravel must serve from `public/`, never from the project root (`.env` lives one level above and must not be web-reachable).

DirectAdmin > **Domain Setup** > `goldenpangolin.com` > set **Document Root** to:

```
public_html/public
```

Then symlink (or replace) `public_html` to your app's `public/`:

```bash
cd ~/domains/goldenpangolin.com
rm -rf public_html
ln -s app/public public_html
```

If your DirectAdmin install disallows symlinks above the docroot, instead copy `app/public/*` into `public_html/` and edit `public_html/index.php` so the `require __DIR__.'/../...'` lines point to `../app/...`.

---

## 3. Install dependencies

```bash
cd ~/domains/goldenpangolin.com/app
composer install --no-dev --optimize-autoloader
```

If npm/node are available **and** you've already built assets locally, you can skip the npm step — Laravel only needs `public/css/*` and `public/js/*` to exist (which they already do in this repo). If you want to rebuild on the server:

```bash
npm ci && npm run prod
```

---

## 4. Configure `.env`

```bash
cp .env.production .env
php artisan key:generate
```

Open `.env` and fill in every `<PLACEHOLDER>`:

| Key | What |
|---|---|
| `APP_URL` | `https://goldenpangolin.com` |
| `APP_KEY` | already generated above |
| `APP_DEBUG` | **`false`** — never expose stack traces |
| `DB_DATABASE / DB_USERNAME / DB_PASSWORD` | from DirectAdmin > **MySQL Management** |
| `MAIL_MAILER` | `postmark` (see [EMAIL_SETUP.md](EMAIL_SETUP.md)) |
| `POSTMARK_TOKEN` | server token from Postmark |
| `MAIL_FROM_ADDRESS` | `hello@goldenpangolin.com` or `info@goldenpangolin.com` |
| `SESSION_SECURE_COOKIE` | `true` (cookies HTTPS-only) |
| `SESSION_DOMAIN` | `.goldenpangolin.com` |
| `OTP_LOGIN_ENABLED` | `true` (2FA email codes on login) |
| `SEED_ADMIN_EMAIL` | `info@goldenpangolin.com` |
| `SEED_ADMIN_PASSWORD` | a strong password — replaces the local `GoldP@2026` |
| `SITE_WHATSAPP_NUMBER` | optional override; admins can edit live from **Admin → Settings** |

---

## 5. Permissions

```bash
cd ~/domains/goldenpangolin.com/app
chmod -R 755 storage bootstrap/cache
```

DirectAdmin runs PHP as your user, so no `chown` is usually needed.

---

## 6. Database

DirectAdmin > **MySQL Management** > create a database and user, grant all on the DB to the user. Plug the credentials into `.env`. Then:

```bash
php artisan migrate --force --seed
php artisan storage:link
```

`migrate` runs all 30+ migrations (incl. site settings, login OTPs, soft-delete bins). `--seed` runs the `UsersTableSeeder` which creates the **Administration** admin (`info@goldenpangolin.com` / your `SEED_ADMIN_PASSWORD`).

`storage:link` symlinks `app/public/storage` → `app/storage/app/public` so uploaded images resolve at `/storage/...`.

---

## 7. Cache for production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

**Re-run this whole block every deploy.** If anything looks stale or you change `.env`, run `php artisan optimize:clear` first.

---

## 8. HTTPS

DirectAdmin > **SSL Certificates** > **Let's Encrypt**:

1. Tick `goldenpangolin.com` and `www.goldenpangolin.com` under **Sub-Domains** (uncheck the wildcard option unless your DNS is locally controlled — see [SSL note in SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md)).
2. Tick **Force SSL with HTTPS Redirect**.
3. Click **Save**.
4. If the Certificates column stays empty, click **TRIGGER RETRY FOR THIS DOMAIN**.

You should now see `https://goldenpangolin.com` redirect from HTTP, with HSTS in the response.

---

## 9. Smoke test

From your laptop:

```bash
curl -sI https://goldenpangolin.com | grep -iE 'strict-transport|x-frame|x-content|referrer|permissions|content-security'
```

You should see all six security headers (the `SecurityHeaders` middleware adds them in production).

Visit in a browser:

- `/` — homepage, hero loads.
- `/packages` `/tours` `/accommodation` `/blog` `/about` `/contact` — all 200, no mixed-content warnings.
- `/login` — sign in with `info@goldenpangolin.com` + `SEED_ADMIN_PASSWORD`.
- After password → redirect to **/login/verify-otp** with a 6-digit code emailed to that address (Postmark dashboard > Activity to confirm).
- After OTP → **/home** dashboard.
- **Admin → Settings** > change WhatsApp number > save > confirm the floating chat button on the public site uses the new number.

---

## 10. Updating later

When you push new code:

```bash
ssh your-user@goldenpangolin.com
cd ~/domains/goldenpangolin.com/app
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## 11. Backups

DirectAdmin > **Create/Restore Backups** > schedule **weekly** full backups. Also add a nightly MySQL dump cron:

```
0 3 * * *  mysqldump -u<user> -p<pass> <db> | gzip > ~/backups/db-$(date +\%F).sql.gz && find ~/backups -name 'db-*.sql.gz' -mtime +14 -delete
```

(Replace `<user> <pass> <db>` with your DB credentials. Put the password in a `~/.my.cnf` if you'd rather not have it in cron.)

---

## Rollback

```bash
cd ~/domains/goldenpangolin.com/app
git reset --hard <previous-good-sha>
composer install --no-dev --optimize-autoloader
php artisan migrate:rollback   # only if a bad migration shipped
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## Notes specific to this build (May 2026 deploy)

- **Default admin**: name `Administration`, email `info@goldenpangolin.com`, password set via `SEED_ADMIN_PASSWORD`. The local-dev password (`GoldP@2026`) is intentionally **not** used in production — set a strong one in `.env`.
- **OTP second factor**: every login emails a 6-digit code via Postmark before the session is established. Set `OTP_LOGIN_ENABLED=false` only if you absolutely need to bypass it temporarily.
- **WhatsApp number** is editable live from **Admin → Settings** (DB-backed, cached). The `SITE_WHATSAPP_NUMBER` env value is only used until something is saved in the database.
- **Traveller tier filter**: visitors are prompted in the cookie banner to pick Local / Regional / International. Activities with no price for the chosen tier are hidden from that visitor — make sure each `Activity` has at least one tier price set in admin, otherwise it won't appear publicly.
- **Hero featured package**: tick the "Show this package on the homepage hero" toggle on any package edit screen. Only one package can hold the slot — promoting one automatically demotes any other.
- **Trash bin**: deletes are soft. Recover from **Admin → Trash** (destinations, packages, accommodations, blog posts).
