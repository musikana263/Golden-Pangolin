<?php

namespace App;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * Mass-assignable attributes.
     *
     * `role` is intentionally NOT here. Promoting a user to admin must go
     * through {@see makeAdmin()} or an explicit assignment by a trusted code
     * path — never via `$user->fill($request->all())`.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'about',
    ];

    /**
     * Attributes hidden from array/JSON serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'role', 'email_verified_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Promote a user to admin. Use this instead of mass-assigning `role`.
     */
    public function makeAdmin(): void
    {
        $this->forceFill(['role' => 'admin'])->save();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistedDestinations()
    {
        return $this->belongsToMany(Destinations::class, 'wishlists', 'user_id', 'destination_id')
            ->withTimestamps();
    }

    public function hasWishlisted(Destinations $destination): bool
    {
        return $this->wishlist()->where('destination_id', $destination->id)->exists();
    }

    public function toggleWishlist(Destinations $destination): bool
    {
        if ($this->hasWishlisted($destination)) {
            $this->wishlist()->where('destination_id', $destination->id)->delete();

            return false;
        }

        $this->wishlist()->create(['destination_id' => $destination->id]);

        return true;
    }
}
