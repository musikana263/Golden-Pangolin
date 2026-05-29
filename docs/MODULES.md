# Golden Pangolin — Module Rundown

A plain-language tour of every module in the site. Use this as a map when onboarding or when deciding where a new feature should live.

## Permission model in one line

There are two roles — **authenticated user** and **admin**. The *only* thing admins can do that regular users can't is **manage other user accounts** (list users, create users, promote to admin). Everything else — content creation, edits, deletes — is open to any logged-in user.

---

## 1. Public-facing site (no login required)

| Module | URL | What it does | Files |
|---|---|---|---|
| **Home** | `/` | Hero, featured destinations, featured packages, blog teaser | [WelcomeController](app/Http/Controllers/WelcomeController.php), [resources/views/welcome.blade.php](resources/views/welcome.blade.php) |
| **Destinations index** | `/packages` | Grid of all destinations with filters | [WelcomeController::packages](app/Http/Controllers/WelcomeController.php), [resources/views/packages.blade.php](resources/views/packages.blade.php) |
| **Destination detail** | `/packages/destinations/{slug}` | Full destination page — gallery, activities, reviews | [Packages\PostController::show](app/Http/Controllers/Packages/PostController.php), [resources/views/desti/show.blade.php](resources/views/desti/show.blade.php) |
| **Tours** | `/tours`, `/tours/{slug}` | Curated multi-day tour packages | [TourPackagesController](app/Http/Controllers/TourPackagesController.php) |
| **Stay (accommodations)** | `/stay`, `/stay/{slug}` | Lodges, camps, hotels | [AccommodationController](app/Http/Controllers/AccommodationController.php) |
| **Blog** | `/news` | Travel articles | [WelcomeController::blog](app/Http/Controllers/WelcomeController.php), [resources/views/blog.blade.php](resources/views/blog.blade.php) |
| **About** | `/about` | Company background | [WelcomeController::about](app/Http/Controllers/WelcomeController.php), [resources/views/about.blade.php](resources/views/about.blade.php) |
| **Contact** | `/contact` (GET, POST) | Contact form. Rate-limited 5/min, honeypot-protected, validated `name/email/subject/message` with length caps | [ContactUsController](app/Http/Controllers/ContactUsController.php), [resources/views/contact.blade.php](resources/views/contact.blade.php) |
| **Wishlist** | `/wishlist`, `/wishlist/clear`, `/wishlist/whatsapp` | Session-based wishlist of packages/activities. Click "Send via WhatsApp" to compose a list message to the configured number | [WishlistController](app/Http/Controllers/WishlistController.php), [SessionWishlist](app/Services/SessionWishlist.php) |
| **Sitemap** | `/sitemap.xml` | SEO sitemap | [SitemapController](app/Http/Controllers/SitemapController.php) |
| **Robots** | `/robots.txt` | Crawl rules | [public/robots.txt](public/robots.txt) |

### UX touches that span every public page

- **Dark mode by default**, toggle in the navbar. First-visit gets a [pangolin hint animation](resources/views/partials/theme-hint.blade.php) that peeks from the navbar and points at the toggle.
- **Cookie consent banner** ([partials/cookie-consent.blade.php](resources/views/partials/cookie-consent.blade.php)).
- **WhatsApp float button** ([partials/whatsapp-float.blade.php](resources/views/partials/whatsapp-float.blade.php)) — number is in [config/site.php](config/site.php).
- **Wishlist hearts** on each card ([partials/wishlist-button.blade.php](resources/views/partials/wishlist-button.blade.php)).

---

## 2. Authentication

| Module | URL | Notes |
|---|---|---|
| **Login** | `/login` | Email + password. Uses Laravel UI's `AuthenticatesUsers` trait → built-in brute-force throttle (5/min) and session regeneration after success. |
| **Logout** | POST `/logout` | |
| **Password reset request** | `/password/email` | Sends reset link via mailer (Postmark in prod, `log` in dev) |
| **Password reset** | `/password/reset/{token}` | Standard Laravel reset, tokens hashed, 60-min expiry |
| **Email verification** | `/email/verify` | Standard Laravel `MustVerifyEmail`. The `isVerified` route middleware ([app/Http/Middleware/IsVerified.php](app/Http/Middleware/IsVerified.php)) gates routes that need a verified mailbox. |

**Public registration is disabled.** New accounts are created by an admin via the user management module. See [LoginController](app/Http/Controllers/Auth/LoginController.php) for why we no longer override the trait's `login()` method (it's a security regression magnet).

---

## 3. Content management (any authenticated user)

Reachable after login. Every form here is validated by a dedicated `FormRequest` under [app/Http/Requests/](app/Http/Requests/) which enforces length caps and rejects SVG uploads.

| Module | Routes | Controller |
|---|---|---|
| **Dashboard** | `GET /home` | [HomeController](app/Http/Controllers/HomeController.php) — stats overview |
| **Destinations** | `resource('destinations')` + `/trashed-destinations`, `/restore-destinations/{id}` | [DestinationsController](app/Http/Controllers/DestinationsController.php) — soft deletes + restore + force-delete |
| **Blog posts** | `resource('blog')` | [BlogController](app/Http/Controllers/BlogController.php) — soft deletes + restore |
| **Categories** | `resource('categories')` | [CategoriesController](app/Http/Controllers/CategoriesController.php) — refuses delete if any activity references the category |
| **Activities** | `resource('activities')` (except `show`) | [Admin\ActivitiesController](app/Http/Controllers/Admin/ActivitiesController.php) — gallery uploads via Spatie MediaLibrary-style flow |
| **Accommodation types** | `resource('accommodation-types')` | [Admin\AccommodationTypesController](app/Http/Controllers/Admin/AccommodationTypesController.php) |
| **Accommodations** | `resource('accommodations')` | [Admin\AccommodationsController](app/Http/Controllers/Admin/AccommodationsController.php) — multi-image upload, amenity checkboxes from [config/site.php](config/site.php) |
| **Tour packages** | `resource('tour-packages')` | [Admin\PackagesController](app/Http/Controllers/Admin/PackagesController.php) — itinerary builder |
| **Hero package** | `GET /hero-package`, `PUT /hero-package` | [HeroPackageController](app/Http/Controllers/HeroPackageController.php) — single editable hero block on the homepage |
| **Reviews** | `POST /destinations/{dest}/reviews`, `DELETE /reviews/{review}` | [ReviewController](app/Http/Controllers/ReviewController.php) — star + comment. Users can only delete their own; admins can delete any |
| **Own profile** | `GET/PUT /users/profile` | [UsersController::edit / update](app/Http/Controllers/UsersController.php) — name + bio only (never `role`) |

The whole admin chrome (sidebar, topbar, stats cards) lives under [layouts/app.blade.php](resources/views/layouts/app.blade.php) and [public/css/admin.css](public/css/admin.css). Public pages use [layouts/front.blade.php](resources/views/layouts/front.blade.php).

---

## 4. User management (admin only)

This is the *only* admin-gated capability.

| Route | Purpose |
|---|---|
| `GET /users` | List every user (avatar, name, email) |
| `GET /users/create` | Form to add a new user, with an "Grant admin access" checkbox |
| `POST /users` | Create the user. Validated by [StoreUserRequest](app/Http/Requests/Users/StoreUserRequest.php) — strong-password rule, unique email, `role` is set via `User::makeAdmin()` (never mass-assigned) |
| `POST /users/{user}/make-admin` | Promote an existing user to admin |

All gated by the `admin` middleware alias → [VerifyIsAdmin](app/Http/Middleware/VerifyIsAdmin.php), which checks `auth()->user()->isAdmin()`. The `role` column is **not** in `User::$fillable`, so mass-assignment can never silently elevate someone.

---

## 5. Public REST API (`/api/v1`)

Read-only, no auth. All responses are JSON via [API Resources](app/Http/Resources/).

| Endpoint | Notes |
|---|---|
| `GET /api/v1/destinations` | Paginated. Filters: `category`, `search`, `sort_by` (whitelisted: `published_at`, `title`, `created_at`, `id`), `sort_order` (`asc`/`desc`), `per_page` (capped at 50) |
| `GET /api/v1/destinations/{id}` | Single destination + category + tags + reviews |
| `GET /api/v1/destinations/featured` | `?limit=` capped at 50 |
| `GET /api/v1/destinations/search?q=` | `q` required, min 2 chars |
| `GET /api/v1/categories` | All categories with destination counts |
| `GET /api/v1/categories/{id}` | Single category |

Controllers under [app/Http/Controllers/Api/](app/Http/Controllers/Api/). The previous authenticated API surface (`auth:api` with the deprecated Laravel-5 `token` driver) was removed — install `laravel/sanctum` if you ever need authenticated API access.

---

## 6. Services (reusable, dependency-injected)

| Service | Purpose | File |
|---|---|---|
| **ImageService** | `upload / delete / replace / getUrl` against the `public` disk. Single chokepoint so we can swap to S3 later by editing one constant | [app/Services/ImageService.php](app/Services/ImageService.php) |
| **SearchService** | Keyword search, category/tag filtering, sorting. Used by both the public site and the API | [app/Services/SearchService.php](app/Services/SearchService.php) |
| **SessionWishlist** | Wishlist state in the session (so guests can use it without a login). Builds a WhatsApp URL with the saved items | [app/Services/SessionWishlist.php](app/Services/SessionWishlist.php) |

---

## 7. Cross-cutting middleware

| Middleware | What it does | File |
|---|---|---|
| **SecurityHeaders** | HSTS (prod), X-Frame, X-Content-Type, Referrer-Policy, Permissions-Policy, CSP. Runs on every response, strips `X-Powered-By`/`Server` | [SecurityHeaders](app/Http/Middleware/SecurityHeaders.php) |
| **TrustProxies** | Trusts upstream proxies so `secure` cookies + HSTS work behind DirectAdmin/nginx | [TrustProxies](app/Http/Middleware/TrustProxies.php) |
| **VerifyIsAdmin** (`admin`) | Gates the user-management routes | [VerifyIsAdmin](app/Http/Middleware/VerifyIsAdmin.php) |
| **IsVerified** (`isVerified`) | Redirects unverified users to the verification notice | [IsVerified](app/Http/Middleware/IsVerified.php) |
| **VerifyCategoriesCount** (`verifyCategoriesCount`) | Blocks blog-post creation if there are zero categories (UX guard) | [VerifyCategoriesCount](app/Http/Middleware/VerifyCategoriesCount.php) |
| **VerifyCsrfToken** | Default Laravel CSRF, **no exclusions** | [VerifyCsrfToken](app/Http/Middleware/VerifyCsrfToken.php) |

---

## 8. Mailables

| Mailable | When it sends | Template |
|---|---|---|
| `App\Mail\Tourdetails` | (Reserved for future booking confirmations) | [resources/views/Email/](resources/views/Email/) |
| `App\Mail\EmailDemo` | Demo / smoke-test | [Email/demoEmail.blade.php](resources/views/Email/demoEmail.blade.php) |

Plus Laravel's built-in **password reset** and **email verification** notifications. In production these all go via Postmark; locally they write to `storage/logs/laravel.log`.

---

## 9. Database (key tables)

```
users ─┬── reviews ──── destinations ──┬── categories (belongsTo)
       ├── wishlists ── destinations   └── reviews (hasMany)
       └── ... profile, role
blogs ──── categories (belongsTo)
accommodations ──── accommodation_types
tour_packages
activities ──── categories
contact (form submissions)
media (Spatie-style polymorphic for galleries)
```

Migrations live under [database/migrations/](database/migrations/). Seeders under [database/seeders/](database/seeders/) — the admin seeder refuses to run in production without explicit `SEED_ADMIN_EMAIL` + `SEED_ADMIN_PASSWORD` env vars.

---

## 10. Frontend assets

- **Theme**: [public/css/theme.css](public/css/theme.css) — colour tokens, typography, Bootstrap 5 overrides.
- **Dark mode**: [public/css/dark-theme.css](public/css/dark-theme.css) — applied when `<html data-theme="dark">` is set. The pre-paint script in [layouts/front.blade.php](resources/views/layouts/front.blade.php) defaults to dark.
- **Admin chrome**: [public/css/admin.css](public/css/admin.css).
- **Animations**: AOS for scroll-in, custom keyframes for the pangolin glow ([dark-theme.css](public/css/dark-theme.css)) and the theme-hint pangolin ([partials/theme-hint.blade.php](resources/views/partials/theme-hint.blade.php)).

---

## 11. Where to find things

| Looking for | Path |
|---|---|
| New public page | Add route in [routes/web.php](routes/web.php), controller method, blade in [resources/views/](resources/views/) |
| New API endpoint | [routes/api.php](routes/api.php), controller under [app/Http/Controllers/Api/](app/Http/Controllers/Api/), Resource class under [app/Http/Resources/](app/Http/Resources/) |
| New admin-only route | Add inside the `auth+admin` group in [routes/web.php](routes/web.php) |
| New form | FormRequest under [app/Http/Requests/](app/Http/Requests/) with `authorize()` + `rules()` |
| New email | Mailable class under [app/Mail/](app/Mail/), template under [resources/views/Email/](resources/views/Email/) |
| Site-wide settings | [config/site.php](config/site.php) (brand, WhatsApp number, amenities) |
| Security headers / CSP | [SecurityHeaders.php](app/Http/Middleware/SecurityHeaders.php) |
| Deploy steps | [docs/DEPLOY_DIRECTADMIN.md](docs/DEPLOY_DIRECTADMIN.md) |
| Email setup | [docs/EMAIL_SETUP.md](docs/EMAIL_SETUP.md) |
| Security checklist | [docs/SECURITY_CHECKLIST.md](docs/SECURITY_CHECKLIST.md) |
| Pentest findings | [docs/PENTEST_REPORT.md](docs/PENTEST_REPORT.md) |
