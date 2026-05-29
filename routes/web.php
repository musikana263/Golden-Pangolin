<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\Admin\AccommodationsController as AdminAccommodationsController;
use App\Http\Controllers\Admin\AccommodationTypesController;
use App\Http\Controllers\Admin\ActivitiesController;
use App\Http\Controllers\Admin\PackagesController as AdminPackagesController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\DestinationsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Packages\PostController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TourPackagesController;
use App\Http\Controllers\TravellerTierController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [WelcomeController::class, 'index']);
Route::get('packages/destinations/{destination}', [PostController::class, 'show'])->name('desti.show');

// /admin → /home (or login if not authenticated)
Route::get('/admin', fn () => redirect(auth()->check() ? route('home') : route('login')))->name('admin');

// Admin-only login. Registration is disabled — admins are seeded or promoted via the admin panel.
Auth::routes(['register' => false, 'verify' => true]);

// Override the password-reset GET URL so the email link doesn't trip Chrome's
// phishing heuristic (it flags /password/reset/<token>?email=<address>). The
// new path /account/recover/{token} pairs with `?em=<base64url-email>` set
// by AppServiceProvider's ResetPassword::createUrlUsing(). Same controller,
// same `password.reset` name — Laravel uses the last-registered name for
// route() URL generation, so all existing email templates pick this up.
Route::get('account/recover/{token}', [
    \App\Http\Controllers\Auth\ResetPasswordController::class,
    'showResetForm',
])->middleware('web')->name('password.reset');

// Second factor — email OTP challenge after step-1 password verification.
Route::get('login/verify-otp', [\App\Http\Controllers\Auth\OtpChallengeController::class, 'show'])
    ->middleware(['throttle:30,1'])
    ->name('otp.challenge');
Route::post('login/verify-otp', [\App\Http\Controllers\Auth\OtpChallengeController::class, 'verify'])
    ->middleware(['throttle:10,1'])
    ->name('otp.verify');
Route::post('login/verify-otp/resend', [\App\Http\Controllers\Auth\OtpChallengeController::class, 'resend'])
    ->middleware(['throttle:3,1'])
    ->name('otp.resend');
Route::post('login/verify-otp/cancel', [\App\Http\Controllers\Auth\OtpChallengeController::class, 'cancel'])
    ->name('otp.cancel');

// Any authenticated user can manage site content. The only thing admins can do
// that regular users cannot is add / promote other users (see the admin group
// below).
Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Generic drag-and-drop reorder endpoint. The {resource} is validated
    // server-side against a whitelist in SortController::RESOURCES.
    Route::post('admin/sort/{resource}', [\App\Http\Controllers\Admin\SortController::class, 'update'])
        ->where('resource', 'destinations|activities|packages|accommodations|blogs')
        ->name('admin.sort');

    Route::resource('categories', CategoriesController::class);

    Route::resource('destinations', DestinationsController::class);

    Route::resource('blog', BlogController::class);

    // Unified trash bin — all soft-deleted models in one place.
    Route::get('trash', [\App\Http\Controllers\Admin\TrashController::class, 'index'])->name('trash.index');
    Route::put('trash/{type}/{id}/restore', [\App\Http\Controllers\Admin\TrashController::class, 'restore'])
        ->whereNumber('id')->where('type', 'destinations|packages|accommodations|blog')
        ->name('trash.restore');
    Route::delete('trash/{type}/{id}', [\App\Http\Controllers\Admin\TrashController::class, 'forceDestroy'])
        ->whereNumber('id')->where('type', 'destinations|packages|accommodations|blog')
        ->name('trash.force-destroy');

    // Legacy URLs kept as redirects so any bookmarks still resolve.
    Route::get('trashed-destinations', fn () => redirect()->route('trash.index'))->name('trashed-destinations.index');

    Route::resource('activities', ActivitiesController::class)->except(['show']);

    Route::resource('accommodation-types', AccommodationTypesController::class)
        ->except(['show'])
        ->parameters(['accommodation-types' => 'accommodation_type']);
    Route::resource('accommodations', AdminAccommodationsController::class)
        ->except(['show'])
        ->scoped(['accommodation' => 'id']);

    Route::resource('tour-packages', AdminPackagesController::class)
        ->except(['show'])
        ->parameters(['tour-packages' => 'package'])
        ->scoped(['package' => 'id']);

    // Reviews
    Route::post('destinations/{destination}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // My profile — every authenticated user can view their own profile and
    // change their own password. Editing details (name / email) is gated to
    // admins inside UpdateProfileRequest.
    Route::get('users/profile', [UsersController::class, 'profile'])->name('users.profile');
    Route::put('users/profile', [UsersController::class, 'update'])->name('users.update-profile');
    Route::put('users/profile/password', [UsersController::class, 'changePassword'])->name('users.change-password');
    // Legacy alias so any old admin dropdown links still resolve.
    Route::get('users/profile/edit', fn () => redirect()->route('users.profile'))->name('users.edit-profile');

});

// Wishlist — session-based, no login required (supports packages + activities)
Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('wishlist/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
Route::get('wishlist/whatsapp', [WishlistController::class, 'whatsapp'])->name('wishlist.whatsapp');

Route::post('wishlist/packages/{package:slug}', [WishlistController::class, 'togglePackage'])->name('wishlist.packages.toggle');
Route::delete('wishlist/packages/{package:slug}', [WishlistController::class, 'destroyPackage'])->name('wishlist.packages.destroy');

Route::post('wishlist/activities/{activity}', [WishlistController::class, 'toggleActivity'])->name('wishlist.activities.toggle');
Route::delete('wishlist/activities/{activity}', [WishlistController::class, 'destroyActivity'])->name('wishlist.activities.destroy');

Route::post('wishlist/accommodations/{accommodation:slug}', [WishlistController::class, 'toggleAccommodation'])->name('wishlist.accommodations.toggle');
Route::delete('wishlist/accommodations/{accommodation:slug}', [WishlistController::class, 'destroyAccommodation'])->name('wishlist.accommodations.destroy');

// Admin-only: managing other user accounts. This is the ONLY capability that
// admins have over regular authenticated users.
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('users', [UsersController::class, 'index'])->name('users.index');
    Route::get('users/create', [UsersController::class, 'create'])->name('users.create');
    Route::post('users', [UsersController::class, 'store'])->name('users.store');
    // Edit / update / delete any user (admin power). Static `/users/create`
    // and `/users/profile` routes are above so they win over `{user}` binding.
    Route::get('users/{user}/edit', [UsersController::class, 'editUser'])->name('users.edit');
    Route::put('users/{user}', [UsersController::class, 'updateUser'])->name('users.update');
    Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{user}/make-admin', [UsersController::class, 'makeAdmin'])->name('users.make-admin');

    // Site settings (currently just the WhatsApp number)
    Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
});

// Public accommodation pages
Route::get('/accommodation', [AccommodationController::class, 'index'])->name('accommodation.index');
Route::get('/accommodation/{accommodation:slug}', [AccommodationController::class, 'show'])->name('accommodation.show');
// Permanent redirects so any pre-launch link / bookmark still resolves.
Route::redirect('/stay', '/accommodation', 301);
Route::redirect('/stay/{any}', '/accommodation/{any}', 301)->where('any', '.*');

// Public tour packages
Route::get('/tours', [TourPackagesController::class, 'index'])->name('tours.index');
Route::get('/tours/{package:slug}', [TourPackagesController::class, 'show'])->name('tours.show');

// Note: Laravel's built-in email-verification routes are registered by
// `Auth::routes([..., 'verify' => true])` above and live under /email/verify.
// The custom `email-verification/check/{token}` routes that used to live here
// pointed at methods that don't exist (`getVerification`) — removed.

Route::get('/about', [WelcomeController::class, 'about'])->name('about');

Route::get('/packages', [WelcomeController::class, 'packages'])->name('packages');

Route::get('/news', [WelcomeController::class, 'blog'])->name('blog');

Route::get('/contact', [WelcomeController::class, 'contact'])->name('contact');

// Contact form submit (rate-limited so spam can't flood the DB).
Route::post('/contact', [ContactUsController::class, 'ContactUs'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

// Traveller tier (local / african / international) — guests + auth users alike.
Route::post('/traveller-tier', [TravellerTierController::class, 'update'])
    ->middleware('throttle:30,1')
    ->name('traveller-tier.update');

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
