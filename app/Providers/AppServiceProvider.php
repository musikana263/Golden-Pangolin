<?php

namespace App\Providers;

use App\Services\SessionWishlist;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SessionWishlist::class);
        $this->app->scoped(\App\Services\TravellerTier::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Custom URL for password-reset emails. Avoids the literal
        // /password/reset/<token>?email=<address> pattern that Chrome's
        // Safe Browsing flags as phishing. The receiving controller
        // (ResetPasswordController::showResetForm) decodes `em` back to the
        // user's email and pre-fills the form just like the default does.
        \Illuminate\Auth\Notifications\ResetPassword::createUrlUsing(function ($notifiable, $token) {
            $em = rtrim(strtr(base64_encode($notifiable->getEmailForPasswordReset()), '+/', '-_'), '=');
            return url(route('password.reset', ['token' => $token], false)).'?em='.$em;
        });

        View::composer('partials.navbar', function ($view) {
            $wishlist = app(SessionWishlist::class);
            $tier = app(\App\Services\TravellerTier::class);
            $view->with([
                'wishlistCount' => $wishlist->count(),
                'wishlistIds' => [
                    'package' => $wishlist->ids(SessionWishlist::TYPE_PACKAGE),
                    'activity' => $wishlist->ids(SessionWishlist::TYPE_ACTIVITY),
                    'accommodation' => $wishlist->ids(SessionWishlist::TYPE_ACCOMMODATION),
                ],
                'travellerTier' => $tier->current(),
                'travellerTierLabel' => $tier->label(),
                'travellerTierOptions' => \App\Services\TravellerTier::TIERS,
            ]);
        });

        // Make the tier visible to every Blade as $currentTravellerTier
        View::composer('*', function ($view) {
            if (! $view->offsetExists('currentTravellerTier')) {
                $view->with('currentTravellerTier', app(\App\Services\TravellerTier::class)->currentOrDefault());
            }
        });

        View::composer(['welcome', 'tours.*', 'desti.*', 'accommodation.*'], function ($view) {
            $wishlist = app(SessionWishlist::class);
            $view->with('wishlistIds', [
                'package' => $wishlist->ids(SessionWishlist::TYPE_PACKAGE),
                'activity' => $wishlist->ids(SessionWishlist::TYPE_ACTIVITY),
                'accommodation' => $wishlist->ids(SessionWishlist::TYPE_ACCOMMODATION),
            ]);
        });
    }
}
